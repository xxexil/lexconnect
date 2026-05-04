<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\LawyerProfile;
use App\Models\Message;
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

        $completedConsultations = Consultation::with(['lawyer','lawyer.lawyerProfile','review'])
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

        $unreadMessages = Message::whereNull('read_at')
            ->where('sender_id', '!=', $user->id)
            ->whereHas('conversation', function ($query) use ($user) {
                $query->where('client_id', $user->id);
            })
            ->count();

        $quickSearchSpecialties = LawyerProfile::query()
            ->whereNotNull('specialty')
            ->where('specialty', '!=', '')
            ->distinct()
            ->orderBy('specialty')
            ->pluck('specialty');

        $quickSearchLocations = LawyerProfile::query()
            ->whereNotNull('location')
            ->where('location', '!=', '')
            ->distinct()
            ->orderBy('location')
            ->pluck('location');

        return view('dashboard', compact(
            'upcomingConsultations','completedConsultations','cancelledConsultations','expiredConsultations',
            'recentPayments','totalConsultations','upcomingCount','totalSpent',
            'unreadMessages','thisMonthConsultations','thisMonthSpent',
            'quickSearchSpecialties','quickSearchLocations'
        ));
    }
}
