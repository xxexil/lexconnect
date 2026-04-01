<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use Illuminate\Http\Request;

class AdminConsultationController extends Controller
{
    public function index(Request $request)
    {
        $query = Consultation::with(['client', 'lawyer']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('client', fn($sq) => $sq->where('name', 'like', "%$s%"))
                  ->orWhereHas('lawyer', fn($sq) => $sq->where('name', 'like', "%$s%"))
                  ->orWhere('code', 'like', "%$s%");
            });
        }

        $consultations = $query->latest('scheduled_at')->paginate(20)->withQueryString();

        $totalRevenue  = Consultation::where('status', 'completed')->sum('price');
        $statusCounts  = Consultation::selectRaw('status, count(*) as cnt')->groupBy('status')->pluck('cnt', 'status');

        return view('admin.consultations', compact('consultations', 'totalRevenue', 'statusCounts'));
    }
}
