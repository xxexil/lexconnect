<?php

namespace App\Http\Controllers\LawFirm;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\LawyerProfile;
use Illuminate\Support\Facades\Auth;

class LawFirmConsultationController extends Controller
{
    public function index()
    {
        $firm      = Auth::user()->lawFirmProfile;
        $lawyerIds = LawyerProfile::where('law_firm_id', $firm->id)->pluck('user_id');

        $pending   = Consultation::with(['client', 'lawyer', 'payment'])
            ->whereIn('lawyer_id', $lawyerIds)
            ->where('status', 'pending')
            ->whereHas('payment', function ($q) {
                $q->where('status', 'downpayment_paid');
            })
            ->latest()
            ->get();
        $upcoming  = Consultation::with(['client', 'lawyer'])->whereIn('lawyer_id', $lawyerIds)->where('status', 'upcoming')->orderBy('scheduled_at')->get();
        $completed = Consultation::with(['client', 'lawyer'])->whereIn('lawyer_id', $lawyerIds)->where('status', 'completed')->latest()->get();
        $cancelled = Consultation::with(['client', 'lawyer'])->whereIn('lawyer_id', $lawyerIds)->where('status', 'cancelled')->latest()->get();

        return view('lawfirm.consultations', compact('firm', 'pending', 'upcoming', 'completed', 'cancelled'));
    }
}
