<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Message $message)
    {
        \Log::info('MessageSent event constructor called for message ID: ' . $this->message->id);
        
        try {
            $this->message->load('sender');
            \Log::info('Sender loaded successfully: ' . $this->message->sender->name);
        } catch (\Exception $e) {
            \Log::error('Error loading sender: ' . $e->getMessage());
        }
    }

    public function broadcastOn(): array
    {
        \Log::info('MessageSent broadcastOn called for conversation: ' . $this->message->conversation_id);
        return [
            new PrivateChannel('conversation.' . $this->message->conversation_id),
        ];
    }

    public function broadcastAs(): string
    {
        \Log::info('MessageSent broadcastAs called - returning: MessageSent');
        return 'MessageSent';
    }

    public function broadcastWith(): array
    {
        \Log::info('MessageSent broadcastWith called');
        
        try {
            $data = [
                'id'              => $this->message->id,
                'conversation_id' => $this->message->conversation_id,
                'sender_id'       => $this->message->sender_id,
                'body'            => e($this->message->body),
                'time'            => $this->message->created_at->format('g:i A'),
                'sender_name'     => $this->message->sender->name,
                'attachment_path' => $this->message->attachment_path ? asset('storage/' . $this->message->attachment_path) : null,
                'attachment_name' => $this->message->attachment_name,
                'attachment_type' => $this->message->attachment_type,
                'batch_uuid'      => $this->message->batch_uuid,
            ];
            
            \Log::info('MessageSent data prepared: ' . json_encode($data));
            return $data;
        } catch (\Exception $e) {
            \Log::error('Error preparing MessageSent data: ' . $e->getMessage());
            return [];
        }
    }
}
