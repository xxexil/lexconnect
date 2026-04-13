<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\LawyerBlockedDate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LawyerBlockedDateController extends Controller
{
    public function index()
    {
        $blockedDates = LawyerBlockedDate::where('lawyer_id', Auth::id())
            ->where('blocked_date', '>=', today())
            ->orderBy('blocked_date')
            ->get();

        return response()->json($blockedDates->map(fn($bd) => $bd->toScheduleArray())->values());
    }

    public function store(Request $request)
    {
        $request->validate([
            'blocked_date' => 'required|date|after_or_equal:today',
            'is_all_day'   => 'nullable|boolean',
            'start_time'   => 'nullable|required_unless:is_all_day,1|date_format:H:i',
            'end_time'     => 'nullable|required_unless:is_all_day,1|date_format:H:i|after:start_time',
            'reason'       => 'nullable|string|max:255',
        ]);

        $isAllDay = $request->boolean('is_all_day');
        $blockedDate = Carbon::parse($request->blocked_date)->toDateString();

        $sameDayBlocks = LawyerBlockedDate::where('lawyer_id', Auth::id())
            ->where('blocked_date', $blockedDate)
            ->get();

        if ($isAllDay && $sameDayBlocks->isNotEmpty()) {
            return back()
                ->withInput()
                ->withErrors(['blocked_date' => 'This date already has a blocked schedule. Remove it first before adding an all-day block.']);
        }

        if (!$isAllDay) {
            if ($sameDayBlocks->contains(fn (LawyerBlockedDate $block) => $block->isAllDay())) {
                return back()
                    ->withInput()
                    ->withErrors(['blocked_date' => 'This date is already blocked for the whole day.']);
            }

            $newStart = Carbon::parse($blockedDate . ' ' . $request->start_time);
            $newEnd = Carbon::parse($blockedDate . ' ' . $request->end_time);

            $hasOverlap = $sameDayBlocks->contains(function (LawyerBlockedDate $block) use ($blockedDate, $newStart, $newEnd) {
                if ($block->isAllDay()) {
                    return true;
                }

                $existingStart = Carbon::parse($blockedDate . ' ' . $block->start_time);
                $existingEnd = Carbon::parse($blockedDate . ' ' . $block->end_time);

                return $newStart->lt($existingEnd) && $newEnd->gt($existingStart);
            });

            if ($hasOverlap) {
                return back()
                    ->withInput()
                    ->withErrors(['start_time' => 'That time range overlaps with an existing blocked time.']);
            }
        }

        LawyerBlockedDate::firstOrCreate(
            [
                'lawyer_id' => Auth::id(),
                'blocked_date' => $blockedDate,
                'start_time' => $isAllDay ? null : $request->start_time,
                'end_time' => $isAllDay ? null : $request->end_time,
            ],
            ['reason' => $request->reason]
        );

        return back()->with('success', $isAllDay ? 'Date blocked successfully.' : 'Time blocked successfully.');
    }

    public function destroy($id)
    {
        $blocked = LawyerBlockedDate::where('id', $id)
            ->where('lawyer_id', Auth::id())
            ->firstOrFail();

        $blocked->delete();

        return back()->with('success', 'Blocked schedule removed.');
    }
}
