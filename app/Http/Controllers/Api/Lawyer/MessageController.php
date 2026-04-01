<?php

namespace App\Http\Controllers\Api\Lawyer;

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

        $conversations = Conversation::where('lawyer_id', $user->id)
            ->with(['client:id,name,avatar', 'latestMessage'])
            ->get()
            ->map(function ($c) use ($user) {
                $unread = $c->messages()->whereNull('read_at')->where('sender_id', '!=', $user->id)->count();
                return [
                    'id'          => $c->id,
                    'other_user'  => ['id' => $c->client->id, 'name' => $c->client->name, 'avatar_url' => $c->client->avatar_url],
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
        $conversation = Conversation::where('lawyer_id', $user->id)->findOrFail($conversationId);

        $conversation->messages()->whereNull('read_at')->where('sender_id', '!=', $user->id)->update(['read_at' => now()]);

        $messages = $conversation->messages()->with('sender:id,name')->orderBy('created_at')->get()->map(fn($m) => [
            'id'          => $m->id,
            'body'        => $m->body,
            'sender_id'   => $m->sender_id,
            'sender_name' => $m->sender->name,
            'created_at'  => $m->created_at,
            'is_mine'     => $m->sender_id === $user->id,
        ]);

        return response()->json([
            'conversation_id' => $conversation->id,
            'other_user'      => ['id' => $conversation->client->id, 'name' => $conversation->client->name],
            'messages'        => $messages,
        ]);
    }

    public function send(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'body'            => 'required|string|max:5000',
        ]);

        $user = $request->user();
        $conversation = Conversation::where('lawyer_id', $user->id)->findOrFail($request->conversation_id);

        $message = $conversation->messages()->create([
            'sender_id' => $user->id,
            'body'      => $request->body,
        ]);

        try { 
            broadcast(new MessageSent($message))->toOthers(); 
        } catch (\Throwable $e) {
            \Log::error('Broadcasting failed: ' . $e->getMessage());
        }

        return response()->json([
            'id'         => $message->id,
            'body'       => $message->body,
            'sender_id'  => $message->sender_id,
            'created_at' => $message->created_at,
        ]);
    }
}
