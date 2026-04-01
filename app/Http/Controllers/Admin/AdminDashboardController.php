<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\LawFirmProfile;
use App\Models\LawyerProfile;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalClients    = User::where('role', 'client')->count();
        $totalLawyers    = User::where('role', 'lawyer')->count();
        $totalFirms      = User::where('role', 'law_firm')->count();
        $totalConsults   = Consultation::count();

        $pendingConsults  = Consultation::where('status', 'pending')->count();
        $upcomingConsults = Consultation::where('status', 'upcoming')->count();
        $completedConsults= Consultation::where('status', 'completed')->count();
        $cancelledConsults= Consultation::where('status', 'cancelled')->count();

        $unverifiedFirms = LawFirmProfile::where('is_verified', false)->count();
        $certifiedLawyers= LawyerProfile::where('is_certified', true)->count();

        $recentConsultations = Consultation::with(['client', 'lawyer'])
            ->latest()
            ->take(8)
            ->get();

        $recentUsers = User::where('role', '!=', 'admin')
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', compact(
            'totalClients', 'totalLawyers', 'totalFirms', 'totalConsults',
            'pendingConsults', 'upcomingConsults', 'completedConsults', 'cancelledConsults',
            'unverifiedFirms', 'certifiedLawyers',
            'recentConsultations', 'recentUsers'
        ));
    }
}
