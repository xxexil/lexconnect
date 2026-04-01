<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Conversation;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index() {
        $user = Auth::user();

        // Auto-expire past upcoming consultations
        Consultation::expireOverdue('client_id', $user->id);

        $upcomingConsultations = Consultation::with(['lawyer','lawyer.lawyerProfile'])
            ->where('client_id', $user->id)
            ->where('status', 'upcoming')
            ->orderBy('scheduled_at')
            ->get();

        $completedConsultations = Consultation::with(['lawyer','lawyer.lawyerProfile'])
            ->where('client_id', $user->id)
            ->where('status', 'completed')
            ->latest('scheduled_at')
            ->take(5)
            ->get();

        $cancelledConsultations = Consultation::with(['lawyer','lawyer.lawyerProfile'])
            ->where('client_id', $user->id)
            ->where('status', 'cancelled')
            ->latest('updated_at')
            ->take(5)
            ->get();

        $expiredConsultations = Consultation::with(['lawyer','lawyer.lawyerProfile'])
            ->where('client_id', $user->id)
            ->where('status', 'expired')
            ->latest('scheduled_at')
            ->take(5)
            ->get();

        $recentPayments = Payment::with(['lawyer','consultation'])
            ->where('client_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $totalConsultations     = Consultation::where('client_id', $user->id)->count();
        $upcomingCount          = $upcomingConsultations->count();
        $totalSpent             = Payment::where('client_id', $user->id)->where('status','paid')->sum('amount');
        $thisMonthConsultations = Consultation::where('client_id', $user->id)
            ->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        $thisMonthSpent = Payment::where('client_id', $user->id)->where('status','paid')
            ->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('amount');

        $unreadMessages = 0;
        $conversations = Conversation::where('client_id', $user->id)->with('messages')->get();
        foreach ($conversations as $conv) {
            $unreadMessages += $conv->messages->whereNull('read_at')->where('sender_id', '!=', $user->id)->count();
        }

        return view('dashboard', compact(
            'upcomingConsultations','completedConsultations','cancelledConsultations','expiredConsultations',
            'recentPayments','totalConsultations','upcomingCount','totalSpent',
            'unreadMessages','thisMonthConsultations','thisMonthSpent'
        ));
    }
}
