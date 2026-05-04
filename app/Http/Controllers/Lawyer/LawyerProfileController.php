<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class LawyerProfileController extends Controller
{
    public function show()
    {
        $user    = Auth::user();
        $profile = $user->lawyerProfile;
        $reviews = \App\Models\Review::with('client')
            ->where('lawyer_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('lawyer.profile', compact('user', 'profile', 'reviews'));
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
            'government_id'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'ibp_id'           => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $user->update(['name' => $request->name]);
        $profile = $user->lawyerProfile;

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        $data = $request->only([
            'specialty', 'firm', 'hourly_rate', 'experience_years', 'location', 'bio',
        ]);

        foreach ([
            'government_id' => 'government_id_doc',
            'ibp_id'        => 'ibp_id_doc',
        ] as $input => $column) {
            if ($request->hasFile($input)) {
                if ($profile->{$column}) {
                    Storage::disk('local')->delete($profile->{$column});
                    Storage::disk('public')->delete($profile->{$column});
                }

                $data[$column] = $request->file($input)->store('lawyer-docs', 'local');
            }
        }

        $profile->update($data);

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
        return back()->with('success', 'Availability is now updated automatically.');
    }
}
