<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Payment;
use App\Services\ConsultationPaymentService;
use App\Services\WebRtcConfigService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ConsultationController extends Controller
{
    public function __construct(
        private ConsultationPaymentService $paymentService,
        private WebRtcConfigService $webRtcConfig,
    )
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
            'case_document'     => $c->case_document,
            'case_document_url' => $c->case_document_url,
            'can_join_video'    => $this->clientCanJoinVideo($c),
            'video_room_name'   => $c->type === 'video' ? $c->videoRoomName() : null,
            'video_join_url'    => $c->type === 'video' ? $c->videoJoinUrl() : null,
            'video_signaling_channel' => $c->type === 'video' ? $c->videoPresenceSignalingChannel() : null,
            'video_echo_signaling_channel' => $c->type === 'video' ? $c->videoEchoSignalingChannel() : null,
            'lawyer'           => ['id' => $c->lawyer->id, 'name' => $c->lawyer->name, 'avatar_url' => $c->lawyer->avatar_url],
            'has_review'       => $c->review !== null,
        ]);

        return response()->json($consultations);
    }

    public function book(Request $request)
    {
        $request->validate([
            'lawyer_id'        => 'required|exists:users,id',
            'scheduled_at'     => 'required|date|after:now',
            'duration_minutes' => 'required|integer|in:30,60,90,120',
            'type'             => 'required|in:video,phone,in-person',
            'notes'            => 'nullable|string|max:1000',
            'case_document'     => 'nullable|file|max:20480|mimes:jpg,jpeg,png,webp,heic,heif,pdf,doc,docx,txt',
        ]);

        $user = $request->user();

        // Get lawyer's hourly rate
        $lawyerProfile = \App\Models\LawyerProfile::where('user_id', $request->lawyer_id)->firstOrFail();
        $price = ($lawyerProfile->hourly_rate / 60) * $request->duration_minutes;
        $price = round($price, 2);

        $docPath = null;
        if ($request->hasFile('case_document')) {
            $docPath = $request->file('case_document')->store('case-documents', 'local');
        }

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
            'case_document'    => $docPath,
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
            'consultation' => $consultation->toApiArray($user->id),
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

    public function video(Request $request, $id)
    {
        $consultation = Consultation::where('client_id', $request->user()->id)->findOrFail($id);

        if ($consultation->type !== 'video') {
            return response()->json(['message' => 'This consultation is not a video call.'], 422);
        }

        return response()->json([
            'consultation' => $consultation->toApiArray($request->user()->id),
            'room_name' => $consultation->videoRoomName(),
            'join_url' => $consultation->videoJoinUrl(),
            'display_name' => $request->user()->name,
            'can_join' => $this->clientCanJoinVideo($consultation),
            'join_opens_at' => $consultation->videoJoinOpensAt(),
            'signaling_channel' => $consultation->videoEchoSignalingChannel(),
            'echo_signaling_channel' => $consultation->videoEchoSignalingChannel(),
            'signaling_event' => 'client-signal',
            'broadcast_auth_endpoint' => url('/api/broadcasting/auth'),
            'peer_id' => $consultation->lawyer_id,
            'is_offer_initiator' => false,
            'ice_servers' => $this->webRtcConfig->iceServers(),
        ]);
    }

    public function status(Request $request, $id)
    {
        $consultation = Consultation::where('client_id', $request->user()->id)->findOrFail($id);
        $balance = $consultation->balancePayment()->first();
        $balanceUrl = null;

        if ($balance && $balance->status === 'pending') {
            $balanceUrl = route('payment.balance.start', $balance);
        }

        return response()->json([
            'consultation_id' => $consultation->id,
            'status' => $consultation->status,
            'balance_payment_id' => $balance?->id,
            'balance_status' => $balance?->status,
            'balance_checkout_url' => $balanceUrl,
            'lawyer_in_video_call' => Cache::has($this->presenceKey($consultation, (int) $consultation->lawyer_id)),
        ]);
    }

    private function clientCanJoinVideo(Consultation $consultation): bool
    {
        return $consultation->canJoinVideoCall()
            || (
                $consultation->type === 'video'
                && $consultation->status === 'upcoming'
                && Cache::has($this->presenceKey($consultation, (int) $consultation->lawyer_id))
            );
    }

    private function presenceKey(Consultation $consultation, int $userId): string
    {
        return "video-call:{$consultation->id}:presence:{$userId}";
    }
}
