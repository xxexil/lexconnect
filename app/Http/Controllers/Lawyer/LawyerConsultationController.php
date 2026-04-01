<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class LawyerConsultationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        Consultation::expireOverdue('lawyer_id', $user->id);

        $pending   = Consultation::with(['client', 'payment'])
            ->where('lawyer_id', $user->id)
            ->where('status', 'pending')
            ->whereHas('payment', function($q) {
                $q->where('status', 'downpayment_paid');
            })
            ->orderBy('created_at', 'desc')
            ->get();
        $upcoming  = Consultation::with('client')->where('lawyer_id', $user->id)->where('status', 'upcoming')->orderBy('scheduled_at')->get();
        $completed = Consultation::with('client')->where('lawyer_id', $user->id)->where('status', 'completed')->orderBy('scheduled_at', 'desc')->get();
        $cancelled = Consultation::with('client')->where('lawyer_id', $user->id)->where('status', 'cancelled')->orderBy('updated_at', 'desc')->get();
        $expired   = Consultation::with('client')->where('lawyer_id', $user->id)->where('status', 'expired')->orderBy('scheduled_at', 'desc')->get();

        return view('lawyer.consultations', compact('pending', 'upcoming', 'completed', 'cancelled', 'expired'));
    }

    public function accept($id)
    {
        $c = Consultation::where('lawyer_id', Auth::id())->where('status', 'pending')->findOrFail($id);
        $c->update(['status' => 'upcoming']);
        return back()->with('success', 'Consultation accepted. The client will be notified.');
    }

    public function decline($id)
    {
        $c = Consultation::where('lawyer_id', Auth::id())->where('status', 'pending')->findOrFail($id);
        $c->update(['status' => 'cancelled']);
        // Mark associated payment as refunded
        Payment::where('consultation_id', $c->id)->update(['status' => 'refunded']);
        return back()->with('success', 'Consultation declined.');
    }

    public function complete($id)
    {
        $c = Consultation::where('lawyer_id', Auth::id())->where('status', 'upcoming')->findOrFail($id);
        $c->update(['status' => 'completed']);
        // Mark payment as paid if not already
        Payment::where('consultation_id', $c->id)->where('status', 'pending')->update(['status' => 'paid']);
        return back()->with('success', 'Consultation marked as completed.');
    }
}
