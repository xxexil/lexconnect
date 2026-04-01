<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Http\Request;

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
                    'last_message'=> $c->latestMessage?->body,
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

        $messages = $conversation->messages()->with('sender:id,name,avatar')->orderBy('created_at')->get()->map(fn($m) => [
            'id'          => $m->id,
            'body'        => $m->body,
            'sender_id'   => $m->sender_id,
            'sender_name' => $m->sender->name,
            'created_at'  => $m->created_at,
            'is_mine'     => $m->sender_id === $user->id,
        ]);

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
            'body'            => 'required|string|max:5000',
        ]);

        $user = $request->user();
        $conversation = Conversation::where(function ($q) use ($user) {
            $q->where('client_id', $user->id)->orWhere('lawyer_id', $user->id);
        })->findOrFail($request->conversation_id);

        $message = $conversation->messages()->create([
            'sender_id' => $user->id,
            'body'      => $request->body,
        ]);

        \Log::info('Message created with ID: ' . $message->id . ' - About to attempt broadcasting');

        try { 
            \Log::info('Attempting to broadcast MessageSent event for message ID: ' . $message->id);
            broadcast(new MessageSent($message))->toOthers(); 
            \Log::info('MessageSent event broadcast successfully for message ID: ' . $message->id);
        } catch (\Throwable $e) {
            \Log::error('Broadcasting failed for message ID: ' . $message->id . ' - Error: ' . $e->getMessage());
            \Log::error('Broadcasting error trace: ' . $e->getTraceAsString());
        }

        return response()->json([
            'id'         => $message->id,
            'body'       => $message->body,
            'sender_id'  => $message->sender_id,
            'created_at' => $message->created_at,
        ]);
    }
}
