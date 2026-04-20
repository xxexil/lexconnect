<?php

namespace App\Http\Controllers\LawFirm;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\FirmApplication;
use App\Models\LawyerProfile;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class LawFirmDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $firm = $user->lawFirmProfile;

        $lawyerIds = LawyerProfile::where('law_firm_id', $firm->id)->pluck('user_id');

        $teamCount           = $lawyerIds->count();
        $activeCount         = LawyerProfile::where('law_firm_id', $firm->id)->get()
            ->filter(fn (LawyerProfile $profile) => $profile->currentStatus() === 'active')
            ->count();
        $pendingApplications = FirmApplication::where('law_firm_id', $firm->id)->where('status', 'pending')->count();
        $totalConsultations  = Consultation::whereIn('lawyer_id', $lawyerIds)->count();

        $totalEarned     = Payment::whereIn('lawyer_id', $lawyerIds)
            ->whereIn('status', ['paid', 'downpayment_paid'])
            ->sum('firm_cut');
        $thisMonthEarned = Payment::whereIn('lawyer_id', $lawyerIds)
            ->whereIn('status', ['paid', 'downpayment_paid'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('firm_cut');

        // Consultation status breakdown (this month)
        $consultationBreakdown = [
            'pending'   => Consultation::whereIn('lawyer_id', $lawyerIds)->where('status', 'pending')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'upcoming'  => Consultation::whereIn('lawyer_id', $lawyerIds)->where('status', 'upcoming')->count(),
            'completed' => Consultation::whereIn('lawyer_id', $lawyerIds)->where('status', 'completed')->whereMonth('updated_at', now()->month)->whereYear('updated_at', now()->year)->count(),
        ];

        // Today's upcoming sessions across all firm lawyers
        $todaySessions = Consultation::with(['client', 'lawyer'])
            ->whereIn('lawyer_id', $lawyerIds)
            ->where('status', 'upcoming')
            ->whereDate('scheduled_at', today())
            ->orderBy('scheduled_at')
            ->get();

        $recentApplications = FirmApplication::with(['lawyer.lawyerProfile'])
            ->where('law_firm_id', $firm->id)
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        // Sort by scheduled_at desc so most recent/upcoming sessions appear first
        $recentConsultations = Consultation::with(['client', 'lawyer'])
            ->whereIn('lawyer_id', $lawyerIds)
            ->orderBy('scheduled_at', 'desc')
            ->take(10)
            ->get();

        $teamMembers = LawyerProfile::with('user')
            ->where('law_firm_id', $firm->id)
            ->get();

        // Monthly earnings for the last 12 months
        $monthlyEarnings = collect(range(11, 0))->map(function ($monthsAgo) use ($lawyerIds) {
            $date = now()->subMonths($monthsAgo);
            return [
                'month' => $date->format('M Y'),
                'total' => Payment::whereIn('lawyer_id', $lawyerIds)
                    ->whereIn('status', ['paid', 'downpayment_paid'])
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->sum('firm_cut'),
            ];
        });

        $highestMonth  = $monthlyEarnings->sortByDesc('total')->first();
        $totalThisYear = Payment::whereIn('lawyer_id', $lawyerIds)
            ->whereIn('status', ['paid', 'downpayment_paid'])
            ->whereYear('created_at', now()->year)
            ->sum('firm_cut');

        // Per-year earnings data for chart year selector (last 5 years)
        $yearlyEarnings = collect(range(now()->year, now()->year - 4))->mapWithKeys(function ($y) use ($lawyerIds) {
            $months = collect(range(1, 12))->map(function ($m) use ($y, $lawyerIds) {
                return [
                    'month' => \Carbon\Carbon::create($y, $m)->format('M'),
                    'total' => Payment::whereIn('lawyer_id', $lawyerIds)
                        ->whereIn('status', ['paid', 'downpayment_paid'])
                        ->whereMonth('created_at', $m)
                        ->whereYear('created_at', $y)
                        ->sum('firm_cut'),
                ];
            });
            return [$y => $months];
        });

        return view('lawfirm.dashboard', compact(
            'firm', 'teamCount', 'activeCount', 'pendingApplications',
            'totalConsultations', 'totalEarned', 'thisMonthEarned',
            'consultationBreakdown', 'todaySessions',
            'recentApplications', 'recentConsultations', 'teamMembers',
            'monthlyEarnings', 'highestMonth', 'totalThisYear', 'yearlyEarnings'
        ));
    }
}
