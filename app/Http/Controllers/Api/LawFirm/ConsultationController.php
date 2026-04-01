<?php

namespace App\Http\Controllers\Api\LawFirm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ConsultationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        // Example: get all consultations for this law firm
        $consultations = \App\Models\Consultation::where('law_firm_id', $user->id)->get();
        return response()->json($consultations);
    }
}
