<?php

namespace App\Events;

use App\Models\Consultation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConsultationStatusChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Consultation $consultation)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('App.Models.User.' . $this->consultation->client_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'ConsultationStatusChanged';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->consultation->id,
            'status' => $this->consultation->status,
            'client_id' => $this->consultation->client_id,
            'lawyer_id' => $this->consultation->lawyer_id,
            'code' => $this->consultation->code,
        ];
    }
}
