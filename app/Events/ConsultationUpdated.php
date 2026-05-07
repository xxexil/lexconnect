<?php

namespace App\Events;

use App\Models\Consultation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConsultationUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Consultation $consultation, public array $changes = [])
    {
    }

    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('user.' . $this->consultation->client_id . '.consultations'),
            new PrivateChannel('user.' . $this->consultation->lawyer_id . '.consultations'),
        ];

        $firmId = $this->consultation->lawyer?->lawyerProfile?->lawFirm?->user_id;
        if ($firmId) {
            $channels[] = new PrivateChannel('user.' . $firmId . '.consultations');
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'ConsultationUpdated';
    }

    public function broadcastWith(): array
    {
        $this->consultation->loadMissing(['client:id,name', 'lawyer:id,name', 'lawyer.lawyerProfile.lawFirm', 'balancePayment']);
        $firmUserId = $this->consultation->lawyer?->lawyerProfile?->lawFirm?->user_id;

        return [
            'consultation' => [
                'id' => $this->consultation->id,
                'code' => $this->consultation->code,
                'status' => $this->consultation->status,
                'type' => $this->consultation->type,
                'scheduled_at' => optional($this->consultation->scheduled_at)->toISOString(),
                'duration_minutes' => $this->consultation->duration_minutes,
                'client_id' => $this->consultation->client_id,
                'lawyer_id' => $this->consultation->lawyer_id,
                'law_firm_id' => $firmUserId,
                'client_name' => $this->consultation->client?->name,
                'lawyer_name' => $this->consultation->lawyer?->name,
                'balance_payment_id' => $this->consultation->balancePayment?->id,
                'balance_payment_status' => $this->consultation->balancePayment?->status,
            ],
            'changes' => $this->changes,
        ];
    }
}
