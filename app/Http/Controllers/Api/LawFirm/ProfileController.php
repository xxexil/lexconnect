<?php

namespace App\Http\Controllers\Api\LawFirm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'firm_name' => $user->firm_name,
            'email' => $user->email,
            // Add more fields as needed
        ]);
    }
}
