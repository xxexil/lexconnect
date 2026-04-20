<?php

namespace App\Http\Controllers\LawFirm;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\LawyerProfile;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class LawFirmEarningsController extends Controller
{
    public function index()
    {
        $firm      = Auth::user()->lawFirmProfile;
        $lawyerIds = LawyerProfile::where('law_firm_id', $firm->id)->pluck('user_id');

        $totalEarned     = Payment::whereIn('lawyer_id', $lawyerIds)->whereIn('status', ['paid', 'downpayment_paid'])->sum('firm_cut');
        $thisMonthEarned = Payment::whereIn('lawyer_id', $lawyerIds)
            ->whereIn('status', ['paid', 'downpayment_paid'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('firm_cut');
        $thisYearEarned  = Payment::whereIn('lawyer_id', $lawyerIds)
            ->whereIn('status', ['paid', 'downpayment_paid'])
            ->whereYear('created_at', now()->year)
            ->sum('firm_cut');

        // Pending = firm's expected cut from pending payments
        $pendingAmount = Payment::whereIn('lawyer_id', $lawyerIds)
            ->where('status', 'pending')
            ->get()
            ->sum(fn($p) => $p->firm_cut ?? 0);

        $totalClients = Payment::whereIn('lawyer_id', $lawyerIds)
            ->whereIn('status', ['paid', 'downpayment_paid'])
            ->distinct('client_id')
            ->count('client_id');

        // Per-lawyer breakdown sorted by firm cut desc, with this month column
        $lawyerBreakdown = LawyerProfile::with('user')
            ->where('law_firm_id', $firm->id)
            ->get()
            ->map(function ($lp) {
                $lp->earned = Payment::where('lawyer_id', $lp->user_id)
                    ->whereIn('status', ['paid', 'downpayment_paid'])
                    ->sum('firm_cut');
                $lp->earned_this_month = Payment::where('lawyer_id', $lp->user_id)
                    ->whereIn('status', ['paid', 'downpayment_paid'])
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->sum('firm_cut');
                $lp->consultations_count = Consultation::where('lawyer_id', $lp->user_id)->count();
                return $lp;
            })
            ->sortByDesc('earned');

        return view('lawfirm.earnings', compact(
            'firm', 'totalEarned', 'thisMonthEarned', 'thisYearEarned',
            'pendingAmount', 'totalClients', 'lawyerBreakdown'
        ));
    }

    public function export()
    {
        $firm      = Auth::user()->lawFirmProfile;
        $lawyerIds = LawyerProfile::where('law_firm_id', $firm->id)->pluck('user_id');

        $payments = Payment::with(['client', 'lawyer', 'consultation'])
            ->whereIn('lawyer_id', $lawyerIds)
            ->latest()
            ->get();

        $filename = 'firm-earnings-' . now()->format('Y-m-d') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($payments) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Client', 'Lawyer', 'Consultation Code', 'Type', 'Payment Type', 'Amount', 'Firm Cut', 'Status']);
            foreach ($payments as $p) {
                $typeLabels = ['downpayment' => 'Downpayment 50%', 'balance' => 'Balance 50%', 'full' => 'Full'];
                fputcsv($handle, [
                    $p->created_at->format('M j, Y'),
                    $p->client->name ?? '',
                    $p->lawyer->name ?? '',
                    $p->consultation?->code ?? '',
                    ucfirst($p->consultation?->type ?? ''),
                    $typeLabels[$p->type] ?? ucfirst($p->type ?? ''),
                    number_format($p->amount, 2),
                    number_format($p->firm_cut, 2),
                    ucfirst(str_replace('_', ' ', $p->status)),
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
