<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LawyerProfile;
use Illuminate\Http\Request;

class AdminLawyerController extends Controller
{
    public function show(LawyerProfile $lawyer)
    {
        $lp = $lawyer->load('user');
        return view('admin.lawyer-details', compact('lp'));
    }
    public function index(Request $request)
    {
        $query = LawyerProfile::with('user');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('user', function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%");
            })->orWhere('specialty', 'like', "%$s%");
        }
        if ($request->filled('certified')) {
            $query->where('is_certified', $request->certified === '1');
        }

        $lawyers = $query->latest()->paginate(20)->withQueryString();
        return view('admin.lawyers', compact('lawyers'));
    }

    public function certify(LawyerProfile $lawyer)
    {
        $lawyer->update(['is_certified' => true]);
        return back()->with('success', "\"{$lawyer->user->name}\" has been certified.");
    }

    public function uncertify(LawyerProfile $lawyer)
    {
        $lawyer->update(['is_certified' => false]);
        return back()->with('success', "\"{$lawyer->user->name}\" certification has been revoked.");
    }
}
