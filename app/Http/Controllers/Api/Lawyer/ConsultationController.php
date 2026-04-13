<?php

namespace App\Http\Controllers\Api\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Payment;
use App\Services\ConsultationPaymentService;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'lawyer') {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $status = $request->get('status', 'pending');
        $query = Consultation::where('lawyer_id', $user->id)->with('client:id,name,avatar');

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
            'client'           => ['id' => $c->client->id, 'name' => $c->client->name, 'avatar_url' => $c->client->avatar_url],
        ]);

        return response()->json($consultations);
    }

    public function accept(Request $request, $id)
    {
        $user = $request->user();
        $consultation = Consultation::where('lawyer_id', $user->id)->where('status', 'pending')->findOrFail($id);
        $consultation->update(['status' => 'upcoming']);
        return response()->json(['message' => 'Consultation accepted.']);
    }

    public function decline(Request $request, $id)
    {
        $user = $request->user();
        $consultation = Consultation::where('lawyer_id', $user->id)->where('status', 'pending')->findOrFail($id);
        $consultation->update(['status' => 'cancelled']);
        Payment::where('consultation_id', $consultation->id)->update(['status' => 'refunded']);
        return response()->json(['message' => 'Consultation declined.']);
    }

    public function complete(Request $request, $id, ConsultationPaymentService $paymentService)
    {
        $user = $request->user();
        $consultation = Consultation::where('lawyer_id', $user->id)->where('status', 'upcoming')->findOrFail($id);
        $consultation->update(['status' => 'completed']);

        $balance = Payment::where('consultation_id', $consultation->id)->where('type', 'balance')->first();
        if ($balance && $balance->status === 'pending') {
            $paymentService->createBalanceCheckout($balance->loadMissing(['consultation', 'client', 'lawyer']));
        }

        return response()->json(['message' => 'Consultation marked as completed. The client can now pay the remaining balance.']);
    }
}
