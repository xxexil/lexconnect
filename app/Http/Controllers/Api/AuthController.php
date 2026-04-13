<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LawyerProfile;
use App\Models\LawFirmProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        // Admin cannot login via mobile app
        if ($user->role === 'admin') {
            return response()->json(['message' => 'Admin access is not available on mobile.'], 403);
        }

        $token = $user->createToken('mobile-app')->plainTextToken;

        $profile = null;
        if ($user->role === 'lawyer') {
            $profile = $user->lawyerProfile;
            if ($profile) {
                $profile->update(['availability_status' => 'available']);
                $profile->refresh();
            }
        } elseif ($user->role === 'law_firm') {
            $profile = $user->lawFirmProfile;
        }

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'role'       => $user->role,
                'avatar_url' => $user->avatar_url,
                'phone'      => $user->phone,
                'bio'        => $user->bio,
            ],
            'profile' => $profile,
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'             => 'required_unless:role,lawyer|nullable|string|max:100',
            'first_name'       => 'required_if:role,lawyer|nullable|string|max:100',
            'middle_name'      => 'nullable|string|max:100',
            'last_name'        => 'required_if:role,lawyer|nullable|string|max:100',
            'email'            => 'required|email|unique:users',
            'password'         => 'required|min:6|confirmed',
            'role'             => 'required|in:client,lawyer,law_firm',
            'firm_name'        => 'required_if:role,law_firm|nullable|string|max:150',
            'specialty'        => 'required_if:role,lawyer|nullable|string|max:100',
            'hourly_rate'      => 'required_if:role,lawyer|nullable|numeric|min:0',
            'experience_years' => 'required_if:role,lawyer|nullable|integer|min:0',
            'location'         => 'nullable|string|max:150',
        ]);

        if ($request->role === 'lawyer') {
            $middle = $request->middle_name ? ' ' . trim($request->middle_name) . ' ' : ' ';
            $fullName = trim($request->first_name . $middle . $request->last_name);
        } else {
            $fullName = $request->name;
        }

        $user = User::create([
            'name'     => $fullName,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        if ($request->role === 'lawyer') {
            LawyerProfile::create([
                'user_id'             => $user->id,
                'specialty'           => $request->specialty,
                'firm'                => $request->firm,
                'hourly_rate'         => $request->hourly_rate ?? 0,
                'experience_years'    => $request->experience_years ?? 0,
                'location'            => $request->location,
                'availability_status' => 'available',
            ]);
        } elseif ($request->role === 'law_firm') {
            LawFirmProfile::create([
                'user_id'   => $user->id,
                'firm_name' => $request->firm_name,
                'firm_size' => 'solo',
                'cut_percentage' => 5,
            ]);
        }

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'role'       => $user->role,
                'avatar_url' => $user->avatar_url,
                'phone'      => $user->phone,
                'bio'        => $user->bio,
            ],
        ], 201);
    }

    public function logout(Request $request)
    {
        if ($request->user()?->role === 'lawyer' && $request->user()->lawyerProfile) {
            $request->user()->lawyerProfile->update(['availability_status' => 'offline']);
        }
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully.']);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        $profile = null;
        if ($user->role === 'lawyer') {
            $profile = $user->lawyerProfile;
        } elseif ($user->role === 'law_firm') {
            $profile = $user->lawFirmProfile;
        }

        return response()->json([
            'user' => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'role'       => $user->role,
                'avatar_url' => $user->avatar_url,
                'phone'      => $user->phone,
                'bio'        => $user->bio,
            ],
            'profile' => $profile,
        ]);
    }
}
