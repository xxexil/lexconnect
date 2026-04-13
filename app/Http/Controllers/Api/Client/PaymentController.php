<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\ConsultationPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(private ConsultationPaymentService $paymentService)
    {
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $payments = Payment::where('client_id', $user->id)
            ->with(['consultation:id,code,scheduled_at,type', 'lawyer:id,name'])
            ->orderByDesc('created_at')
            ->paginate(20)
            ->through(fn($p) => $this->serializePayment($p));

        $stats = [
            'total_paid'    => Payment::where('client_id', $user->id)->where('status', 'paid')->sum('amount'),
            'pending'       => Payment::where('client_id', $user->id)->where('status', 'pending')->sum('amount'),
        ];

        return response()->json(['payments' => $payments, 'stats' => $stats]);
    }

    public function resume(Request $request, $id)
    {
        $payment = Payment::where('client_id', $request->user()->id)
            ->with(['consultation:id,code,scheduled_at,type', 'lawyer:id,name', 'client:id,name,email'])
            ->findOrFail($id);

        if ($payment->status !== 'pending') {
            return response()->json([
                'message' => 'Payment is no longer pending.',
                'payment' => $this->serializePayment($payment),
            ]);
        }

        try {
            $payment = $this->paymentService->createCheckoutForPayment($payment, forceRefresh: true, context: 'mobile');
        } catch (\RuntimeException $e) {
            Log::warning('API payment resume failed', [
                'payment_id' => $payment->id,
                'client_id' => $request->user()->id,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Unable to resume checkout right now.',
                'payment' => $this->serializePayment($payment),
                'error' => 'checkout_unavailable',
            ], 422);
        }

        return response()->json([
            'message' => $payment->status === 'pending'
                ? 'Checkout session ready.'
                : 'Payment status updated.',
            'payment' => $this->serializePayment($payment),
        ]);
    }

    public function status(Request $request, $id)
    {
        $payment = Payment::where('client_id', $request->user()->id)
            ->with(['consultation:id,code,scheduled_at,type', 'lawyer:id,name', 'client:id,name,email'])
            ->findOrFail($id);

        if ($payment->status === 'pending' && $payment->paymongo_session_id) {
            try {
                $payment = $this->paymentService->syncPaymentStatus($payment);
            } catch (\RuntimeException $e) {
                Log::warning('API payment status check failed', [
                    'payment_id' => $payment->id,
                    'client_id' => $request->user()->id,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'payment' => $this->serializePayment($payment),
        ]);
    }

    private function serializePayment(Payment $payment): array
    {
        return [
            'id' => $payment->id,
            'consultation_id' => $payment->consultation_id,
            'amount' => $payment->amount,
            'status' => $payment->status,
            'type' => $payment->type,
            'created_at' => $payment->created_at,
            'checkout_url' => $payment->paymongo_checkout_url,
            'has_checkout_session' => filled($payment->paymongo_session_id),
            'is_paid' => in_array($payment->status, ['downpayment_paid', 'paid'], true),
            'consultation' => $payment->consultation ? [
                'id' => $payment->consultation_id,
                'code' => $payment->consultation->code,
                'scheduled_at' => $payment->consultation->scheduled_at,
                'type' => $payment->consultation->type,
            ] : null,
            'lawyer_name' => $payment->lawyer?->name,
        ];
    }
}
