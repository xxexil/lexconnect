<?php

namespace App\Http\Controllers\LawFirm;

use App\Http\Controllers\Controller;
use App\Models\LawFirmProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class LawFirmProfileController extends Controller
{
    public function show()
    {
        $firm = Auth::user()->lawFirmProfile;

        return view('lawfirm.profile', compact('firm'));
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
            'cut_percentage' => 'required|numeric|min:0|max:100',
            'specialties'  => 'nullable|array',
            'specialties.*'=> 'string|max:100',
            'dti_sec_registration' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'business_permit' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'valid_id' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'firm_ibp_id' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'name'         => 'required|string|max:100',
            'password'     => 'nullable|min:6|confirmed',
        ]);

        $docUpdates = [];
        foreach ([
            'dti_sec_registration' => 'dti_sec_registration_doc',
            'business_permit' => 'business_permit_doc',
            'valid_id' => 'valid_id_doc',
            'firm_ibp_id' => 'ibp_id_doc',
        ] as $input => $column) {
            if ($request->hasFile($input)) {
                if ($firm->{$column}) {
                    Storage::disk('public')->delete($firm->{$column});
                }
                $docUpdates[$column] = $request->file($input)->store('law-firm-docs', 'public');
            }
        }

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
            'cut_percentage' => $request->cut_percentage,
            'specialties'  => $request->specialties ?? [],
            ...$docUpdates,
        ]);

        $user->name = $request->name;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return back()->with('success', 'Firm profile updated successfully.');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:4096',
        ]);

        $user = Auth::user();
        if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
            \Storage::disk('public')->delete($user->avatar);
        }
        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return back()->with('success', 'Profile photo updated successfully.');
    }
}
