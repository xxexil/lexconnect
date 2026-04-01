<?php

namespace App\Http\Controllers;

use App\Models\LawyerProfile;
use App\Models\LawyerBlockedDate;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;

class LawyerController extends Controller
{
    public function show($id) {
        $profile = LawyerProfile::with(['user'])->where('user_id', $id)->firstOrFail();
        $reviews = Review::with('client')
            ->where('lawyer_id', $id)
            ->latest()
            ->get();
        $blockedDates = LawyerBlockedDate::where('lawyer_id', $id)
            ->where('blocked_date', '>=', today())
            ->pluck('blocked_date')
            ->map(fn($d) => $d->format('Y-m-d'));
        return view('lawyer-public-profile', compact('profile', 'reviews', 'blockedDates'));
    }

    public function index(Request $request) {
        $query = LawyerProfile::with(['user', 'nextConsultation', 'upcomingConsultations']);

        if ($request->filled('specialty')) {
            $query->where('specialty', $request->specialty);
        }
        if ($request->filled('min_rate')) {
            $query->where('hourly_rate', '>=', $request->min_rate);
        }
        if ($request->filled('max_rate') && $request->max_rate < 1000) {
            $query->where('hourly_rate', '<=', $request->max_rate);
        }
        if ($request->filled('min_experience')) {
            $query->where('experience_years', '>=', $request->min_experience);
        }
        if ($request->filled('min_rating')) {
            $query->where('rating', '>=', $request->min_rating);
        }
        if ($request->filled('availability')) {
            $query->where('availability_status', $request->availability);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', fn($u) => $u->where('name','like',"%$search%"))
                  ->orWhere('specialty','like',"%$search%")
                  ->orWhere('firm','like',"%$search%");
            });
        }

        $sort = $request->get('sort', 'rating');
        match($sort) {
            'rate_asc'  => $query->orderBy('hourly_rate', 'asc'),
            'rate_desc' => $query->orderBy('hourly_rate', 'desc'),
            'reviews'   => $query->orderBy('reviews_count', 'desc'),
            default     => $query->orderBy('rating', 'desc'),
        };

        $lawyers     = $query->paginate(9)->withQueryString();
        $specialties = LawyerProfile::distinct()->orderBy('specialty')->pluck('specialty');

        // Get blocked dates for all displayed lawyers
        $lawyerIds = $lawyers->pluck('user_id')->toArray();
        $blockedDates = LawyerBlockedDate::whereIn('lawyer_id', $lawyerIds)
            ->where('blocked_date', '>=', today())
            ->get()
            ->groupBy('lawyer_id')
            ->map(fn($dates) => $dates->map(fn($d) => [
                'date'   => $d->blocked_date->format('Y-m-d'),
                'reason' => $d->reason,
            ]));

        // Precompute free slots for each lawyer
        $lawyerSlots = [];
        $workHours = [9, 10, 11, 13, 14, 15, 16, 17];
        foreach ($lawyers as $lp) {
            $bookedWindows = $lp->upcomingConsultations->map(fn($c) => [
                'start' => \Carbon\Carbon::parse($c->scheduled_at),
                'end'   => \Carbon\Carbon::parse($c->scheduled_at)->addMinutes($c->duration_minutes ?? 60),
            ]);
            $lawyerBlocked = isset($blockedDates[$lp->user_id]) ? collect($blockedDates[$lp->user_id])->pluck('date')->toArray() : [];
            $freeSlots = [];
            for ($__d = 0; $__d <= 7 && count($freeSlots) < 3; $__d++) {
                $__dayDate = \Carbon\Carbon::today()->addDays($__d)->format('Y-m-d');
                if (in_array($__dayDate, $lawyerBlocked)) continue;
                foreach ($workHours as $__hr) {
                    if (count($freeSlots) >= 3) break;
                    $__slot = \Carbon\Carbon::today()->addDays($__d)->setHour($__hr)->setMinute(0)->setSecond(0);
                    if ($__slot->isPast()) continue;
                    $__free = true;
                    foreach ($bookedWindows as $__bw) {
                        if ($__slot->lt($__bw['end']) && $__slot->copy()->addHour()->gt($__bw['start'])) {
                            $__free = false; break;
                        }
                    }
                    if ($__free) $freeSlots[] = $__slot;
                }
            }
            $lawyerSlots[$lp->user_id] = $freeSlots;
        }

        return view('find-lawyers', compact('lawyers', 'specialties', 'blockedDates', 'lawyerSlots'));
    }
}
