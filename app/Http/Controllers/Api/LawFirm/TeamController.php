<?php

namespace App\Http\Controllers\Api\LawFirm;

use App\Http\Controllers\Controller;
use App\Models\LawyerProfile;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $firm = $request->user()->lawFirmProfile;

        if (!$firm) {
            return response()->json(['message' => 'Law firm profile not found.'], 404);
        }

        $team = LawyerProfile::with('user')
            ->where('law_firm_id', $firm->id)
            ->orderByDesc('rating')
            ->get()
            ->map(fn (LawyerProfile $profile) => [
                'id' => $profile->user_id,
                'profile_id' => $profile->id,
                'name' => $profile->user?->name,
                'email' => $profile->user?->email,
                'avatar_url' => $profile->user?->avatar_url,
                'specialty' => $profile->specialty,
                'firm' => $profile->firm,
                'hourly_rate' => $profile->hourly_rate,
                'experience_years' => $profile->experience_years,
                'location' => $profile->location,
                'rating' => $profile->rating,
                'reviews_count' => $profile->reviews_count,
                'is_certified' => (bool) $profile->is_certified,
                'current_status' => $profile->currentStatus(),
                'current_status_label' => $profile->currentStatusLabel(),
            ])
            ->values();

        return response()->json([
            'firm_id' => $firm->id,
            'team_count' => $team->count(),
            'members' => $team,
        ]);
    }
}
