<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    public function index(Request $request) {
        $user = Auth::user();
        
        // Debug logging
        \Log::info('Client MessageController - User accessing messages:', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->role,
            'request_path' => $request->path()
        ]);
        
        $conversations = Conversation::where('client_id', $user->id)
            ->with(['client','lawyer','latestMessage'])
            ->leftJoin('messages', function($join) {
                $join->on('conversations.id', '=', 'messages.conversation_id')
                     ->whereRaw('messages.id = (SELECT MAX(id) FROM messages WHERE conversation_id = conversations.id)');
            })
            ->orderByRaw('COALESCE(messages.created_at, conversations.created_at) DESC')
            ->select('conversations.*')
            ->get();
            
        \Log::info('Client MessageController - Found conversations:', [
            'user_id' => $user->id,
            'conversation_count' => $conversations->count(),
            'conversation_ids' => $conversations->pluck('id')->toArray()
        ]);

        $activeConvId = $request->get('conversation');
        $activeConv = null;
        $messages   = collect();

        if ($activeConvId) {
            $activeConv = Conversation::with(['client','lawyer','lawyer.lawyerProfile'])->findOrFail($activeConvId);
            // mark messages as read
            Message::where('conversation_id', $activeConvId)
                ->where('sender_id', '!=', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
            $messages = Message::where('conversation_id', $activeConvId)
                ->with('sender')
                ->orderBy('created_at')
                ->get();
        } elseif ($conversations->isNotEmpty()) {
            return redirect()->route('messages', ['conversation' => $conversations->first()->id]);
        }

        return view('messages', compact('conversations','activeConv','messages','user'));
    }

    public function startConversation(Request $request) {
        $request->validate(['lawyer_id' => 'required|exists:users,id']);
        $client = Auth::user();
        $conv = Conversation::firstOrCreate(
            ['client_id' => $client->id, 'lawyer_id' => $request->lawyer_id]
        );
        return redirect()->route('messages', ['conversation' => $conv->id]);
    }

    public function send(Request $request) {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'body'            => 'nullable|string|max:2000',
            'attachment'      => 'nullable|file|max:20480|mimes:jpeg,png,jpg,gif,webp,heic,heif,pdf,doc,docx,txt,mp3,wav,m4a,aac,ogg,oga,webm',
            'attachments'     => 'nullable|array',
            'attachments.*'   => 'file|max:20480|mimes:jpeg,png,jpg,gif,webp,heic,heif,pdf,doc,docx,txt,mp3,wav,m4a,aac,ogg,oga,webm',
        ]);

        $conv = Conversation::findOrFail($request->conversation_id);
        $user = Auth::user();
        $attachments = [];

        if ($conv->client_id !== $user->id) abort(403);
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
                'sender_id' => $user->id,
                'body' => $request->body ?? '',
            ]));
        } else {
            foreach ($attachments as $index => $file) {
                $path = $file->store('message-attachments', 'public');
                $messages->push(Message::create([
                    'conversation_id' => $conv->id,
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
                \Log::error('Broadcasting failed: ' . $e->getMessage());
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
                        'attachment_url' => $message->attachment_url,
                        'attachment_path' => $message->attachment_url,
                        'attachment_name' => $message->attachment_name,
                        'attachment_type' => $message->attachment_type,
                        'batch_uuid' => $message->batch_uuid,
                    ];
                })->values(),
            ]);
        }

        return redirect()->route('messages', ['conversation' => $conv->id]);
    }
}
