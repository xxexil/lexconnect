<?php

namespace App\Http\Controllers\Api\LawFirm;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\LawyerProfile;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function index(Request $request)
    {
        $firm = $request->user()->lawFirmProfile;

        if (!$firm) {
            return response()->json(['message' => 'Law firm profile not found.'], 404);
        }

        $lawyerIds = LawyerProfile::where('law_firm_id', $firm->id)->pluck('user_id');

        $mapConsultation = fn (Consultation $consultation) => [
            'id' => $consultation->id,
            'code' => $consultation->code,
            'status' => $consultation->status,
            'type' => $consultation->type,
            'scheduled_at' => $consultation->scheduled_at,
            'duration_minutes' => $consultation->duration_minutes,
            'duration_label' => $consultation->duration_label,
            'price' => $consultation->price,
            'client' => $consultation->client ? [
                'id' => $consultation->client->id,
                'name' => $consultation->client->name,
                'avatar_url' => $consultation->client->avatar_url,
            ] : null,
            'lawyer' => $consultation->lawyer ? [
                'id' => $consultation->lawyer->id,
                'name' => $consultation->lawyer->name,
                'avatar_url' => $consultation->lawyer->avatar_url,
            ] : null,
            'payment' => $consultation->payment ? [
                'id' => $consultation->payment->id,
                'status' => $consultation->payment->status,
                'amount' => $consultation->payment->amount,
                'firm_cut' => $consultation->payment->firm_cut,
            ] : null,
        ];

        $pending = Consultation::with(['client:id,name,avatar', 'lawyer:id,name,avatar', 'payment'])
            ->whereIn('lawyer_id', $lawyerIds)
            ->where('status', 'pending')
            ->whereHas('payment', function ($query) {
                $query->where('status', 'downpayment_paid');
            })
            ->latest()
            ->get()
            ->map($mapConsultation)
            ->values();

        $upcoming = Consultation::with(['client:id,name,avatar', 'lawyer:id,name,avatar', 'payment'])
            ->whereIn('lawyer_id', $lawyerIds)
            ->where('status', 'upcoming')
            ->orderBy('scheduled_at')
            ->get()
            ->map($mapConsultation)
            ->values();

        $completed = Consultation::with(['client:id,name,avatar', 'lawyer:id,name,avatar', 'payment'])
            ->whereIn('lawyer_id', $lawyerIds)
            ->where('status', 'completed')
            ->latest()
            ->get()
            ->map($mapConsultation)
            ->values();

        $cancelled = Consultation::with(['client:id,name,avatar', 'lawyer:id,name,avatar', 'payment'])
            ->whereIn('lawyer_id', $lawyerIds)
            ->where('status', 'cancelled')
            ->latest()
            ->get()
            ->map($mapConsultation)
            ->values();

        return response()->json([
            'firm_id' => $firm->id,
            'counts' => [
                'pending' => $pending->count(),
                'upcoming' => $upcoming->count(),
                'completed' => $completed->count(),
                'cancelled' => $cancelled->count(),
            ],
            'pending' => $pending,
            'upcoming' => $upcoming,
            'completed' => $completed,
            'cancelled' => $cancelled,
        ]);
    }
}
