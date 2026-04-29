<?php

namespace App\Http\Controllers\Api\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Payment;
use App\Services\ConsultationPaymentService;
use App\Services\WebRtcConfigService;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function __construct(private WebRtcConfigService $webRtcConfig)
    {
    }

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
            'case_document'     => $c->case_document,
            'case_document_url' => $c->case_document_url,
            'can_join_video'    => $c->canJoinVideoCall(),
            'video_room_name'   => $c->type === 'video' ? $c->videoRoomName() : null,
            'video_join_url'    => $c->type === 'video' ? $c->videoJoinUrl() : null,
            'video_signaling_channel' => $c->type === 'video' ? $c->videoPresenceSignalingChannel() : null,
            'video_echo_signaling_channel' => $c->type === 'video' ? $c->videoEchoSignalingChannel() : null,
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
            $paymentService->createBalanceCheckout(
                $balance->loadMissing(['consultation', 'client', 'lawyer']),
                forceRefresh: true
            );
        }

        return response()->json(['message' => 'Consultation marked as completed. The client can now pay the remaining balance.']);
    }

    public function video(Request $request, $id)
    {
        $consultation = Consultation::where('lawyer_id', $request->user()->id)->findOrFail($id);

        if ($consultation->type !== 'video') {
            return response()->json(['message' => 'This consultation is not a video call.'], 422);
        }

        return response()->json([
            'consultation' => $consultation->toApiArray($request->user()->id),
            'room_name' => $consultation->videoRoomName(),
            'join_url' => $consultation->videoJoinUrl(),
            'display_name' => $request->user()->name,
            'can_join' => $consultation->canJoinVideoCall(),
            'join_opens_at' => $consultation->videoJoinOpensAt(),
            'signaling_channel' => $consultation->videoEchoSignalingChannel(),
            'echo_signaling_channel' => $consultation->videoEchoSignalingChannel(),
            'signaling_event' => 'client-signal',
            'broadcast_auth_endpoint' => url('/api/broadcasting/auth'),
            'peer_id' => $consultation->client_id,
            'is_offer_initiator' => true,
            'ice_servers' => $this->webRtcConfig->iceServers(),
        ]);
    }

    public function status(Request $request, $id)
    {
        $consultation = Consultation::where('lawyer_id', $request->user()->id)->findOrFail($id);
        $balance = $consultation->balancePayment()->first();

        return response()->json([
            'consultation_id' => $consultation->id,
            'status' => $consultation->status,
            'balance_payment_id' => $balance?->id,
            'balance_status' => $balance?->status,
            'balance_checkout_url' => null,
        ]);
    }
}
