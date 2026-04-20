<?php

namespace App\Http\Controllers\LawFirm;

use App\Http\Controllers\Controller;
use App\Models\FirmApplication;
use App\Models\LawyerProfile;
use Illuminate\Support\Facades\Auth;

class LawFirmLawyerController extends Controller
{
    public function index()
    {
        $firm = Auth::user()->lawFirmProfile;

        $teamMembers = LawyerProfile::with('user')
            ->where('law_firm_id', $firm->id)
            ->get()
            ->map(function ($member) {
                $member->total_consultations = \App\Models\Consultation::where('lawyer_id', $member->user_id)->count();
                $member->joined_at = \App\Models\FirmApplication::where('lawyer_id', $member->user_id)
                    ->where('law_firm_id', $member->law_firm_id)
                    ->where('status', 'accepted')
                    ->value('responded_at');
                return $member;
            });

        $applications = FirmApplication::with(['lawyer', 'lawyer.lawyerProfile'])
            ->where('law_firm_id', $firm->id)
            ->whereIn('status', ['pending', 'rejected'])
            ->orderByRaw("FIELD(status,'pending','rejected')")
            ->latest()
            ->get();

        return view('lawfirm.lawyers', compact('firm', 'teamMembers', 'applications'));
    }

    public function accept($id)
    {
        $firm = Auth::user()->lawFirmProfile;
        $app  = FirmApplication::where('id', $id)->where('law_firm_id', $firm->id)->firstOrFail();

        $app->update(['status' => 'accepted', 'responded_at' => now()]);
        LawyerProfile::where('user_id', $app->lawyer_id)->update(['law_firm_id' => $firm->id]);

        return back()->with('success', 'Application accepted — lawyer added to your team.');
    }

    public function reject($id)
    {
        $firm = Auth::user()->lawFirmProfile;
        $app  = FirmApplication::where('id', $id)->where('law_firm_id', $firm->id)->firstOrFail();

        $app->update(['status' => 'rejected', 'responded_at' => now()]);

        return back()->with('success', 'Application rejected.');
    }

    public function remove($lawyerId)
    {
        $firm = Auth::user()->lawFirmProfile;

        LawyerProfile::where('user_id', $lawyerId)
            ->where('law_firm_id', $firm->id)
            ->update(['law_firm_id' => null]);

        FirmApplication::where('lawyer_id', $lawyerId)
            ->where('law_firm_id', $firm->id)
            ->where('status', 'accepted')
            ->update(['status' => 'rejected', 'responded_at' => now()]);

        return back()->with('success', 'Lawyer removed from your firm.');
    }
}
