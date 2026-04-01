<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use App\Services\PayMongoChildMerchantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class LawyerProfileController extends Controller
{
    public function show(PayMongoChildMerchantService $childMerchantService)
    {
        $user    = Auth::user();
        $profile = $user->lawyerProfile;
        $paymongoMerchant = $profile?->paymongoChildMerchant;
        $childMerchantSupportMessage = $childMerchantService->supportMessage();

        return view('lawyer.profile', compact('user', 'profile', 'paymongoMerchant', 'childMerchantSupportMessage'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'             => 'required|string|max:100',
            'specialty'        => 'required|string|max:100',
            'firm'             => 'nullable|string|max:150',
            'hourly_rate'      => 'required|numeric|min:0|max:500000',
            'experience_years' => 'required|integer|min:0|max:60',
            'location'         => 'nullable|string|max:150',
            'bio'              => 'nullable|string|max:2000',
            'password'         => 'nullable|min:6|confirmed',
            'gcash_number'     => 'nullable|string|max:20',
        ]);

        $user->update(['name' => $request->name]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        // Handle GCash number and QR code
        $data = $request->only([
            'specialty', 'firm', 'hourly_rate', 'experience_years', 'location', 'bio',
            'gcash_number'
        ]);
        // Remove old QR code if a new one is uploaded
        if ($request->hasFile('gcash_qr')) {
            $oldQr = $user->lawyerProfile->gcash_qr;
            if ($oldQr) {
                Storage::disk('public')->delete($oldQr);
            }
            $qrPath = $request->file('gcash_qr')->store('gcash-qr', 'public');
            $data['gcash_qr'] = $qrPath;
        } elseif ($request->input('gcash_qr') === null) {
            // If cleared, remove from profile
            $data['gcash_qr'] = null;
        }
        $user->lawyerProfile->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:4096',
        ]);

        $user = Auth::user();
        if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
            Storage::disk('public')->delete($user->avatar);
        }
        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return back()->with('success', 'Profile photo updated successfully.');
    }

    public function updateAvailability(Request $request)
    {
        $request->validate(['status' => 'required|in:available,busy,offline']);
        Auth::user()->lawyerProfile->update(['availability_status' => $request->status]);
        return back()->with('success', 'Availability updated.');
    }
}
