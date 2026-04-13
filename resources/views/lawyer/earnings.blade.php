@extends('layouts.lawyer')
@section('title', 'Earnings')
@section('content')

<div class="lp-page-header">
    <div>
        <h1 class="lp-page-title">Earnings</h1>
        <p class="lp-page-sub">Your payment history and earnings summary</p>
    </div>
</div>

@if(session('success'))
    <div class="lp-alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

<div class="lp-stats-grid">
    <div class="lp-stat-card">
        <div class="lp-stat-icon earned-icon"><i class="fas fa-peso-sign"></i></div>
        <div>
            <div class="lp-stat-num">&#8369;{{ number_format($totalEarned, 0) }}</div>
            <div class="lp-stat-lbl">Total Earned</div>
        </div>
    </div>
    <div class="lp-stat-card">
        <div class="lp-stat-icon upcoming-icon"><i class="fas fa-calendar-alt"></i></div>
        <div>
            <div class="lp-stat-num">&#8369;{{ number_format($thisMonth, 0) }}</div>
            <div class="lp-stat-lbl">This Month</div>
        </div>
    </div>
    <div class="lp-stat-card">
        <div class="lp-stat-icon pending-icon"><i class="fas fa-hourglass-half"></i></div>
        <div>
            <div class="lp-stat-num">&#8369;{{ number_format($pendingAmount, 0) }}</div>
            <div class="lp-stat-lbl">Pending</div>
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
            <div class="lp-stat-num">&#8369;{{ number_format($totalFirmCut, 0) }}</div>
            <div class="lp-stat-lbl">Firm Cut (5%)</div>
        </div>
    </div>
    @endif
</div>

@livewire('lawyer.earnings-history')

@endsection
