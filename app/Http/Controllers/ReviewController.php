<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Store a new review (client only, for their own completed consultation).
     */
    public function store(Request $request)
    {
        $request->validate([
            'consultation_id' => 'required|exists:consultations,id',
            'rating'          => 'required|integer|min:1|max:5',
            'comment'         => 'nullable|string|max:1000',
        ]);

        $consultation = \App\Models\Consultation::findOrFail($request->consultation_id);

        if ($consultation->client_id !== Auth::id()) abort(403);
        if ($consultation->status !== 'completed') {
            return back()->with('error', 'You can only review completed consultations.');
        }

        // Prevent duplicate review
        if (\App\Models\Review::where('consultation_id', $consultation->id)->exists()) {
            return back()->with('error', 'You have already reviewed this consultation.');
        }

        \App\Models\Review::create([
            'client_id'       => Auth::id(),
            'lawyer_id'       => $consultation->lawyer_id,
            'consultation_id' => $consultation->id,
            'rating'          => $request->rating,
            'comment'         => $request->comment,
        ]);

        // Recalculate lawyer's aggregate rating
        $avg   = \App\Models\Review::where('lawyer_id', $consultation->lawyer_id)->avg('rating');
        $count = \App\Models\Review::where('lawyer_id', $consultation->lawyer_id)->count();
        \App\Models\LawyerProfile::where('user_id', $consultation->lawyer_id)
            ->update(['rating' => round($avg, 1), 'reviews_count' => $count]);

        return back()->with('success', 'Thank you for your review!');
    }
}
