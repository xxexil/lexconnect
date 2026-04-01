<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentRiskEvent;
use Illuminate\Http\Request;

class AdminFraudRiskController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentRiskEvent::with(['client', 'lawyer', 'consultation', 'payment']);

        if ($request->filled('risk_level')) {
            $query->where('risk_level', $request->risk_level);
        }

        if ($request->filled('recommendation')) {
            $query->where('recommendation', $request->recommendation);
        }

        if ($request->filled('context')) {
            $query->where('context', $request->context);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($nested) use ($search) {
                $nested->where('email', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($clientQuery) use ($search) {
                        $clientQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('lawyer', function ($lawyerQuery) use ($search) {
                        $lawyerQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('consultation', function ($consultationQuery) use ($search) {
                        $consultationQuery->where('code', 'like', "%{$search}%");
                    });
            });
        }

        $riskEvents = $query->latest()->paginate(20)->withQueryString();

        $summaryBase = PaymentRiskEvent::query();
        $summary = [
            'total' => (clone $summaryBase)->count(),
            'high' => (clone $summaryBase)->where('risk_level', 'high')->count(),
            'medium' => (clone $summaryBase)->where('risk_level', 'medium')->count(),
            'blocked' => (clone $summaryBase)->where('recommendation', 'block')->count(),
            'last24h' => (clone $summaryBase)->where('created_at', '>=', now()->subDay())->count(),
        ];

        $contexts = PaymentRiskEvent::query()
            ->select('context')
            ->distinct()
            ->orderBy('context')
            ->pluck('context');

        return view('admin.fraud-risk-events', compact('riskEvents', 'summary', 'contexts'));
    }
}
