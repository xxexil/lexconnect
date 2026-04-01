<?php

namespace App\Http\Controllers\Api\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class EarningsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'lawyer') {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $total     = Payment::where('lawyer_id', $user->id)->where('status', 'paid')->sum('lawyer_net');
        $firmCut   = Payment::where('lawyer_id', $user->id)->where('status', 'paid')->sum('firm_cut');
        $pending   = Payment::where('lawyer_id', $user->id)->where('status', 'pending')->sum('amount');
        $thisMonth = Payment::where('lawyer_id', $user->id)
            ->where('status', 'paid')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->sum('lawyer_net');

        $recentPayments = Payment::where('lawyer_id', $user->id)
            ->where('status', 'paid')
            ->with(['consultation:id,code,scheduled_at', 'client:id,name'])
            ->orderByDesc('updated_at')
            ->take(15)
            ->get()
            ->map(fn($p) => [
                'id'           => $p->id,
                'amount'       => $p->lawyer_net,
                'type'         => $p->type,
                'date'         => $p->updated_at->toDateString(),
                'client_name'  => $p->client?->name,
                'consult_code' => $p->consultation?->code,
            ]);

        return response()->json([
            'total_earned'   => $total,
            'firm_cut_total' => $firmCut,
            'pending'        => $pending,
            'this_month'     => $thisMonth,
            'recent_payments'=> $recentPayments,
        ]);
    }
}
