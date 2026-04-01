<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'role'       => $user->role,
            'phone'      => $user->phone,
            'bio'        => $user->bio,
            'avatar_url' => $user->avatar_url,
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name'     => 'sometimes|string|max:100',
            'phone'    => 'sometimes|nullable|string|max:20',
            'bio'      => 'sometimes|nullable|string|max:500',
            'password' => 'sometimes|min:6|confirmed',
        ]);

        $data = $request->only(['name', 'phone', 'bio']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user'    => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'role'       => $user->role,
                'phone'      => $user->phone,
                'bio'        => $user->bio,
                'avatar_url' => $user->avatar_url,
            ],
        ]);
    }
}
