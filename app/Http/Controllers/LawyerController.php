<?php

namespace App\Http\Controllers;

use App\Models\LawyerProfile;
use App\Models\LawyerBlockedDate;
use App\Models\Review;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class LawyerController extends Controller
{
    public function show($id) {
        // Lawyers and law firms should not be able to browse client-facing pages
        if (auth()->check() && in_array(auth()->user()->role, ['lawyer', 'law_firm'])) {
            abort(403, 'Access denied. This page is for clients only.');
        }

        $profile = LawyerProfile::with(['user'])->where('user_id', $id)->firstOrFail();
        $reviews = Review::with('client')
            ->where('lawyer_id', $id)
            ->latest()
            ->get();
        $blockedDates = LawyerBlockedDate::where('lawyer_id', $id)
            ->where('blocked_date', '>=', today())
            ->get()
            ->filter(fn(LawyerBlockedDate $blockedDate) => $blockedDate->isAllDay())
            ->pluck('blocked_date')
            ->map(fn($d) => $d->format('Y-m-d'));
        return view('lawyer-public-profile', compact('profile', 'reviews', 'blockedDates'));
    }

    public function index(Request $request) {
        $query = LawyerProfile::with(['user', 'nextConsultation', 'upcomingConsultations'])
            ->whereHas('user');

        if ($request->filled('specialty')) {
            $query->where('specialty', $request->specialty);
        }
        if ($request->filled('location')) {
            $query->where('location', $request->location);
        }
        if ($request->filled('min_rate')) {
            $query->where('hourly_rate', '>=', $request->min_rate);
        }
        if ($request->filled('max_rate')) {
            $query->where('hourly_rate', '<=', $request->max_rate);
        }
        if ($request->filled('min_experience')) {
            $query->where('experience_years', '>=', $request->min_experience);
        }
        if ($request->filled('min_rating')) {
            $query->where('rating', '>=', $request->min_rating);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', fn($u) => $u->where('name','like',"%$search%"))
                  ->orWhere('specialty','like',"%$search%")
                  ->orWhere('firm','like',"%$search%")
                  ->orWhere('location','like',"%$search%");
            });
        }

        $sort = $request->get('sort', 'rating');
        match($sort) {
            'rate_asc'  => $query->orderBy('hourly_rate', 'asc'),
            'rate_desc' => $query->orderBy('hourly_rate', 'desc'),
            'reviews'   => $query->orderBy('reviews_count', 'desc'),
            default     => $query->orderBy('rating', 'desc'),
        };

        if ($request->filled('availability')) {
            $requestedStatus = $request->availability === 'available' ? 'active' : $request->availability;
            $filteredLawyers = $query->get()
                ->filter(fn (LawyerProfile $profile) => $profile->currentStatus() === $requestedStatus)
                ->values();

            $page = LengthAwarePaginator::resolveCurrentPage();
            $lawyers = new LengthAwarePaginator(
                $filteredLawyers->forPage($page, 9)->values(),
                $filteredLawyers->count(),
                9,
                $page,
                ['path' => LengthAwarePaginator::resolveCurrentPath()]
            );
            $lawyers->withQueryString();
        } else {
            $lawyers = $query->paginate(9)->withQueryString();
        }
        $specialties = LawyerProfile::query()
            ->whereNotNull('specialty')
            ->where('specialty', '!=', '')
            ->distinct()
            ->orderBy('specialty')
            ->pluck('specialty');

        $locations = LawyerProfile::query()
            ->whereNotNull('location')
            ->where('location', '!=', '')
            ->distinct()
            ->orderBy('location')
            ->pluck('location');

        // Get blocked dates for all displayed lawyers
        $lawyerIds = $lawyers->pluck('user_id')->toArray();
        $blockedDates = LawyerBlockedDate::whereIn('lawyer_id', $lawyerIds)
            ->where('blocked_date', '>=', today())
            ->get()
            ->groupBy('lawyer_id')
            ->map(fn($dates) => $dates->map(fn(LawyerBlockedDate $blockedDate) => $blockedDate->toScheduleArray())->values());

        // Precompute free slots for each lawyer
        $lawyerSlots = [];
        $workHours = [9, 10, 11, 13, 14, 15, 16, 17];
        foreach ($lawyers as $lp) {
            $bookedWindows = $lp->upcomingConsultations->map(fn($c) => [
                'start' => \Carbon\Carbon::parse($c->scheduled_at),
                'end'   => \Carbon\Carbon::parse($c->scheduled_at)->addMinutes($c->duration_minutes ?? 60),
            ]);
            $lawyerBlocked = isset($blockedDates[$lp->user_id]) ? collect($blockedDates[$lp->user_id]) : collect();
            $allDayBlockedDates = $lawyerBlocked->where('is_all_day', true)->pluck('date')->toArray();
            $blockedWindows = $lawyerBlocked
                ->where('is_all_day', false)
                ->map(fn($blockedDate) => [
                    'start' => \Carbon\Carbon::parse($blockedDate['date'] . ' ' . $blockedDate['start_time']),
                    'end' => \Carbon\Carbon::parse($blockedDate['date'] . ' ' . $blockedDate['end_time']),
                ]);
            $freeSlots = [];
            for ($__d = 0; $__d <= 7 && count($freeSlots) < 3; $__d++) {
                $__dayDate = \Carbon\Carbon::today()->addDays($__d)->format('Y-m-d');
                if (in_array($__dayDate, $allDayBlockedDates)) continue;
                foreach ($workHours as $__hr) {
                    if (count($freeSlots) >= 3) break;
                    $__slot = \Carbon\Carbon::today()->addDays($__d)->setHour($__hr)->setMinute(0)->setSecond(0);
                    if ($__slot->isPast()) continue;
                    $__slotEnd = $__slot->copy()->addHour();
                    $__free = true;
                    foreach ($bookedWindows as $__bw) {
                        if ($__slot->lt($__bw['end']) && $__slotEnd->gt($__bw['start'])) {
                            $__free = false; break;
                        }
                    }
                    if ($__free) {
                        foreach ($blockedWindows as $__bw) {
                            if ($__slot->lt($__bw['end']) && $__slotEnd->gt($__bw['start'])) {
                                $__free = false; break;
                            }
                        }
                    }
                    if ($__free) $freeSlots[] = $__slot;
                }
            }
            $lawyerSlots[$lp->user_id] = $freeSlots;
        }

        return view('find-lawyers', compact('lawyers', 'specialties', 'locations', 'blockedDates', 'lawyerSlots'));
    }
}
