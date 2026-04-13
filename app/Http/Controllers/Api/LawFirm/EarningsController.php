<?php

namespace App\Http\Controllers\Api\LawFirm;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\LawyerProfile;
use App\Models\Payment;
use Illuminate\Http\Request;

class EarningsController extends Controller
{
    public function index(Request $request)
    {
        $firm = $request->user()->lawFirmProfile;

        if (!$firm) {
            return response()->json(['message' => 'Law firm profile not found.'], 404);
        }

        $lawyerProfiles = LawyerProfile::with('user')
            ->where('law_firm_id', $firm->id)
            ->get();
        $lawyerIds = $lawyerProfiles->pluck('user_id');

        $payments = Payment::with(['lawyer:id,name,avatar', 'client:id,name,avatar', 'consultation:id,code,scheduled_at,type'])
            ->whereIn('lawyer_id', $lawyerIds)
            ->latest()
            ->get();

        $lawyerBreakdown = $lawyerProfiles->map(function (LawyerProfile $profile) {
            return [
                'id' => $profile->user_id,
                'name' => $profile->user?->name,
                'avatar_url' => $profile->user?->avatar_url,
                'specialty' => $profile->specialty,
                'earned' => Payment::where('lawyer_id', $profile->user_id)
                    ->whereIn('status', ['paid', 'downpayment_paid'])
                    ->sum('firm_cut'),
                'consultations_count' => Consultation::where('lawyer_id', $profile->user_id)->count(),
            ];
        })->values();

        return response()->json([
            'firm_id' => $firm->id,
            'stats' => [
                'total_earned' => Payment::whereIn('lawyer_id', $lawyerIds)->whereIn('status', ['paid', 'downpayment_paid'])->sum('firm_cut'),
                'this_month_earned' => Payment::whereIn('lawyer_id', $lawyerIds)
                    ->whereIn('status', ['paid', 'downpayment_paid'])
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->sum('firm_cut'),
                'pending_amount' => Payment::whereIn('lawyer_id', $lawyerIds)->where('status', 'pending')->sum('amount'),
                'total_clients' => Payment::whereIn('lawyer_id', $lawyerIds)
                    ->where('status', 'paid')
                    ->distinct('client_id')
                    ->count('client_id'),
            ],
            'payments' => $payments->map(fn (Payment $payment) => [
                'id' => $payment->id,
                'amount' => $payment->amount,
                'status' => $payment->status,
                'type' => $payment->type,
                'firm_cut' => $payment->firm_cut,
                'lawyer_net' => $payment->lawyer_net,
                'created_at' => $payment->created_at,
                'client' => $payment->client ? [
                    'id' => $payment->client->id,
                    'name' => $payment->client->name,
                    'avatar_url' => $payment->client->avatar_url,
                ] : null,
                'lawyer' => $payment->lawyer ? [
                    'id' => $payment->lawyer->id,
                    'name' => $payment->lawyer->name,
                    'avatar_url' => $payment->lawyer->avatar_url,
                ] : null,
                'consultation' => $payment->consultation ? [
                    'id' => $payment->consultation->id,
                    'code' => $payment->consultation->code,
                    'scheduled_at' => $payment->consultation->scheduled_at,
                    'type' => $payment->consultation->type,
                ] : null,
            ])->values(),
            'lawyer_breakdown' => $lawyerBreakdown,
        ]);
    }
}
