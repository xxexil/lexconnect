<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LawFirmProfile;
use Illuminate\Http\Request;

class AdminLawFirmController extends Controller
{
    public function show(LawFirmProfile $firm)
    {
        $firm->load('user');
        return view('admin.law-firm-details', compact('firm'));
    }

    public function index(Request $request)
    {
        $query = LawFirmProfile::with('user');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('firm_name', 'like', "%$s%")
                  ->orWhere('city', 'like', "%$s%");
            });
        }
        if ($request->filled('verified')) {
            $query->where('is_verified', $request->verified === '1');
        }

        $firms = $query->latest()->paginate(20)->withQueryString();
        return view('admin.law-firms', compact('firms'));
    }

    public function verify(LawFirmProfile $firm)
    {
        $firm->update(['is_verified' => true]);
        return back()->with('success', "\"{$firm->firm_name}\" has been verified.");
    }

    public function unverify(LawFirmProfile $firm)
    {
        $firm->update(['is_verified' => false]);
        return back()->with('success', "\"{$firm->firm_name}\" verification has been revoked.");
    }
}
