<?php

namespace App\Http\Controllers\Api\LawFirm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        // Example: get all users in the same law firm
        $team = \App\Models\User::where('law_firm_id', $user->id)->get();
        return response()->json($team);
    }
}
