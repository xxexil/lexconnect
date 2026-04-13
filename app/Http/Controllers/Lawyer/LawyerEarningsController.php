<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class LawyerEarningsController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $allPayments = Payment::where('lawyer_id', $user->id)
            ->latest()
            ->get();

        $paidPayments  = $allPayments->whereIn('status', ['paid', 'downpayment_paid']);
        $totalEarned   = $paidPayments->sum('lawyer_net');
        $thisMonth     = $paidPayments->filter(fn($p) => $p->created_at->isCurrentMonth())->sum('lawyer_net');
        $pendingAmount = $allPayments->where('status', 'pending')->sum('amount');
        $totalFirmCut  = $paidPayments->sum('firm_cut');
        $totalClients  = $paidPayments->unique('client_id')->count();

        return view('lawyer.earnings', compact('totalEarned', 'thisMonth', 'pendingAmount', 'totalClients', 'totalFirmCut'));
    }
}
