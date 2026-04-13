<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Payment;
use App\Services\ConsultationPaymentService;
use Illuminate\Support\Facades\Auth;

class VideoCallController extends Controller
{
    public function join(Consultation $consultation)
    {
        $user = Auth::user();

        if ($consultation->client_id !== $user->id && $consultation->lawyer_id !== $user->id) {
            abort(403, 'You are not part of this consultation.');
        }

        if ($consultation->type !== 'video') {
            return redirect()->back()->with('error', 'This consultation is not a video call.');
        }

        if ($consultation->status !== 'upcoming') {
            return redirect()->back()->with('error', 'The video call is only available for confirmed upcoming consultations.');
        }

        if (! $consultation->canJoinVideoCall()) {
            return redirect()->back()->with('error', 'The video call will be available at ' . $consultation->videoJoinOpensAt()->format('M d, g:i A') . ', 5 minutes before the scheduled time.');
        }

        $roomName    = 'LexConnect-' . $consultation->code;
        $displayName = $user->name;
        $returnRoute = $user->role === 'lawyer' ? route('lawyer.consultations') : route('consultations');

        return view('video-call', compact('consultation', 'roomName', 'displayName', 'returnRoute'));
    }

    public function end(Consultation $consultation, ConsultationPaymentService $paymentService)
    {
        $user = Auth::user();

        if ($consultation->lawyer_id !== $user->id) {
            abort(403);
        }

        if ($consultation->status === 'upcoming') {
            $consultation->update(['status' => 'completed']);

            $balance = Payment::where('consultation_id', $consultation->id)
                ->where('type', 'balance')
                ->where('status', 'pending')
                ->first();

            if ($balance) {
                $paymentService->createBalanceCheckout($balance->loadMissing(['consultation', 'client', 'lawyer']));
            }
        }

        return redirect()->route('lawyer.consultations')->with('success', 'Session ended and the final balance request has been prepared for the client.');
    }
}
