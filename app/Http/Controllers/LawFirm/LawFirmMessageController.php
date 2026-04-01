<?php

namespace App\Http\Controllers\LawFirm;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LawFirmMessageController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Law firm user is stored as client_id in conversations they initiate
        $conversations = Conversation::where('client_id', $user->id)
            ->with(['lawyer', 'client', 'latestMessage', 'messages'])
            ->leftJoin('messages', function($join) {
                $join->on('conversations.id', '=', 'messages.conversation_id')
                     ->whereRaw('messages.id = (SELECT MAX(id) FROM messages WHERE conversation_id = conversations.id)');
            })
            ->orderByRaw('COALESCE(messages.created_at, conversations.created_at) DESC')
            ->select('conversations.*')
            ->get();

        $activeConvId = $request->get('conversation');
        $activeConv   = null;
        $messages     = collect();

        if ($activeConvId) {
            $activeConv = Conversation::with(['client', 'lawyer'])->findOrFail($activeConvId);
            if ($activeConv->client_id !== $user->id) abort(403);

            Message::where('conversation_id', $activeConvId)
                ->where('sender_id', '!=', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            $messages = Message::where('conversation_id', $activeConvId)
                ->with('sender')
                ->orderBy('created_at')
                ->get();
        } elseif ($conversations->isNotEmpty()) {
            return redirect()->route('lawfirm.messages', ['conversation' => $conversations->first()->id]);
        }

        return view('lawfirm.messages', compact('conversations', 'activeConv', 'messages', 'user'));
    }

    public function startConversation(Request $request)
    {
        $request->validate(['lawyer_id' => 'required|exists:users,id']);
        $user = Auth::user();

        $conv = Conversation::firstOrCreate([
            'client_id' => $user->id,
            'lawyer_id' => $request->lawyer_id,
        ]);

        return redirect()->route('lawfirm.messages', ['conversation' => $conv->id]);
    }

    public function send(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'body'            => 'required|string|max:2000',
        ]);

        $conv = Conversation::findOrFail($request->conversation_id);
        if ($conv->client_id !== Auth::id()) abort(403);

        $message = Message::create([
            'conversation_id' => $conv->id,
            'sender_id'       => Auth::id(),
            'body'            => $request->body,
        ]);

        try { broadcast(new MessageSent($message))->toOthers(); } catch (\Exception $e) {}

        if ($request->expectsJson()) {
            $message->load('sender');
            return response()->json([
                'id'        => $message->id,
                'sender_id' => $message->sender_id,
                'body'      => $message->body,
                'time'      => $message->created_at->format('g:i A'),
            ]);
        }

        return redirect()->route('lawfirm.messages', ['conversation' => $conv->id]);
    }
}
