<?php

namespace App\Http\Controllers\Api\LawFirm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'firm_name' => $user->firm_name,
            'email' => $user->email,
            // Add more fields as needed
        ]);
    }
}
