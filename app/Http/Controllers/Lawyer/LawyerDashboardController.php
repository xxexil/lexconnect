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

        // Next upcoming session (even if not today)
        $nextSession = Consultation::with('client')
            ->where('lawyer_id', $user->id)
            ->where('status', 'upcoming')
            ->where('scheduled_at', '>', now())
            ->orderBy('scheduled_at')
            ->first();

        // Up to 5 upcoming sessions for display when no sessions today
        $upcomingSessions = Consultation::with('client')
            ->where('lawyer_id', $user->id)
            ->where('status', 'upcoming')
            ->where('scheduled_at', '>', now())
            ->orderBy('scheduled_at')
            ->take(5)
            ->get();

        $upcomingCount  = Consultation::where('lawyer_id', $user->id)->where('status', 'upcoming')->count();
        $pendingCount   = $pendingConsultations->count();
        $totalEarned    = Payment::where('lawyer_id', $user->id)
            ->whereIn('status', ['paid', 'downpayment_paid'])
            ->get()
            ->sum(fn($p) => $p->lawyer_net ?? ($p->amount - ($p->firm_cut ?? 0)));
        $totalClients   = Consultation::where('lawyer_id', $user->id)->distinct('client_id')->count('client_id');

        $convIds        = Conversation::where('lawyer_id', $user->id)->pluck('id');
        $unreadMessages = Message::whereIn('conversation_id', $convIds)
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->count();

        $recentConsultations = Consultation::with('client')
            ->where('lawyer_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get();

        $blockedDates = LawyerBlockedDate::where('lawyer_id', $user->id)
            ->where('blocked_date', '>=', today())
            ->orderBy('blocked_date')
            ->get();

        // Booked slots for calendar (upcoming consultations)
        $bookedSlots = Consultation::where('lawyer_id', $user->id)
            ->whereIn('status', ['upcoming', 'pending'])
            ->get()
            ->groupBy(fn($c) => \Carbon\Carbon::parse($c->scheduled_at)->format('Y-m-d'))
            ->map(fn($group) => $group->map(fn($c) => [
                'time'   => \Carbon\Carbon::parse($c->scheduled_at)->format('g:i A'),
                'client' => $c->client->name ?? '',
                'type'   => $c->type,
            ])->values()->toArray());

        // Monthly earnings for the last 12 months
        $monthlyEarnings = collect(range(11, 0))->map(function ($monthsAgo) use ($user) {
            $date = now()->subMonths($monthsAgo);
            return [
                'month' => $date->format('M Y'),
                'total' => Payment::where('lawyer_id', $user->id)
                    ->whereIn('status', ['paid', 'downpayment_paid'])
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->get()
                    ->sum(fn($p) => $p->lawyer_net ?? ($p->amount - ($p->firm_cut ?? 0))),
            ];
        });

        // Monthly consultations count for the last 12 months
        $monthlyConsultations = collect(range(11, 0))->map(function ($monthsAgo) use ($user) {
            $date = now()->subMonths($monthsAgo);
            return [
                'month' => $date->format('M Y'),
                'count' => Consultation::where('lawyer_id', $user->id)
                    ->where('status', 'completed')
                    ->whereMonth('updated_at', $date->month)
                    ->whereYear('updated_at', $date->year)
                    ->count(),
            ];
        });

        // Earnings insights
        $highestMonth    = $monthlyEarnings->sortByDesc('total')->first();
        $totalThisYear   = Payment::where('lawyer_id', $user->id)
            ->whereIn('status', ['paid', 'downpayment_paid'])
            ->whereYear('created_at', now()->year)
            ->get()
            ->sum(fn($p) => $p->lawyer_net ?? ($p->amount - ($p->firm_cut ?? 0)));

        // Per-year earnings data for chart year selector (last 5 years)
        $yearlyEarnings = collect(range(now()->year, now()->year - 4))->mapWithKeys(function ($y) use ($user) {
            $months = collect(range(1, 12))->map(function ($m) use ($y, $user) {
                return [
                    'month' => \Carbon\Carbon::create($y, $m)->format('M'),
                    'total' => Payment::where('lawyer_id', $user->id)
                        ->whereIn('status', ['paid', 'downpayment_paid'])
                        ->whereMonth('created_at', $m)
                        ->whereYear('created_at', $y)
                        ->get()
                        ->sum(fn($p) => $p->lawyer_net ?? ($p->amount - ($p->firm_cut ?? 0))),
                ];
            });
            return [$y => $months];
        });

        // Per-year consultations data for chart year selector (last 5 years)
        $yearlyConsultations = collect(range(now()->year, now()->year - 4))->mapWithKeys(function ($y) use ($user) {
            $months = collect(range(1, 12))->map(function ($m) use ($y, $user) {
                return [
                    'month' => \Carbon\Carbon::create($y, $m)->format('M'),
                    'count' => Consultation::where('lawyer_id', $user->id)
                        ->where('status', 'completed')
                        ->whereMonth('updated_at', $m)
                        ->whereYear('updated_at', $y)
                        ->count(),
                ];
            });
            return [$y => $months];
        });

        return view('lawyer.dashboard', compact(
            'profile', 'pendingConsultations', 'todayConsultations', 'nextSession', 'upcomingSessions',
            'upcomingCount', 'pendingCount', 'totalEarned', 'totalClients',
            'unreadMessages', 'recentConsultations', 'blockedDates',
            'bookedSlots', 'monthlyEarnings', 'monthlyConsultations',
            'highestMonth', 'totalThisYear', 'yearlyEarnings', 'yearlyConsultations'
        ));
    }
}
