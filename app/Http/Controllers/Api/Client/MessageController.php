<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $conversations = Conversation::where('client_id', $user->id)
            ->orWhere('lawyer_id', $user->id)
            ->with([
                'client:id,name,avatar',
                'lawyer:id,name,avatar',
                'latestMessage',
            ])
            ->get()
            ->map(function ($c) use ($user) {
                $other = $user->id === $c->client_id ? $c->lawyer : $c->client;
                $unread = $c->messages()->whereNull('read_at')->where('sender_id', '!=', $user->id)->count();
                return [
                    'id'          => $c->id,
                    'other_user'  => ['id' => $other->id, 'name' => $other->name, 'avatar_url' => $other->avatar_url],
                    'last_message'=> $c->latestMessage?->body ?: ($c->latestMessage?->attachment_name ? 'Attachment' : null),
                    'last_attachment_type' => $c->latestMessage?->attachment_type,
                    'last_at'     => $c->latestMessage?->created_at,
                    'unread'      => $unread,
                ];
            });

        return response()->json($conversations);
    }

    public function show(Request $request, $conversationId)
    {
        $user = $request->user();
        $conversation = Conversation::where(function ($q) use ($user) {
            $q->where('client_id', $user->id)->orWhere('lawyer_id', $user->id);
        })->findOrFail($conversationId);

        // Mark messages as read
        $conversation->messages()->whereNull('read_at')->where('sender_id', '!=', $user->id)->update(['read_at' => now()]);

        $messages = $conversation->messages()
            ->with('sender:id,name,avatar')
            ->orderBy('created_at')
            ->get()
            ->map(fn (Message $message) => $message->toApiArray($user->id));

        $other = $user->id === $conversation->client_id ? $conversation->lawyer : $conversation->client;

        return response()->json([
            'conversation_id' => $conversation->id,
            'other_user'      => ['id' => $other->id, 'name' => $other->name, 'avatar_url' => $other->avatar_url],
            'messages'        => $messages,
        ]);
    }

    public function start(Request $request)
    {
        $request->validate(['lawyer_id' => 'required|exists:users,id']);

        $user = $request->user();

        $conversation = Conversation::firstOrCreate(
            ['client_id' => $user->id, 'lawyer_id' => $request->lawyer_id]
        );

        return response()->json(['conversation_id' => $conversation->id]);
    }

    public function send(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'body'            => 'nullable|string|max:5000',
            'attachment'      => 'nullable|file|max:20480|mimes:jpeg,png,jpg,gif,webp,heic,heif,pdf,doc,docx,txt,mp3,wav,m4a,aac,ogg,oga,webm',
            'attachments'     => 'nullable|array',
            'attachments.*'   => 'file|max:20480|mimes:jpeg,png,jpg,gif,webp,heic,heif,pdf,doc,docx,txt,mp3,wav,m4a,aac,ogg,oga,webm',
        ]);

        $user = $request->user();
        $conversation = Conversation::where(function ($q) use ($user) {
            $q->where('client_id', $user->id)->orWhere('lawyer_id', $user->id);
        })->findOrFail($request->conversation_id);

        $attachments = [];
        if ($request->hasFile('attachment')) {
            $attachments[] = $request->file('attachment');
        }
        if ($request->hasFile('attachments')) {
            $attachments = array_merge($attachments, $request->file('attachments'));
        }

        if (!$request->filled('body') && count($attachments) === 0) {
            return response()->json(['message' => 'Message or attachment required.'], 422);
        }

        $messages = collect();
        $batchUuid = count($attachments) > 1 ? (string) Str::uuid() : null;

        if (count($attachments) === 0) {
            $messages->push($conversation->messages()->create([
                'sender_id' => $user->id,
                'body'      => $request->body ?? '',
            ]));
        } else {
            foreach ($attachments as $index => $file) {
                $path = $file->store('message-attachments', 'public');
                $messages->push($conversation->messages()->create([
                    'sender_id'       => $user->id,
                    'body'            => $index === 0 ? ($request->body ?? '') : '',
                    'attachment_path' => $path,
                    'attachment_name' => $file->getClientOriginalName(),
                    'attachment_type' => Message::attachmentTypeForMime($file->getMimeType()),
                    'batch_uuid'      => $batchUuid,
                ]));
            }
        }

        $messages->each(function (Message $message) {
            try {
                broadcast(new MessageSent($message->loadMissing('sender')))->toOthers();
            } catch (\Throwable $e) {
                \Log::error('Broadcasting failed for message ID: ' . $message->id . ' - Error: ' . $e->getMessage());
            }
        });

        $payload = $messages
            ->map(fn (Message $message) => $message->loadMissing('sender')->toApiArray($user->id))
            ->values();
        $first = $payload->first();

        return response()->json(array_merge($first ?? [], [
            'message' => $first,
            'messages' => $payload,
        ]));
    }
}
