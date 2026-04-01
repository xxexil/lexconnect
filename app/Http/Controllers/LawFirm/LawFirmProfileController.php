<?php

namespace App\Http\Controllers\LawFirm;

use App\Http\Controllers\Controller;
use App\Models\LawFirmProfile;
use App\Services\PayMongoChildMerchantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LawFirmProfileController extends Controller
{
    public function show(PayMongoChildMerchantService $childMerchantService)
    {
        $firm = Auth::user()->lawFirmProfile;
        $paymongoMerchant = $firm?->paymongoChildMerchant;
        $childMerchantSupportMessage = $childMerchantService->supportMessage();

        return view('lawfirm.profile', compact('firm', 'paymongoMerchant', 'childMerchantSupportMessage'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $firm = $user->lawFirmProfile;

        $request->validate([
            'firm_name'    => 'required|string|max:150',
            'tagline'      => 'nullable|string|max:200',
            'description'  => 'nullable|string|max:2000',
            'address'      => 'nullable|string|max:200',
            'city'         => 'nullable|string|max:100',
            'website'      => 'nullable|url|max:200',
            'phone'        => 'nullable|string|max:30',
            'founded_year' => 'nullable|integer|min:1800|max:' . date('Y'),
            'firm_size'    => 'nullable|in:solo,small,medium,large',
            'specialties'  => 'nullable|array',
            'specialties.*'=> 'string|max:100',
            'name'         => 'required|string|max:100',
            'password'     => 'nullable|min:6|confirmed',
        ]);

        $firm->update([
            'firm_name'    => $request->firm_name,
            'tagline'      => $request->tagline,
            'description'  => $request->description,
            'address'      => $request->address,
            'city'         => $request->city,
            'website'      => $request->website,
            'phone'        => $request->phone,
            'founded_year' => $request->founded_year,
            'firm_size'    => $request->firm_size ?? 'small',
            'specialties'  => $request->specialties ?? [],
        ]);

        $user->name = $request->name;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return back()->with('success', 'Firm profile updated successfully.');
    }
}
