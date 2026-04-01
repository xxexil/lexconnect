<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function index() {
        $user     = Auth::user();
        $payments = Payment::with(['lawyer','consultation'])
            ->where('client_id', $user->id)
            ->latest()
            ->get();
        $totalSpent    = $payments->whereIn('status', ['paid', 'downpayment_paid'])->sum('amount');
        $totalPending  = $payments->where('status', 'pending')->sum('amount');
        $totalRefunded = $payments->where('status', 'refunded')->sum('amount');
        return view('payments', compact('payments','totalSpent','totalPending','totalRefunded'));
    }
}
