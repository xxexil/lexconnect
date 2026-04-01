<?php

namespace App\Http\Controllers\Lawyer;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LawyerMessageController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Debug logging
        \Log::info('Lawyer MessageController - User accessing messages:', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->role,
            'request_path' => $request->path()
        ]);

        // Get conversations with proper sorting - simple query without grouping
        $conversations = Conversation::where('lawyer_id', $user->id)
            ->with(['client', 'lawyer', 'latestMessage'])
            ->get()
            ->sortByDesc(function($conversation) {
                return $conversation->latestMessage ? $conversation->latestMessage->created_at : $conversation->created_at;
            });
            
        \Log::info('Lawyer MessageController - Found conversations:', [
            'user_id' => $user->id,
            'conversation_count' => $conversations->count(),
            'conversation_ids' => $conversations->pluck('id')->toArray(),
            'client_ids' => $conversations->pluck('client_id')->toArray(),
            'conversations_detail' => $conversations->map(function($conv) {
                return [
                    'id' => $conv->id,
                    'client_id' => $conv->client_id,
                    'client_name' => $conv->client->name ?? 'Unknown',
                    'latest_message' => $conv->latestMessage ? $conv->latestMessage->created_at : null
                ];
            })->toArray()
        ]);

        $activeConvId = $request->get('conversation');
        $activeConv   = null;
        $messages     = collect();

        if ($activeConvId) {
            $activeConv = Conversation::with(['client', 'lawyer'])->findOrFail($activeConvId);
            if ($activeConv->lawyer_id !== $user->id) abort(403);

            Message::where('conversation_id', $activeConvId)
                ->where('sender_id', '!=', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            $messages = Message::where('conversation_id', $activeConvId)
                ->with('sender')
                ->orderBy('created_at')
                ->get();
        } elseif ($conversations->isNotEmpty()) {
            return redirect()->route('lawyer.messages', ['conversation' => $conversations->first()->id]);
        }

        return view('lawyer.messages', compact('conversations', 'activeConv', 'messages', 'user'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'body'            => 'required|string|max:2000',
        ]);

        $conv = Conversation::findOrFail($request->conversation_id);
        $user = Auth::user();
        
        // Debug logging
        \Log::info('Lawyer MessageController send - Message attempt:', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->role,
            'conversation_id' => $conv->id,
            'conversation_client_id' => $conv->client_id,
            'conversation_lawyer_id' => $conv->lawyer_id,
            'message_body' => $request->body
        ]);
        
        if ($conv->lawyer_id !== Auth::id()) {
            \Log::warning('Lawyer MessageController - Unauthorized send attempt:', [
                'user_id' => $user->id,
                'conversation_id' => $conv->id,
                'conversation_lawyer_id' => $conv->lawyer_id
            ]);
            abort(403);
        }

        $message = Message::create([
            'conversation_id' => $conv->id,
            'sender_id'       => Auth::id(),
            'body'            => $request->body,
        ]);

        try { 
            broadcast(new MessageSent($message))->toOthers(); 
        } catch (\Exception $e) {
            \Log::error('Broadcasting failed for lawyer message: ' . $e->getMessage());
        }

        if ($request->expectsJson()) {
            $message->load('sender');
            return response()->json([
                'id'        => $message->id,
                'sender_id' => $message->sender_id,
                'body'      => $message->body,
                'time'      => $message->created_at->format('g:i A'),
            ]);
        }

        return redirect()->route('lawyer.messages', ['conversation' => $conv->id]);
    }
}