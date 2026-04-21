<?php

namespace App\Services;

use App\Models\Message;

class MessageUpdateService
{
    public function updateForSender(Message $message, int $senderId, string $body): array
    {
        abort_unless($message->sender_id === $senderId, 403);

        $message->body = $body;
        $message->save();
        $message->refresh();

        $latestMessage = Message::where('conversation_id', $message->conversation_id)
            ->latest('created_at')
            ->latest('id')
            ->first();

        return [
            'conversation_id' => $message->conversation_id,
            'message_id' => $message->id,
            'body' => $message->body,
            'time' => $message->created_at?->format('g:i A'),
            'latest_message' => $latestMessage ? [
                'id' => $latestMessage->id,
                'preview' => $latestMessage->body ?: ($latestMessage->attachment_name ?: 'Attachment'),
                'time' => $latestMessage->created_at?->diffForHumans(null, true),
            ] : null,
        ];
    }
}
