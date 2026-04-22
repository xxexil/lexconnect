<?php

namespace App\Http\Controllers\Api\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Events\MessageDeleted;
use App\Events\MessageSent;
use App\Events\MessageUpdated;
use App\Services\MessageDeletionService;
use App\Services\MessageUpdateService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $conversations = Conversation::where('lawyer_id', $user->id)
            ->with(['client:id,name,avatar', 'latestMessage'])
            ->get()
            ->map(function ($c) use ($user) {
                $unread = $c->messages()->whereNull('read_at')->where('sender_id', '!=', $user->id)->count();
                return [
                    'id'          => $c->id,
                    'call_room_name' => $c->callRoomName(),
                    'other_user'  => ['id' => $c->client->id, 'name' => $c->client->name, 'avatar_url' => $c->client->avatar_url],
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
        $conversation = Conversation::where('lawyer_id', $user->id)->findOrFail($conversationId);

        $conversation->messages()->whereNull('read_at')->where('sender_id', '!=', $user->id)->update(['read_at' => now()]);

        $messages = $conversation->messages()
            ->with('sender:id,name,avatar')
            ->orderBy('created_at')
            ->get()
            ->map(fn (Message $message) => $message->toApiArray($user->id));

        return response()->json([
            'conversation_id' => $conversation->id,
            'call_room_name'  => $conversation->callRoomName(),
            'other_user'      => [
                'id' => $conversation->client->id,
                'name' => $conversation->client->name,
                'avatar_url' => $conversation->client->avatar_url,
            ],
            'messages'        => $messages,
        ]);
    }

    public function send(Request $request)
    {
        $request->validate(
            array_merge(['conversation_id' => 'required|exists:conversations,id'], Message::messageValidationRules()),
            Message::messageValidationMessages()
        );

        $user = $request->user();
        $conversation = Conversation::where('lawyer_id', $user->id)->findOrFail($request->conversation_id);

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
                $messages->push($conversation->messages()->create([
                    'sender_id'       => $user->id,
                    'body'            => $index === 0 ? ($request->body ?? '') : '',
                    ...Message::storeUploadedAttachment($file),
                    'batch_uuid'      => $batchUuid,
                ]));
            }
        }

        $messages->each(function (Message $message) {
            try {
                broadcast(new MessageSent($message->loadMissing('sender')))->toOthers();
            } catch (\Throwable $e) {
                \Log::error('Broadcasting failed: ' . $e->getMessage());
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

    public function sendCallInvite(Request $request, int $conversationId)
    {
        $request->validate([
            'title' => 'nullable|string|max:120',
        ]);

        $user = $request->user();
        $conversation = Conversation::where('lawyer_id', $user->id)->findOrFail($conversationId);

        $message = $conversation->messages()->create([
            'sender_id' => $user->id,
            'body' => $conversation->directCallInvitePayload($user->name, $request->input('title', 'Video Call')),
        ]);

        broadcast(new MessageSent($message->loadMissing('sender')))->toOthers();

        return response()->json([
            'room_name' => $conversation->callRoomName(),
            'invite' => $message->toApiArray($user->id),
        ], 201);
    }

    public function destroy(Request $request, Message $message, MessageDeletionService $messageDeletionService)
    {
        $user = $request->user();
        $message->loadMissing('conversation');

        $conversation = Conversation::where('lawyer_id', $user->id)->findOrFail($message->conversation_id);

        if ($message->sender_id !== $user->id || $conversation->id !== $message->conversation_id) {
            abort(403);
        }

        $payload = $messageDeletionService->deleteForSender($message, $user->id);

        try {
            broadcast(new MessageDeleted($payload))->toOthers();
        } catch (\Throwable $e) {
            \Log::error('API lawyer message deletion broadcast failed: ' . $e->getMessage());
        }

        return response()->json($payload);
    }

    public function update(Request $request, Message $message, MessageUpdateService $messageUpdateService)
    {
        $request->validate([
            'body' => 'required|string|max:5000',
        ]);

        $user = $request->user();
        $message->loadMissing('conversation');

        $conversation = Conversation::where('lawyer_id', $user->id)->findOrFail($message->conversation_id);

        if ($message->sender_id !== $user->id || $conversation->id !== $message->conversation_id) {
            abort(403);
        }

        $payload = $messageUpdateService->updateForSender($message, $user->id, $request->string('body')->trim()->toString());

        try {
            broadcast(new MessageUpdated($payload))->toOthers();
        } catch (\Throwable $e) {
            \Log::error('API lawyer message update broadcast failed: ' . $e->getMessage());
        }

        return response()->json($payload);
    }
}
