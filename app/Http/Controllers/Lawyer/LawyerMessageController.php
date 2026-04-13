<?php

namespace App\Http\Controllers\Lawyer;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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
            'body'            => 'nullable|string|max:2000',
            'attachment'      => 'nullable|file|max:10240|mimes:jpeg,png,jpg,gif,webp,pdf,doc,docx,txt',
            'attachments'     => 'nullable|array',
            'attachments.*'   => 'file|max:10240|mimes:jpeg,png,jpg,gif,webp,pdf,doc,docx,txt',
        ]);

        $conv = Conversation::findOrFail($request->conversation_id);
        $user = Auth::user();
        $attachments = [];
        
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
                'conversation_id' => $conv->id,
                'sender_id' => Auth::id(),
                'body' => $request->body ?? '',
            ]));
        } else {
            foreach ($attachments as $index => $file) {
                $path = $file->store('message-attachments', 'public');
                $messages->push(Message::create([
                    'conversation_id' => $conv->id,
                    'sender_id' => Auth::id(),
                    'body' => $index === 0 ? ($request->body ?? '') : '',
                    'attachment_path' => $path,
                    'attachment_name' => $file->getClientOriginalName(),
                    'attachment_type' => str_starts_with($file->getMimeType(), 'image/') ? 'image' : 'file',
                    'batch_uuid' => $batchUuid,
                ]));
            }
        }

        $messages->each(function (Message $message) {
            try {
                broadcast(new MessageSent($message))->toOthers();
            } catch (\Exception $e) {
                \Log::error('Broadcasting failed for lawyer message: ' . $e->getMessage());
            }
        });

        if ($request->expectsJson()) {
            return response()->json([
                'messages' => $messages->map(function (Message $message) {
                    $message->load('sender');

                    return [
                        'id' => $message->id,
                        'sender_id' => $message->sender_id,
                        'body' => $message->body,
                        'time' => $message->created_at->format('g:i A'),
                        'attachment_path' => $message->attachment_path ? asset('storage/' . $message->attachment_path) : null,
                        'attachment_name' => $message->attachment_name,
                        'attachment_type' => $message->attachment_type,
                        'batch_uuid' => $message->batch_uuid,
                    ];
                })->values(),
            ]);
        }

        return redirect()->route('lawyer.messages', ['conversation' => $conv->id]);
    }
}
