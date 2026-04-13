<?php

namespace App\Http\Controllers\Api\LawFirm;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\FirmApplication;
use App\Models\LawyerProfile;
use App\Models\Payment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $firm = $user->lawFirmProfile;

        if (!$firm) {
            return response()->json(['message' => 'Law firm profile not found.'], 404);
        }

        $teamMembers = LawyerProfile::with('user')
            ->where('law_firm_id', $firm->id)
            ->get();
        $lawyerIds = $teamMembers->pluck('user_id');

        $recentConsultations = Consultation::with(['client:id,name,avatar', 'lawyer:id,name,avatar'])
            ->whereIn('lawyer_id', $lawyerIds)
            ->latest()
            ->take(6)
            ->get()
            ->map(fn (Consultation $consultation) => [
                'id' => $consultation->id,
                'code' => $consultation->code,
                'status' => $consultation->status,
                'type' => $consultation->type,
                'scheduled_at' => $consultation->scheduled_at,
                'duration_minutes' => $consultation->duration_minutes,
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
            ]);

        return response()->json([
            'firm' => [
                'id' => $firm->id,
                'firm_name' => $firm->firm_name,
                'tagline' => $firm->tagline,
                'city' => $firm->city,
                'phone' => $firm->phone,
                'website' => $firm->website,
                'firm_size' => $firm->firm_size,
                'firm_size_label' => $firm->firm_size_label,
                'is_verified' => (bool) $firm->is_verified,
                'rating' => $firm->rating,
                'reviews_count' => $firm->reviews_count,
                'logo_url' => $user->avatar_url,
            ],
            'stats' => [
                'team_count' => $teamMembers->count(),
                'active_count' => $teamMembers->filter(fn (LawyerProfile $profile) => $profile->currentStatus() === 'active')->count(),
                'pending_applications' => FirmApplication::where('law_firm_id', $firm->id)->where('status', 'pending')->count(),
                'total_consultations' => Consultation::whereIn('lawyer_id', $lawyerIds)->count(),
                'total_earned' => Payment::whereIn('lawyer_id', $lawyerIds)->where('status', 'paid')->sum('amount'),
                'this_month_earned' => Payment::whereIn('lawyer_id', $lawyerIds)
                    ->where('status', 'paid')
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->sum('amount'),
            ],
            'team_members' => $teamMembers->take(4)->map(fn (LawyerProfile $profile) => [
                'id' => $profile->user_id,
                'name' => $profile->user?->name,
                'avatar_url' => $profile->user?->avatar_url,
                'specialty' => $profile->specialty,
                'current_status' => $profile->currentStatus(),
                'current_status_label' => $profile->currentStatusLabel(),
                'rating' => $profile->rating,
                'reviews_count' => $profile->reviews_count,
            ])->values(),
            'recent_consultations' => $recentConsultations->values(),
        ]);
    }
}
