<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LawyerProfile;
use App\Models\Review;
use App\Models\Consultation;
use Illuminate\Http\Request;

class LawyerController extends Controller
{
    public function index(Request $request)
    {
        $query = LawyerProfile::with('user:id,name,avatar,email');

        if ($request->filled('specialty')) {
            $query->where('specialty', 'like', '%' . $request->specialty . '%');
        }
        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }
        if ($request->filled('min_rate')) {
            $query->where('hourly_rate', '>=', $request->min_rate);
        }
        if ($request->filled('max_rate')) {
            $query->where('hourly_rate', '<=', $request->max_rate);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"))
                ->orWhere('specialty', 'like', "%{$search}%")
                ->orWhere('location', 'like', "%{$search}%");
        }

        $sort = $request->get('sort', 'rating');
        match ($sort) {
            'rate_asc'  => $query->orderBy('hourly_rate'),
            'rate_desc' => $query->orderByDesc('hourly_rate'),
            'experience'=> $query->orderByDesc('experience_years'),
            default     => $query->orderByDesc('rating'),
        };

        $lawyers = $query->paginate(15)->through(fn($lp) => [
            'id'                  => $lp->user_id,
            'profile_id'          => $lp->id,
            'name'                => $lp->user->name,
            'avatar_url'          => $lp->user->avatar_url,
            'specialty'           => $lp->specialty,
            'location'            => $lp->location,
            'hourly_rate'         => $lp->hourly_rate,
            'experience_years'    => $lp->experience_years,
            'rating'              => $lp->rating,
            'reviews_count'       => $lp->reviews_count,
            'availability_status' => $lp->currentStatus(),
            'is_certified'        => $lp->is_certified,
        ]);

        return response()->json($lawyers);
    }

    public function show(Request $request, $id)
    {
        $user = User::with(['lawyerProfile'])->findOrFail($id);
        $lp = $user->lawyerProfile;

        $reviews = Review::where('lawyer_id', $id)
            ->with('client:id,name,avatar')
            ->orderByDesc('created_at')
            ->take(10)
            ->get()
            ->map(fn($r) => [
                'id'           => $r->id,
                'rating'       => $r->rating,
                'comment'      => $r->comment,
                'created_at'   => $r->created_at->toDateString(),
                'client_name'  => $r->client->name,
                'client_avatar'=> $r->client->avatar_url,
            ]);

        // Check if the authenticated client can review this lawyer
        $canReview = false;
        $reviewableConsultationId = null;
        $authUser = $request->user();
        if ($authUser && $authUser->role === 'client') {
            $reviewable = Consultation::where('client_id', $authUser->id)
                ->where('lawyer_id', $id)
                ->where('status', 'completed')
                ->whereDoesntHave('review')
                ->orderByDesc('scheduled_at')
                ->first();
            $canReview = $reviewable !== null;
            $reviewableConsultationId = $reviewable?->id;
        }

        return response()->json([
            'id'                        => $user->id,
            'name'                      => $user->name,
            'email'                     => $user->email,
            'avatar_url'                => $user->avatar_url,
            'phone'                     => $user->phone,
            'bio'                       => $user->bio,
            'specialty'                 => $lp?->specialty,
            'location'                  => $lp?->location,
            'hourly_rate'               => $lp?->hourly_rate,
            'experience_years'          => $lp?->experience_years,
            'rating'                    => $lp?->rating,
            'reviews_count'             => $lp?->reviews_count,
            'availability_status'       => $lp?->currentStatus(),
            'is_certified'              => $lp?->is_certified,
            'firm'                      => $lp?->firm,
            'reviews'                   => $reviews,
            'can_review'                => $canReview,
            'reviewable_consultation_id'=> $reviewableConsultationId,
        ]);
    }
}
