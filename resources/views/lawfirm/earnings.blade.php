@extends('layouts.lawfirm')
@section('title', 'Earnings')
@section('content')

<div class="lp-page-header">
    <div>
        <h1 class="lp-page-title">Firm Earnings</h1>
        <p class="lp-page-sub">Track the actual share retained by your firm from team consultations</p>
    </div>
</div>

<div class="lp-stats-grid">
    <div class="lp-stat-card">
        <div class="lp-stat-icon earned-icon"><i class="fas fa-peso-sign"></i></div>
        <div>
            <div class="lp-stat-num">&#8369;{{ number_format($totalEarned, 2) }}</div>
            <div class="lp-stat-lbl">Total Firm Cut</div>
        </div>
    </div>
    <div class="lp-stat-card">
        <div class="lp-stat-icon upcoming-icon"><i class="fas fa-calendar-check"></i></div>
        <div>
            <div class="lp-stat-num">&#8369;{{ number_format($thisMonthEarned, 2) }}</div>
            <div class="lp-stat-lbl">This Month's Cut</div>
        </div>
    </div>
    <div class="lp-stat-card">
        <div class="lp-stat-icon pending-icon"><i class="fas fa-hourglass-half"></i></div>
        <div>
            <div class="lp-stat-num">&#8369;{{ number_format($pendingAmount, 2) }}</div>
            <div class="lp-stat-lbl">Pending Amount</div>
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

@if($lawyerBreakdown->count() > 0)
<div class="lp-card" style="margin-bottom:24px;">
    <div class="lp-card-header">
        <h2 class="lp-card-title"><i class="fas fa-chart-bar"></i> Per-Lawyer Breakdown</h2>
    </div>
    <table class="lp-table">
        <thead>
            <tr><th>Lawyer</th><th>Specialty</th><th>Consultations</th><th>Firm Cut</th><th>Rate</th></tr>
        </thead>
        <tbody>
            @foreach($lawyerBreakdown as $lp)
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div class="lp-req-avatar" style="width:32px;height:32px;line-height:32px;font-size:.8rem;flex-shrink:0;">{{ strtoupper(substr($lp->user->name,0,1)) }}</div>
                        {{ $lp->user->name }}
                    </div>
                </td>
                <td>{{ $lp->specialty }}</td>
                <td>{{ $lp->consultations_count }}</td>
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
