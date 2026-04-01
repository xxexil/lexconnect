<?php

namespace App\Http\Controllers\Api\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Payment;
use App\Models\Conversation;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'lawyer') {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $stats = [
            'pending'    => Consultation::where('lawyer_id', $user->id)->where('status', 'pending')->count(),
            'upcoming'   => Consultation::where('lawyer_id', $user->id)->where('status', 'upcoming')->count(),
            'completed'  => Consultation::where('lawyer_id', $user->id)->where('status', 'completed')->count(),
            'total_clients' => Consultation::where('lawyer_id', $user->id)
                ->distinct('client_id')->count('client_id'),
        ];

        $totalEarned = Payment::where('lawyer_id', $user->id)
            ->where('status', 'paid')
            ->sum('lawyer_net');

        $pendingConsultations = Consultation::where('lawyer_id', $user->id)
            ->where('status', 'pending')
            ->with('client:id,name,avatar')
            ->orderBy('scheduled_at')
            ->take(5)
            ->get()
            ->map(fn($c) => [
                'id'           => $c->id,
                'code'         => $c->code,
                'scheduled_at' => $c->scheduled_at,
                'type'         => $c->type,
                'price'        => $c->price,
                'client'       => ['id' => $c->client->id, 'name' => $c->client->name],
            ]);

        $unreadMessages = Conversation::where('lawyer_id', $user->id)
            ->with(['messages' => fn($q) => $q->whereNull('read_at')->where('sender_id', '!=', $user->id)])
            ->get()
            ->sum(fn($c) => $c->messages->count());

        return response()->json([
            'stats'                => $stats,
            'total_earned'         => $totalEarned,
            'pending_consultations'=> $pendingConsultations,
            'unread_messages'      => $unreadMessages,
        ]);
    }
}
