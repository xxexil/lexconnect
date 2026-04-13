<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Payment;
use App\Services\ConsultationPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ConsultationController extends Controller
{
    public function __construct(private ConsultationPaymentService $paymentService)
    {
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $status = $request->get('status', 'all');

        $query = Consultation::where('client_id', $user->id)
            ->with(['lawyer:id,name,avatar', 'review:id,consultation_id']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $consultations = $query->orderByDesc('scheduled_at')->paginate(20)->through(fn($c) => [
            'id'               => $c->id,
            'code'             => $c->code,
            'scheduled_at'     => $c->scheduled_at,
            'type'             => $c->type,
            'status'           => $c->status,
            'duration_minutes' => $c->duration_minutes,
            'price'            => $c->price,
            'notes'            => $c->notes,
            'lawyer'           => ['id' => $c->lawyer->id, 'name' => $c->lawyer->name, 'avatar_url' => $c->lawyer->avatar_url],
            'has_review'       => $c->review !== null,
        ]);

        return response()->json($consultations);
    }

    public function book(Request $request)
    {
        $request->validate([
            'lawyer_id'        => 'required|exists:users,id',
            'scheduled_at'     => 'required|date',
            'duration_minutes' => 'required|integer|in:30,60,90,120',
            'type'             => 'required|in:video,phone,in-person',
            'notes'            => 'nullable|string|max:1000',
        ]);

        $user = $request->user();

        // Get lawyer's hourly rate
        $lawyerProfile = \App\Models\LawyerProfile::where('user_id', $request->lawyer_id)->firstOrFail();
        $price = ($lawyerProfile->hourly_rate / 60) * $request->duration_minutes;
        $price = round($price, 2);

        $consultation = Consultation::create([
            'code'             => 'LC-' . strtoupper(Str::random(8)),
            'client_id'        => $user->id,
            'lawyer_id'        => $request->lawyer_id,
            'scheduled_at'     => $request->scheduled_at,
            'duration_minutes' => $request->duration_minutes,
            'type'             => $request->type,
            'status'           => 'pending',
            'price'            => $price,
            'notes'            => $request->notes,
        ]);

        // Create payment records
        $downpayment = round($price * 0.5, 2);
        $balance = $price - $downpayment;

        $downpaymentPayment = Payment::create([
            'client_id'       => $user->id,
            'lawyer_id'       => $request->lawyer_id,
            'consultation_id' => $consultation->id,
            'amount'          => $downpayment,
            'status'          => 'pending',
            'type'            => 'downpayment',
            'firm_cut'        => 0,
            'lawyer_net'      => $downpayment,
        ]);

        Payment::create([
            'client_id'       => $user->id,
            'lawyer_id'       => $request->lawyer_id,
            'consultation_id' => $consultation->id,
            'amount'          => $balance,
            'status'          => 'pending',
            'type'            => 'balance',
            'firm_cut'        => 0,
            'lawyer_net'      => $balance,
        ]);

        try {
            $downpaymentPayment = $this->paymentService->createDownpaymentCheckout(
                $downpaymentPayment->loadMissing(['consultation', 'lawyer', 'client']),
                context: 'mobile'
            );
        } catch (\RuntimeException $e) {
            Log::warning('API consultation checkout session failed', [
                'consultation_id' => $consultation->id,
                'payment_id' => $downpaymentPayment->id,
                'client_id' => $user->id,
                'message' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'message'      => 'Consultation booked successfully.',
            'consultation' => $consultation,
            'price'        => $price,
            'downpayment'  => $downpayment,
            'payment'      => [
                'id' => $downpaymentPayment->id,
                'type' => $downpaymentPayment->type,
                'status' => $downpaymentPayment->status,
                'amount' => $downpaymentPayment->amount,
                'checkout_url' => $downpaymentPayment->paymongo_checkout_url,
                'has_checkout_session' => filled($downpaymentPayment->paymongo_session_id),
            ],
        ], 201);
    }

    public function cancel(Request $request, $id)
    {
        $user = $request->user();
        $consultation = Consultation::where('client_id', $user->id)->findOrFail($id);

        if (!in_array($consultation->status, ['pending', 'upcoming'])) {
            return response()->json(['message' => 'This consultation cannot be cancelled.'], 422);
        }

        $consultation->update(['status' => 'cancelled']);

        // Mark payments as refunded
        Payment::where('consultation_id', $consultation->id)->update(['status' => 'refunded']);

        return response()->json(['message' => 'Consultation cancelled successfully.']);
    }
}
