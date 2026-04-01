<?php

namespace App\Http\Controllers\Api\LawFirm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        // Example: get all messages for this law firm
        $messages = \App\Models\Message::where('law_firm_id', $user->id)->get();
        return response()->json($messages);
    }
}
