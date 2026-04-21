<?php

namespace App\Services;

use App\Models\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MessageDeletionService
{
    public function deleteForSender(Message $message, int $senderId): array
    {
        $messages = Message::query()
            ->where('conversation_id', $message->conversation_id)
            ->where('sender_id', $senderId)
            ->when(
                filled($message->batch_uuid),
                fn ($query) => $query->where('batch_uuid', $message->batch_uuid),
                fn ($query) => $query->whereKey($message->id)
            )
            ->orderBy('id')
            ->get();

        $attachmentPaths = $messages->pluck('attachment_path')->filter()->unique()->values()->all();
        $deletedMessageIds = $messages->pluck('id')->values()->all();
        $deletedUnreadCount = $messages->whereNull('read_at')->count();
        $conversationId = $message->conversation_id;
        $batchUuid = $message->batch_uuid;

        DB::transaction(function () use ($deletedMessageIds) {
            if (!empty($deletedMessageIds)) {
                Message::whereIn('id', $deletedMessageIds)->delete();
            }
        });

        if (!empty($attachmentPaths)) {
            Storage::disk('public')->delete($attachmentPaths);
        }

        $latestMessage = Message::where('conversation_id', $conversationId)
            ->latest('created_at')
            ->latest('id')
            ->first();

        return [
            'conversation_id' => $conversationId,
            'deleted_message_ids' => $deletedMessageIds,
            'deleted_message_id' => $message->id,
            'batch_uuid' => $batchUuid,
            'deleted_unread_count' => $deletedUnreadCount,
            'latest_message' => $latestMessage ? [
                'id' => $latestMessage->id,
                'preview' => $latestMessage->body ?: ($latestMessage->attachment_name ?: 'Attachment'),
                'time' => $latestMessage->created_at?->diffForHumans(null, true),
            ] : null,
        ];
    }
}
