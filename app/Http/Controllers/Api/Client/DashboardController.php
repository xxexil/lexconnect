<?php

namespace App\Http\Controllers\Api\Client;

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

        $upcoming = Consultation::where('client_id', $user->id)
            ->where('status', 'upcoming')
            ->with('lawyer:id,name,avatar')
            ->orderBy('scheduled_at')
            ->take(5)
            ->get()
            ->map(fn($c) => [
                'id'              => $c->id,
                'code'            => $c->code,
                'scheduled_at'    => $c->scheduled_at,
                'type'            => $c->type,
                'status'          => $c->status,
                'duration_minutes'=> $c->duration_minutes,
                'price'           => $c->price,
                'lawyer'          => ['id' => $c->lawyer->id, 'name' => $c->lawyer->name],
            ]);

        $stats = [
            'total'     => Consultation::where('client_id', $user->id)->count(),
            'upcoming'  => Consultation::where('client_id', $user->id)->where('status', 'upcoming')->count(),
            'completed' => Consultation::where('client_id', $user->id)->where('status', 'completed')->count(),
            'cancelled' => Consultation::where('client_id', $user->id)->where('status', 'cancelled')->count(),
        ];

        $totalSpent = Payment::where('client_id', $user->id)
            ->where('status', 'paid')
            ->sum('amount');

        $unreadMessages = Conversation::where(function ($q) use ($user) {
                $q->where('client_id', $user->id)->orWhere('lawyer_id', $user->id);
            })
            ->with(['messages' => fn($q) => $q->whereNull('read_at')->where('sender_id', '!=', $user->id)])
            ->get()
            ->sum(fn($c) => $c->messages->count());

        return response()->json([
            'upcoming_consultations' => $upcoming,
            'stats'                  => $stats,
            'total_spent'            => $totalSpent,
            'unread_messages'        => $unreadMessages,
        ]);
    }
}
