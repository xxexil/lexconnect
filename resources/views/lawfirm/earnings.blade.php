@extends('layouts.lawfirm')
@section('title', 'Earnings')
@section('content')

<div class="lp-page-header">
    <div>
        <h1 class="lp-page-title">Firm Earnings</h1>
        <p class="lp-page-sub">Revenue generated across all team lawyers</p>
    </div>
</div>

{{-- STAT CARDS --}}
<div class="lp-stats-grid">
    <div class="lp-stat-card">
        <div class="lp-stat-icon earned-icon"><i class="fas fa-peso-sign"></i></div>
        <div>
            <div class="lp-stat-num">₱{{ number_format($totalEarned, 0) }}</div>
            <div class="lp-stat-lbl">Total Earned</div>
        </div>
    </div>
    <div class="lp-stat-card">
        <div class="lp-stat-icon upcoming-icon"><i class="fas fa-calendar-check"></i></div>
        <div>
            <div class="lp-stat-num">₱{{ number_format($thisMonthEarned, 0) }}</div>
            <div class="lp-stat-lbl">This Month</div>
        </div>
    </div>
    <div class="lp-stat-card">
        <div class="lp-stat-icon pending-icon"><i class="fas fa-hourglass-half"></i></div>
        <div>
            <div class="lp-stat-num">₱{{ number_format($pendingAmount, 0) }}</div>
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

{{-- PER-LAWYER BREAKDOWN --}}
@if($lawyerBreakdown->count() > 0)
<div class="lp-card" style="margin-bottom:24px;">
    <div class="lp-card-header">
        <h2 class="lp-card-title"><i class="fas fa-chart-bar"></i> Per-Lawyer Breakdown</h2>
    </div>
    <table class="lp-table">
        <thead>
            <tr><th>Lawyer</th><th>Specialty</th><th>Consultations</th><th>Earned</th><th>Rate</th></tr>
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
                <td><strong style="color:#1e2d4d;">₱{{ number_format($lp->earned, 2) }}</strong></td>
                <td>₱{{ number_format($lp->hourly_rate, 0) }}/hr</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- TRANSACTION TABLE --}}
<div class="lp-card">
    <div class="lp-card-header">
        <h2 class="lp-card-title"><i class="fas fa-receipt"></i> All Transactions</h2>
    </div>
    @if($payments->isEmpty())
    <div class="lp-empty-sm"><i class="fas fa-peso-sign"></i> No transactions yet</div>
    @else
    <div style="overflow-x:auto;">
        <table class="lp-table">
            <thead>
                <tr><th>Client</th><th>Lawyer</th><th>Consultation</th><th>Amount</th><th>Status</th><th>Date</th></tr>
            </thead>
            <tbody>
                @foreach($payments as $p)
                <tr>
                    <td>{{ $p->client->name ?? '—' }}</td>
                    <td>{{ $p->lawyer->name ?? '—' }}</td>
                    <td style="font-size:.8rem;color:#6c757d;">{{ $p->consultation->consultation_code ?? 'N/A' }}</td>
                    <td><strong>₱{{ number_format($p->amount, 2) }}</strong></td>
                    <td><span class="lp-pay-badge {{ $p->status }}">{{ ucfirst($p->status) }}</span></td>
                    <td style="font-size:.82rem;color:#6c757d;">{{ $p->created_at->format('M j, Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@endsection
