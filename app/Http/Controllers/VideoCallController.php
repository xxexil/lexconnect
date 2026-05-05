<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Services\WebRtcConfigService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class VideoCallController extends Controller
{
    public function join(Consultation $consultation, WebRtcConfigService $webRtcConfig)
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

        $displayName = $user->name;
        $returnRoute = $user->role === 'lawyer' ? route('lawyer.consultations') : route('consultations');
        $peer = $consultation->client_id === $user->id
            ? $consultation->lawyer()->select('id', 'name', 'role')->first()
            : $consultation->client()->select('id', 'name', 'role')->first();

        return view('video-call', [
            'consultation' => $consultation,
            'displayName' => $displayName,
            'returnRoute' => $returnRoute,
            'iceServers' => $webRtcConfig->iceServers(),
            'currentUserId' => $user->id,
            'currentUserRole' => $user->role,
            'peerId' => $peer?->id,
            'peerName' => $peer?->name,
            'echoSignalingChannel' => $consultation->videoEchoSignalingChannel(),
            'presenceSignalingChannel' => $consultation->videoPresenceSignalingChannel(),
        ]);
    }

    public function end(Consultation $consultation)
    {
        $user = Auth::user();

        if ($consultation->lawyer_id !== $user->id) {
            abort(403);
        }

        if ($consultation->status === 'upcoming') {
            $consultation->update(['status' => 'completed']);
        }

        $balance = $consultation->balancePayment()->first();
        $balanceUrl = $balance && $balance->status === 'pending'
            ? route('payment.balance.start', $balance)
            : null;

        $this->queueSignalForUser($consultation, (int) $consultation->client_id, [
            'type' => 'consultation-ended',
            'consultationId' => $consultation->id,
            'fromUserId' => (int) $user->id,
            'fromRole' => $user->role,
            'targetUserId' => (int) $consultation->client_id,
            'balance_checkout_url' => $balanceUrl,
            'signalId' => (string) str()->uuid(),
            'sentAt' => (int) round(microtime(true) * 1000),
        ]);

        if (request()->expectsJson()) {
            return response()->json([
                'ended' => true,
                'redirect_url' => route('lawyer.consultations'),
            ]);
        }

        return redirect()->route('lawyer.consultations')->with('success', 'Session ended. The client can now proceed to the remaining balance payment.');
    }

    public function status(Consultation $consultation)
    {
        $user = Auth::user();

        if ($consultation->client_id !== $user->id && $consultation->lawyer_id !== $user->id) {
            abort(403, 'You are not part of this consultation.');
        }

        $balance = $consultation->balancePayment()->first();
        $balanceUrl = null;

        if ($user->id === $consultation->client_id && $balance && $balance->status === 'pending') {
            $balanceUrl = route('payment.balance.start', $balance);
        }

        return response()->json([
            'consultation_id' => $consultation->id,
            'status' => $consultation->status,
            'balance_payment_id' => $balance?->id,
            'balance_status' => $balance?->status,
            'balance_checkout_url' => $balanceUrl,
        ]);
    }

    public function heartbeat(Consultation $consultation)
    {
        $user = Auth::user();

        if ($consultation->client_id !== $user->id && $consultation->lawyer_id !== $user->id) {
            abort(403, 'You are not part of this consultation.');
        }

        $peerId = (int) ($consultation->client_id === $user->id
            ? $consultation->lawyer_id
            : $consultation->client_id);

        Cache::put($this->presenceKey($consultation, (int) $user->id), now()->timestamp, now()->addSeconds(12));

        return response()->json([
            'peer_online' => Cache::has($this->presenceKey($consultation, $peerId)),
            'peer_id' => $peerId,
        ]);
    }

    public function signal(Request $request, Consultation $consultation)
    {
        $user = Auth::user();

        if ($consultation->client_id !== $user->id && $consultation->lawyer_id !== $user->id) {
            abort(403, 'You are not part of this consultation.');
        }

        $peerId = (int) ($consultation->client_id === $user->id
            ? $consultation->lawyer_id
            : $consultation->client_id);

        $payload = $request->validate([
            'type' => ['required', 'string', 'in:peer-ready,hangup,offer,answer,ice-candidate,screen-share-start,screen-share-stop,audio-muted,consultation-ended'],
            'sdp' => ['nullable', 'array'],
            'candidate' => ['nullable', 'array'],
            'muted' => ['nullable', 'boolean'],
            'signalId' => ['nullable', 'string'],
            'sentAt' => ['nullable', 'integer'],
        ]);

        $payload = array_merge($payload, [
            'signalId' => $payload['signalId'] ?? (string) str()->uuid(),
            'consultationId' => $consultation->id,
            'fromUserId' => (int) $user->id,
            'fromRole' => $user->role,
            'targetUserId' => $peerId,
            'sentAt' => $payload['sentAt'] ?? (int) round(microtime(true) * 1000),
        ]);

        $key = $this->signalKey($consultation, $peerId);
        $signals = Cache::get($key, []);
        $signals[] = $payload;
        $signals = array_slice($signals, -80);

        Cache::put($key, $signals, now()->addMinutes(10));

        return response()->json(['queued' => true]);
    }

    public function signals(Consultation $consultation)
    {
        $user = Auth::user();

        if ($consultation->client_id !== $user->id && $consultation->lawyer_id !== $user->id) {
            abort(403, 'You are not part of this consultation.');
        }

        $key = $this->signalKey($consultation, (int) $user->id);

        return response()->json([
            'signals' => Cache::pull($key, []),
        ]);
    }

    private function presenceKey(Consultation $consultation, int $userId): string
    {
        return "video-call:{$consultation->id}:presence:{$userId}";
    }

    private function signalKey(Consultation $consultation, int $userId): string
    {
        return "video-call:{$consultation->id}:signals:{$userId}";
    }

    private function queueSignalForUser(Consultation $consultation, int $userId, array $payload): void
    {
        $key = $this->signalKey($consultation, $userId);
        $signals = Cache::get($key, []);
        $signals[] = $payload;
        $signals = array_slice($signals, -80);

        Cache::put($key, $signals, now()->addMinutes(10));
    }
}
