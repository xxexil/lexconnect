<?php

namespace App\Http\Controllers\LawFirm;

use App\Events\MessageSent;
use App\Events\MessageUpdated;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\MessageDeletionService;
use App\Services\MessageUpdateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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
        $request->validate(
            array_merge(['conversation_id' => 'required|exists:conversations,id'], Message::messageValidationRules(2000)),
            Message::messageValidationMessages()
        );

        $conv = Conversation::findOrFail($request->conversation_id);
        $attachments = [];
        if ($conv->client_id !== Auth::id()) abort(403);

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
                $messages->push(Message::create([
                    'conversation_id' => $conv->id,
                    'sender_id' => Auth::id(),
                    'body' => $index === 0 ? ($request->body ?? '') : '',
                    ...Message::storeUploadedAttachment($file),
                    'batch_uuid' => $batchUuid,
                ]));
            }
        }

        $messages->each(function (Message $message) {
            try { broadcast(new MessageSent($message))->toOthers(); } catch (\Exception $e) {}
        });

        if ($request->expectsJson()) {
            return response()->json([
                'messages' => $messages->map(fn (Message $message) => $message->load('sender')->toApiArray(Auth::id()))->values(),
            ]);
        }

        return redirect()->route('lawfirm.messages', ['conversation' => $conv->id]);
    }

    public function destroy(Request $request, Message $message, MessageDeletionService $messageDeletionService)
    {
        $user = Auth::user();
        $message->loadMissing('conversation');

        if (
            $message->sender_id !== $user->id ||
            !$message->conversation ||
            $message->conversation->client_id !== $user->id
        ) {
            abort(403);
        }

        $payload = $messageDeletionService->deleteForSender($message, $user->id);

        try {
            broadcast(new \App\Events\MessageDeleted($payload))->toOthers();
        } catch (\Throwable $e) {
            \Log::error('Broadcasting law firm message deletion failed: ' . $e->getMessage());
        }

        return response()->json($payload);
    }

    public function update(Request $request, Message $message, MessageUpdateService $messageUpdateService)
    {
        $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $user = Auth::user();
        $message->loadMissing('conversation');

        if (
            $message->sender_id !== $user->id ||
            !$message->conversation ||
            $message->conversation->client_id !== $user->id
        ) {
            abort(403);
        }

        $payload = $messageUpdateService->updateForSender($message, $user->id, $request->string('body')->trim()->toString());

        try {
            broadcast(new MessageUpdated($payload))->toOthers();
        } catch (\Throwable $e) {
            \Log::error('Broadcasting law firm message update failed: ' . $e->getMessage());
        }

        return response()->json($payload);
    }
}
