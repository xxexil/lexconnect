<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\LawyerBlockedDate;
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

        return response()->json($blockedDates->map(fn($bd) => [
            'id'     => $bd->id,
            'date'   => $bd->blocked_date->format('Y-m-d'),
            'reason' => $bd->reason,
        ]));
    }

    public function store(Request $request)
    {
        $request->validate([
            'blocked_date' => 'required|date|after_or_equal:today',
            'reason'       => 'nullable|string|max:255',
        ]);

        LawyerBlockedDate::updateOrCreate(
            ['lawyer_id' => Auth::id(), 'blocked_date' => $request->blocked_date],
            ['reason' => $request->reason]
        );

        return back()->with('success', 'Date blocked successfully.');
    }

    public function destroy($id)
    {
        $blocked = LawyerBlockedDate::where('id', $id)
            ->where('lawyer_id', Auth::id())
            ->firstOrFail();

        $blocked->delete();

        return back()->with('success', 'Blocked date removed.');
    }
}
