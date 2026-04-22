<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MessageAttachmentController extends Controller
{
    public function show(Request $request, Message $message)
    {
        abort_unless($message->attachment_path, 404);

        $user = $request->user();

        $conversation = Conversation::query()
            ->whereKey($message->conversation_id)
            ->where(function ($query) use ($user) {
                $query->where('client_id', $user->id)
                    ->orWhere('lawyer_id', $user->id);
            })
            ->firstOrFail();

        unset($conversation);

        $disk = $message->attachmentDisk();
        abort_unless(Storage::disk($disk)->exists($message->attachment_path), 404);

        $mime = $message->attachment_mime ?: Storage::disk($disk)->mimeType($message->attachment_path) ?: 'application/octet-stream';

        return Storage::disk($disk)->response(
            $message->attachment_path,
            $message->attachment_name ?: basename($message->attachment_path),
            [
                'Content-Type' => $mime,
                'Content-Disposition' => 'inline; filename="' . addslashes($message->attachment_name ?: basename($message->attachment_path)) . '"',
            ]
        );
    }
}
