<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Payment;
use App\Models\User;
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

        $scheduledAt = \Carbon\Carbon::parse($consultation->scheduled_at);
        if (now()->lt($scheduledAt->copy()->subMinutes(5))) {
            return redirect()->back()->with('error', 'The video call will be available 5 minutes before the scheduled time (' . $scheduledAt->format('M d, g:i A') . ').');
        }

        $roomName    = 'LexConnect-' . $consultation->code;
        $displayName = $user->name;
        $returnRoute = $user->role === 'lawyer' ? route('lawyer.consultations') : route('consultations');

        return view('video-call', compact('consultation', 'roomName', 'displayName', 'returnRoute'));
    }

    public function end(Consultation $consultation)
    {
        $user = Auth::user();

        if ($consultation->lawyer_id !== $user->id) {
            abort(403);
        }

        if ($consultation->status === 'upcoming') {
            $consultation->update(['status' => 'completed']);

            $lawyer = User::with('lawyerProfile')->find($user->id);
            $inFirm = $lawyer->lawyerProfile && $lawyer->lawyerProfile->law_firm_id;

            $balance = Payment::where('consultation_id', $consultation->id)
                ->where('type', 'balance')
                ->where('status', 'pending')
                ->first();

            if ($balance) {
                $firmCut   = $inFirm ? round($balance->amount * 0.05, 2) : 0;
                $lawyerNet = round($balance->amount - $firmCut, 2);

                $balance->update([
                    'status'     => 'paid',
                    'firm_cut'   => $firmCut,
                    'lawyer_net' => $lawyerNet,
                ]);
            }
        }

        return redirect()->route('lawyer.consultations')->with('success', 'Session ended and marked as completed.');
    }
}
