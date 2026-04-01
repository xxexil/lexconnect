<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\FirmApplication;
use App\Models\LawFirmProfile;
use App\Models\LawyerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LawyerFirmController extends Controller
{
    public function index()
    {
        $user    = Auth::user();
        $profile = $user->lawyerProfile;

        $currentFirm = $profile->law_firm_id
            ? LawFirmProfile::find($profile->law_firm_id)
            : null;

        $myApplications = FirmApplication::with('lawFirm')
            ->where('lawyer_id', $user->id)
            ->latest()
            ->get();

        $appliedFirmIds = $myApplications->whereIn('status', ['pending', 'accepted'])->pluck('law_firm_id');

        $firms = LawFirmProfile::with('user')
            ->withCount('lawyers')
            ->whereNotIn('id', $appliedFirmIds)
            ->when($profile->law_firm_id, fn($q) => $q->where('id', '!=', $profile->law_firm_id))
            ->latest()
            ->get();

        return view('lawyer.firms', compact('profile', 'currentFirm', 'myApplications', 'firms'));
    }

    public function apply(Request $request)
    {
        $request->validate([
            'law_firm_id' => 'required|exists:law_firm_profiles,id',
            'message'     => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        $existing = FirmApplication::where('lawyer_id', $user->id)
            ->where('law_firm_id', $request->law_firm_id)
            ->whereIn('status', ['pending', 'accepted'])
            ->first();

        if ($existing) {
            return back()->withErrors(['message' => 'You already have an active application or membership with this firm.']);
        }

        FirmApplication::create([
            'lawyer_id'   => $user->id,
            'law_firm_id' => $request->law_firm_id,
            'message'     => $request->message,
            'status'      => 'pending',
        ]);

        return back()->with('success', 'Application submitted! The firm will review your profile.');
    }

    public function leave()
    {
        $user    = Auth::user();
        $profile = $user->lawyerProfile;

        if ($profile->law_firm_id) {
            FirmApplication::where('lawyer_id', $user->id)
                ->where('law_firm_id', $profile->law_firm_id)
                ->where('status', 'accepted')
                ->update(['status' => 'rejected', 'responded_at' => now()]);

            $profile->update(['law_firm_id' => null]);
        }

        return back()->with('success', 'You have left the firm.');
    }
}
