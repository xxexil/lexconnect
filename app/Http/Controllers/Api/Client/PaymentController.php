<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $payments = Payment::where('client_id', $user->id)
            ->with(['consultation:id,code,scheduled_at,type', 'lawyer:id,name'])
            ->orderByDesc('created_at')
            ->paginate(20)
            ->through(fn($p) => [
                'id'              => $p->id,
                'amount'          => $p->amount,
                'status'          => $p->status,
                'type'            => $p->type,
                'created_at'      => $p->created_at,
                'consultation'    => $p->consultation ? [
                    'code'         => $p->consultation->code,
                    'scheduled_at' => $p->consultation->scheduled_at,
                    'type'         => $p->consultation->type,
                ] : null,
                'lawyer_name'     => $p->lawyer?->name,
            ]);

        $stats = [
            'total_paid'    => Payment::where('client_id', $user->id)->where('status', 'paid')->sum('amount'),
            'pending'       => Payment::where('client_id', $user->id)->where('status', 'pending')->sum('amount'),
        ];

        return response()->json(['payments' => $payments, 'stats' => $stats]);
    }
}
