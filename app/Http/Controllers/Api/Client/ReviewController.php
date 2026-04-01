<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Consultation;
use App\Models\LawyerProfile;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'consultation_id' => 'required|exists:consultations,id',
            'rating'          => 'required|integer|between:1,5',
            'comment'         => 'nullable|string|max:1000',
        ]);

        $user = $request->user();
        $consultation = Consultation::where('client_id', $user->id)
            ->where('status', 'completed')
            ->findOrFail($request->consultation_id);

        if (Review::where('consultation_id', $consultation->id)->exists()) {
            return response()->json(['message' => 'You have already reviewed this consultation.'], 422);
        }

        $review = Review::create([
            'client_id'       => $user->id,
            'lawyer_id'       => $consultation->lawyer_id,
            'consultation_id' => $consultation->id,
            'rating'          => $request->rating,
            'comment'         => $request->comment,
        ]);

        // Recalculate lawyer's aggregate rating
        $lp = LawyerProfile::where('user_id', $consultation->lawyer_id)->first();
        if ($lp) {
            $avg = Review::where('lawyer_id', $consultation->lawyer_id)->avg('rating');
            $count = Review::where('lawyer_id', $consultation->lawyer_id)->count();
            $lp->update(['rating' => round($avg, 2), 'reviews_count' => $count]);
        }

        return response()->json(['message' => 'Review submitted successfully.', 'review' => $review], 201);
    }
}
