<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class LawyerEarningsController extends Controller
{
    public function index()
    {
        $user    = Auth::user();
        $profile = $user->lawyerProfile;
        $firm    = $profile?->lawFirm;

        $allPayments  = Payment::where('lawyer_id', $user->id)->latest()->get();
        $paidPayments = $allPayments->whereIn('status', ['paid', 'downpayment_paid']);

        $totalEarned   = $paidPayments->sum(fn($p) => $p->lawyer_net ?? ($p->amount - ($p->firm_cut ?? 0)));
        $thisMonth     = $paidPayments->filter(fn($p) => $p->created_at->isCurrentMonth())
                            ->sum(fn($p) => $p->lawyer_net ?? ($p->amount - ($p->firm_cut ?? 0)));
        $thisYear      = $paidPayments->filter(fn($p) => $p->created_at->isCurrentYear())
                            ->sum(fn($p) => $p->lawyer_net ?? ($p->amount - ($p->firm_cut ?? 0)));

        $pendingAmount = $allPayments->where('status', 'pending')
                            ->sum(fn($p) => $p->amount - ($p->firm_cut ?? 0));

        $totalFirmCut  = $paidPayments->sum('firm_cut');
        $totalClients  = $paidPayments->unique('client_id')->count();
        $firmCutPct    = $firm?->formatted_cut_percentage ?? null;

        return view('lawyer.earnings', compact(
            'totalEarned', 'thisMonth', 'thisYear',
            'pendingAmount', 'totalClients', 'totalFirmCut', 'firmCutPct'
        ));
    }

    public function export()
    {
        $user     = Auth::user();
        $payments = Payment::with(['client', 'consultation'])
            ->where('lawyer_id', $user->id)
            ->latest()
            ->get();

        $filename = 'earnings-' . now()->format('Y-m-d') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($payments) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Client', 'Consultation Code', 'Type', 'Payment Type', 'Amount', 'Firm Cut', 'Your Net', 'Status']);
            foreach ($payments as $p) {
                $typeLabels = ['downpayment' => 'Downpayment 50%', 'balance' => 'Balance 50%', 'full' => 'Full'];
                fputcsv($handle, [
                    $p->created_at->format('M j, Y'),
                    $p->client->name ?? '',
                    $p->consultation?->code ?? '',
                    ucfirst($p->consultation?->type ?? ''),
                    $typeLabels[$p->type] ?? ucfirst($p->type ?? ''),
                    number_format($p->amount, 2),
                    $p->firm_cut > 0 ? number_format($p->firm_cut, 2) : '',
                    number_format($p->lawyer_net ?? ($p->amount - ($p->firm_cut ?? 0)), 2),
                    $p->status === 'downpayment_paid' ? 'Paid (Down)' : ucfirst(str_replace('_', ' ', $p->status)),
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
