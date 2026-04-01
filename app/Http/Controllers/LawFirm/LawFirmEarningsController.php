<?php

namespace App\Http\Controllers\LawFirm;

use App\Http\Controllers\Controller;
use App\Models\LawyerProfile;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class LawFirmEarningsController extends Controller
{
    public function index()
    {
        $firm      = Auth::user()->lawFirmProfile;
        $lawyerIds = LawyerProfile::where('law_firm_id', $firm->id)->pluck('user_id');

        $totalEarned     = Payment::whereIn('lawyer_id', $lawyerIds)->where('status', 'paid')->sum('amount');
        $thisMonthEarned = Payment::whereIn('lawyer_id', $lawyerIds)
            ->where('status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');
        $pendingAmount   = Payment::whereIn('lawyer_id', $lawyerIds)->where('status', 'pending')->sum('amount');
        $totalClients    = Payment::whereIn('lawyer_id', $lawyerIds)
            ->where('status', 'paid')
            ->distinct('client_id')
            ->count('client_id');

        $payments = Payment::with(['lawyer', 'client', 'consultation'])
            ->whereIn('lawyer_id', $lawyerIds)
            ->latest()
            ->get();

        // Per-lawyer breakdown
        $lawyerBreakdown = LawyerProfile::with('user')
            ->where('law_firm_id', $firm->id)
            ->get()
            ->map(function ($lp) {
                $lp->earned = Payment::where('lawyer_id', $lp->user_id)->where('status', 'paid')->sum('amount');
                $lp->consultations_count = \App\Models\Consultation::where('lawyer_id', $lp->user_id)->count();
                return $lp;
            });

        return view('lawfirm.earnings', compact(
            'firm', 'totalEarned', 'thisMonthEarned', 'pendingAmount',
            'totalClients', 'payments', 'lawyerBreakdown'
        ));
    }
}
