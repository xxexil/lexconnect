<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Conversation;
use App\Models\LawyerBlockedDate;
use App\Models\Message;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class LawyerDashboardController extends Controller
{
    public function index()
    {
        $user    = Auth::user();
        $profile = $user->lawyerProfile;

        // Auto-expire past upcoming consultations
        Consultation::expireOverdue('lawyer_id', $user->id);

        $pendingConsultations = Consultation::with('client')
            ->where('lawyer_id', $user->id)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $todayConsultations = Consultation::with('client')
            ->where('lawyer_id', $user->id)
            ->where('status', 'upcoming')
            ->whereDate('scheduled_at', today())
            ->orderBy('scheduled_at')
            ->get();

        $upcomingCount  = Consultation::where('lawyer_id', $user->id)->where('status', 'upcoming')->count();
        $pendingCount   = $pendingConsultations->count();
        $totalEarned    = Payment::where('lawyer_id', $user->id)->where('status', 'paid')->sum('amount');
        $totalClients   = Consultation::where('lawyer_id', $user->id)->distinct('client_id')->count('client_id');

        $convIds        = Conversation::where('lawyer_id', $user->id)->pluck('id');
        $unreadMessages = Message::whereIn('conversation_id', $convIds)
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->count();

        $recentConsultations = Consultation::with('client')
            ->where('lawyer_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->take(6)
            ->get();

        $blockedDates = LawyerBlockedDate::where('lawyer_id', $user->id)
            ->where('blocked_date', '>=', today())
            ->orderBy('blocked_date')
            ->get();

        return view('lawyer.dashboard', compact(
            'profile', 'pendingConsultations', 'todayConsultations',
            'upcomingCount', 'pendingCount', 'totalEarned', 'totalClients',
            'unreadMessages', 'recentConsultations', 'blockedDates'
        ));
    }
}
