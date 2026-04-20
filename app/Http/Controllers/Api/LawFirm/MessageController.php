<?php

namespace App\Http\Controllers\Api\LawFirm;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
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
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'body' => 'nullable|string|max:2000',
            'attachment' => 'nullable|file|max:20480|mimes:jpeg,png,jpg,gif,webp,heic,heif,pdf,doc,docx,txt,mp3,wav,m4a,aac,ogg,oga,webm',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:20480|mimes:jpeg,png,jpg,gif,webp,heic,heif,pdf,doc,docx,txt,mp3,wav,m4a,aac,ogg,oga,webm',
        ]);

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
                $path = $file->store('message-attachments', 'public');
                $messages->push(Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $user->id,
                    'body' => $index === 0 ? ($request->body ?? '') : '',
                    'attachment_path' => $path,
                    'attachment_name' => $file->getClientOriginalName(),
                    'attachment_type' => Message::attachmentTypeForMime($file->getMimeType()),
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
}
