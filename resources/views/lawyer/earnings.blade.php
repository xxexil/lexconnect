@extends('layouts.lawyer')
@section('title', 'Earnings')
@section('content')

<div class="lp-page-header">
    <div>
        <h1 class="lp-page-title">Earnings</h1>
        <p class="lp-page-sub">Your payment history and earnings summary</p>
    </div>
    {{-- CSV Export --}}
    <a href="{{ route('lawyer.earnings.export') }}"
       style="display:inline-flex;align-items:center;gap:8px;padding:9px 20px;background:#1e2d4d;color:#fff;border-radius:9px;font-size:.88rem;font-weight:600;text-decoration:none;transition:background .15s;"
       onmouseover="this.style.background='#162240'" onmouseout="this.style.background='#1e2d4d'">
        <i class="fas fa-download"></i> Export CSV
    </a>
</div>

@if(session('success'))
    <div class="lp-alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

{{-- STAT CARDS --}}
<div class="lp-stats-grid">
    <div class="lp-stat-card">
        <div class="lp-stat-icon earned-icon"><i class="fas fa-peso-sign"></i></div>
        <div>
            <div class="lp-stat-num">&#8369;{{ number_format($totalEarned, 2) }}</div>
            <div class="lp-stat-lbl">Total Earned</div>
        </div>
    </div>
    <div class="lp-stat-card">
        <div class="lp-stat-icon upcoming-icon"><i class="fas fa-calendar-alt"></i></div>
        <div>
            <div class="lp-stat-num">&#8369;{{ number_format($thisMonth, 2) }}</div>
            <div class="lp-stat-lbl">This Month</div>
        </div>
    </div>
    <div class="lp-stat-card">
        <div class="lp-stat-icon" style="background:rgba(124,58,237,.1);color:#7c3aed;"><i class="fas fa-calendar"></i></div>
        <div>
            <div class="lp-stat-num">&#8369;{{ number_format($thisYear, 2) }}</div>
            <div class="lp-stat-lbl">This Year</div>
        </div>
    </div>
    <div class="lp-stat-card">
        <div class="lp-stat-icon pending-icon"><i class="fas fa-hourglass-half"></i></div>
        <div>
            <div class="lp-stat-num">&#8369;{{ number_format($pendingAmount, 2) }}</div>
            <div class="lp-stat-lbl">Expected Pending</div>
        </div>
    </div>
    <div class="lp-stat-card">
        <div class="lp-stat-icon clients-icon"><i class="fas fa-user-friends"></i></div>
        <div>
            <div class="lp-stat-num">{{ $totalClients }}</div>
            <div class="lp-stat-lbl">Clients Paid</div>
        </div>
    </div>
    @if($totalFirmCut > 0)
    <div class="lp-stat-card">
        <div class="lp-stat-icon pending-icon"><i class="fas fa-building"></i></div>
        <div>
            <div class="lp-stat-num">&#8369;{{ number_format($totalFirmCut, 2) }}</div>
            <div class="lp-stat-lbl">Firm Cut{{ $firmCutPct ? ' ('.$firmCutPct.'%)' : '' }}</div>
        </div>
    </div>
    @endif
</div>

@livewire('lawyer.earnings-history')

@endsection
