<?php

namespace App\Http\Controllers\Api\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\LawyerProfile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $lp = $user->lawyerProfile;

        return response()->json([
            'id'                  => $user->id,
            'name'                => $user->name,
            'email'               => $user->email,
            'phone'               => $user->phone,
            'bio'                 => $user->bio,
            'avatar_url'          => $user->avatar_url,
            'specialty'           => $lp?->specialty,
            'firm'                => $lp?->firm,
            'hourly_rate'         => $lp?->hourly_rate,
            'experience_years'    => $lp?->experience_years,
            'location'            => $lp?->location,
            'availability_status' => $lp?->currentStatus(),
            'is_certified'        => $lp?->is_certified,
            'rating'              => $lp?->rating,
            'reviews_count'       => $lp?->reviews_count,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'             => 'sometimes|string|max:100',
            'phone'            => 'sometimes|nullable|string|max:20',
            'bio'              => 'sometimes|nullable|string|max:1000',
            'specialty'        => 'sometimes|string|max:100',
            'location'         => 'sometimes|nullable|string|max:150',
            'hourly_rate'      => 'sometimes|numeric|min:0',
            'experience_years' => 'sometimes|integer|min:0',
        ]);

        $user = $request->user();
        $user->update($request->only(['name', 'phone', 'bio']));

        $lp = $user->lawyerProfile;
        if ($lp) {
            $lp->update($request->only(['specialty', 'location', 'hourly_rate', 'experience_years']));
        }

        $lp = $user->fresh()->lawyerProfile;

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user'    => [
                'id'                  => $user->id,
                'name'                => $user->name,
                'email'               => $user->email,
                'phone'               => $user->phone,
                'bio'                 => $user->bio,
                'avatar_url'          => $user->avatar_url,
                'specialty'           => $lp?->specialty,
                'firm'                => $lp?->firm,
                'hourly_rate'         => $lp?->hourly_rate,
                'experience_years'    => $lp?->experience_years,
                'location'            => $lp?->location,
                'availability_status' => $lp?->currentStatus(),
                'is_certified'        => $lp?->is_certified,
                'rating'              => $lp?->rating,
                'reviews_count'       => $lp?->reviews_count,
            ],
        ]);
    }

    public function updateAvailability(Request $request)
    {
        $user = $request->user();
        $lp = $user->lawyerProfile;

        if (!$lp) {
            return response()->json(['message' => 'Lawyer profile not found.'], 404);
        }

        return response()->json([
            'message' => 'Availability is now automatic.',
            'status' => $lp->currentStatus(),
        ]);
    }
}
