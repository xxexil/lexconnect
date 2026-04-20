@extends('layouts.lawfirm')
@section('title', 'Earnings')
@section('content')

<div class="lp-page-header">
    <div>
        <h1 class="lp-page-title">Firm Earnings</h1>
        <p class="lp-page-sub">Track the actual share retained by your firm from team consultations</p>
    </div>
    <a href="{{ route('lawfirm.earnings.export') }}"
       style="display:inline-flex;align-items:center;gap:8px;padding:9px 20px;background:#1e2d4d;color:#fff;border-radius:9px;font-size:.88rem;font-weight:600;text-decoration:none;transition:background .15s;"
       onmouseover="this.style.background='#162240'" onmouseout="this.style.background='#1e2d4d'">
        <i class="fas fa-download"></i> Export CSV
    </a>
</div>

{{-- STAT CARDS --}}
<div class="lp-stats-grid" style="grid-template-columns:repeat(5,1fr);">
    <div class="lp-stat-card">
        <div class="lp-stat-icon earned-icon"><i class="fas fa-peso-sign"></i></div>
        <div>
            <div class="lp-stat-num" style="font-size:1rem;">&#8369;{{ number_format($totalEarned, 2) }}</div>
            <div class="lp-stat-lbl">Total Firm Cut</div>
        </div>
    </div>
    <div class="lp-stat-card">
        <div class="lp-stat-icon upcoming-icon"><i class="fas fa-calendar-alt"></i></div>
        <div>
            <div class="lp-stat-num" style="font-size:1rem;">&#8369;{{ number_format($thisMonthEarned, 2) }}</div>
            <div class="lp-stat-lbl">This Month</div>
        </div>
    </div>
    <div class="lp-stat-card">
        <div class="lp-stat-icon" style="background:rgba(124,58,237,.1);color:#7c3aed;"><i class="fas fa-calendar"></i></div>
        <div>
            <div class="lp-stat-num" style="font-size:1rem;">&#8369;{{ number_format($thisYearEarned, 2) }}</div>
            <div class="lp-stat-lbl">This Year</div>
        </div>
    </div>
    <div class="lp-stat-card">
        <div class="lp-stat-icon pending-icon"><i class="fas fa-hourglass-half"></i></div>
        <div>
            <div class="lp-stat-num" style="font-size:1rem;">&#8369;{{ number_format($pendingAmount, 2) }}</div>
            <div class="lp-stat-lbl">Expected Pending</div>
        </div>
    </div>
    <div class="lp-stat-card">
        <div class="lp-stat-icon clients-icon"><i class="fas fa-user-friends"></i></div>
        <div>
            <div class="lp-stat-num">{{ $totalClients }}</div>
            <div class="lp-stat-lbl">Unique Clients</div>
        </div>
    </div>
</div>

{{-- PER-LAWYER BREAKDOWN --}}
@if($lawyerBreakdown->count() > 0)
<div class="lp-card" style="margin-bottom:24px;">
    <div class="lp-card-header">
        <h2 class="lp-card-title"><i class="fas fa-chart-bar"></i> Per-Lawyer Breakdown</h2>
        <span style="font-size:.8rem;color:#6c757d;">Sorted by total firm cut</span>
    </div>
    <table class="lp-table">
        <thead>
            <tr>
                <th>Lawyer</th>
                <th>Specialty</th>
                <th>Consultations</th>
                <th>This Month</th>
                <th>Total Firm Cut</th>
                <th>Rate</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lawyerBreakdown as $lp)
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:32px;height:32px;border-radius:50%;background:#1e2d4d;color:#fff;display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:700;flex-shrink:0;overflow:hidden;">
                            @if($lp->user->avatar_url)
                                <img src="{{ $lp->user->avatar_url }}" style="width:100%;height:100%;object-fit:cover;display:block;" alt="{{ $lp->user->name }}">
                            @else
                                {{ strtoupper(substr($lp->user->name,0,1)) }}
                            @endif
                        </div>
                        {{ $lp->user->name }}
                    </div>
                </td>
                <td>{{ $lp->specialty }}</td>
                <td>{{ $lp->consultations_count }}</td>
                <td style="color:#3b82f6;font-weight:600;">&#8369;{{ number_format($lp->earned_this_month, 2) }}</td>
                <td><strong style="color:#1e2d4d;">&#8369;{{ number_format($lp->earned, 2) }}</strong></td>
                <td>&#8369;{{ number_format($lp->hourly_rate, 0) }}/hr</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@livewire('lawfirm.earnings-history')

@endsection
