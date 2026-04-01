<?php

namespace App\Http\Controllers\Api\LawFirm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EarningsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        // Example: sum all earnings for this law firm
        $total = \App\Models\Earning::where('law_firm_id', $user->id)->sum('amount');
        return response()->json(['total' => $total]);
    }
}
