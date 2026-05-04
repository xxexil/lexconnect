<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function index(Request $request) {
        $user = Auth::user();

        $allPayments = Payment::with(['lawyer', 'consultation'])
            ->where('client_id', $user->id)
            ->latest()
            ->get();

        $paymentsQuery = Payment::with(['lawyer', 'consultation'])
            ->where('client_id', $user->id);

        if ($search = trim((string) $request->input('search'))) {
            $paymentsQuery->where(function ($query) use ($search) {
                $query->whereHas('lawyer', function ($lawyerQuery) use ($search) {
                    $lawyerQuery->where('name', 'like', '%' . $search . '%');
                })->orWhereHas('consultation', function ($consultationQuery) use ($search) {
                    $consultationQuery->where('code', 'like', '%' . $search . '%')
                        ->orWhere('type', 'like', '%' . $search . '%');
                })->orWhere('type', 'like', '%' . $search . '%')
                  ->orWhere('status', 'like', '%' . $search . '%');
            });
        }

        if ($status = $request->input('status')) {
            $paymentsQuery->where('status', $status);
        }

        if ($type = $request->input('type')) {
            $paymentsQuery->where('type', $type);
        }

        if ($dateFrom = $request->input('date_from')) {
            $paymentsQuery->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->input('date_to')) {
            $paymentsQuery->whereDate('created_at', '<=', $dateTo);
        }

        $payments = $paymentsQuery
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $totalSpent = $allPayments->whereIn('status', ['paid', 'downpayment_paid'])->sum('amount');
        $totalPending = $allPayments->where('status', 'pending')->sum('amount');
        $totalRefunded = $allPayments->where('status', 'refunded')->sum('amount');
        $transactionCount = $allPayments->count();

        $thisMonthSpent = Payment::where('client_id', $user->id)
            ->whereIn('status', ['paid', 'downpayment_paid'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        return view('payments', compact('payments', 'totalSpent', 'totalPending', 'totalRefunded', 'transactionCount', 'thisMonthSpent'));
    }
}
