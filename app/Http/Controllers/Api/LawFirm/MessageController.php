<?php

namespace App\Http\Controllers\Api\LawFirm;

use App\Events\MessageSent;
use App\Events\MessageDeleted;
use App\Events\MessageUpdated;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\MessageDeletionService;
use App\Services\MessageUpdateService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $conversations = Conversation::where('client_id', $user->id)
            ->with(['lawyer:id,name,avatar', 'client:id,name,avatar', 'latestMessage'])
            ->leftJoin('messages', function ($join) {
                $join->on('conversations.id', '=', 'messages.conversation_id')
                    ->whereRaw('messages.id = (SELECT MAX(id) FROM messages WHERE conversation_id = conversations.id)');
            })
            ->orderByRaw('COALESCE(messages.created_at, conversations.created_at) DESC')
            ->select('conversations.*')
            ->get()
            ->map(function (Conversation $conversation) use ($user) {
                $unread = $conversation->messages()
                    ->whereNull('read_at')
                    ->where('sender_id', '!=', $user->id)
                    ->count();

                return [
                    'id' => $conversation->id,
                    'call_room_name' => $conversation->callRoomName(),
                    'other_user' => $conversation->lawyer ? [
                        'id' => $conversation->lawyer->id,
                        'name' => $conversation->lawyer->name,
                        'avatar_url' => $conversation->lawyer->avatar_url,
                    ] : null,
                    'last_message' => $conversation->latestMessage?->body ?: ($conversation->latestMessage?->attachment_name ? 'Attachment' : null),
                    'last_attachment_type' => $conversation->latestMessage?->attachment_type,
                    'last_at' => $conversation->latestMessage?->created_at,
                    'unread' => $unread,
                ];
            })
            ->values();

        return response()->json($conversations);
    }

    public function show(Request $request, $conversationId)
    {
        $user = $request->user();
        $conversation = Conversation::with(['lawyer:id,name,avatar'])
            ->where('client_id', $user->id)
            ->findOrFail($conversationId);

        $conversation->messages()
            ->whereNull('read_at')
            ->where('sender_id', '!=', $user->id)
            ->update(['read_at' => now()]);

        $messages = $conversation->messages()
            ->with('sender:id,name,avatar')
            ->orderBy('created_at')
            ->get()
            ->map(fn (Message $message) => $message->toApiArray($user->id))
            ->values();

        return response()->json([
            'conversation_id' => $conversation->id,
            'call_room_name' => $conversation->callRoomName(),
            'other_user' => $conversation->lawyer ? [
                'id' => $conversation->lawyer->id,
                'name' => $conversation->lawyer->name,
                'avatar_url' => $conversation->lawyer->avatar_url,
            ] : null,
            'messages' => $messages,
        ]);
    }

    public function start(Request $request)
    {
        $request->validate(['lawyer_id' => 'required|exists:users,id']);

        $conversation = Conversation::firstOrCreate([
            'client_id' => $request->user()->id,
            'lawyer_id' => $request->lawyer_id,
        ]);

        return response()->json(['conversation_id' => $conversation->id]);
    }

    public function send(Request $request)
    {
        $request->validate(
            array_merge(['conversation_id' => 'required|exists:conversations,id'], Message::messageValidationRules(2000)),
            Message::messageValidationMessages()
        );

        $user = $request->user();
        $conversation = Conversation::where('client_id', $user->id)->findOrFail($request->conversation_id);

        $attachments = [];
        if ($request->hasFile('attachment')) {
            $attachments[] = $request->file('attachment');
        }
        if ($request->hasFile('attachments')) {
            $attachments = array_merge($attachments, $request->file('attachments'));
        }

        if (!$request->filled('body') && count($attachments) === 0) {
            return response()->json(['error' => 'Message or attachment required.'], 422);
        }

        $messages = collect();
        $batchUuid = count($attachments) > 1 ? (string) Str::uuid() : null;

        if (count($attachments) === 0) {
            $messages->push(Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $user->id,
                'body' => $request->body ?? '',
            ]));
        } else {
            foreach ($attachments as $index => $file) {
                $messages->push(Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $user->id,
                    'body' => $index === 0 ? ($request->body ?? '') : '',
                    ...Message::storeUploadedAttachment($file),
                    'batch_uuid' => $batchUuid,
                ]));
            }
        }

        $messages->each(function (Message $message) {
            try {
                broadcast(new MessageSent($message))->toOthers();
            } catch (\Throwable $e) {
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
        $conversation = Conversation::where('client_id', $user->id)->findOrFail($conversationId);

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

        $conversation = Conversation::where('client_id', $user->id)->findOrFail($message->conversation_id);

        if ($message->sender_id !== $user->id || $conversation->id !== $message->conversation_id) {
            abort(403);
        }

        $payload = $messageDeletionService->deleteForSender($message, $user->id);

        try {
            broadcast(new MessageDeleted($payload))->toOthers();
        } catch (\Throwable $e) {
            \Log::error('API law firm message deletion broadcast failed: ' . $e->getMessage());
        }

        return response()->json($payload);
    }

    public function update(Request $request, Message $message, MessageUpdateService $messageUpdateService)
    {
        $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $user = $request->user();
        $message->loadMissing('conversation');

        $conversation = Conversation::where('client_id', $user->id)->findOrFail($message->conversation_id);

        if ($message->sender_id !== $user->id || $conversation->id !== $message->conversation_id) {
            abort(403);
        }

        $payload = $messageUpdateService->updateForSender($message, $user->id, $request->string('body')->trim()->toString());

        try {
            broadcast(new MessageUpdated($payload))->toOthers();
        } catch (\Throwable $e) {
            \Log::error('API law firm message update broadcast failed: ' . $e->getMessage());
        }

        return response()->json($payload);
    }
}
