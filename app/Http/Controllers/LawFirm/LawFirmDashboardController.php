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
        $totalEarned         = Payment::whereIn('lawyer_id', $lawyerIds)->where('status', 'paid')->sum('amount');
        $thisMonthEarned     = Payment::whereIn('lawyer_id', $lawyerIds)
            ->where('status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $recentApplications = FirmApplication::with(['lawyer.lawyerProfile'])
            ->where('law_firm_id', $firm->id)
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        $recentConsultations = Consultation::with(['client', 'lawyer'])
            ->whereIn('lawyer_id', $lawyerIds)
            ->latest()
            ->take(6)
            ->get();

        $teamMembers = LawyerProfile::with('user')
            ->where('law_firm_id', $firm->id)
            ->take(4)
            ->get();

        return view('lawfirm.dashboard', compact(
            'firm', 'teamCount', 'activeCount', 'pendingApplications',
            'totalConsultations', 'totalEarned', 'thisMonthEarned',
            'recentApplications', 'recentConsultations', 'teamMembers'
        ));
    }
}
