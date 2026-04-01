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

{{-- STATS --}}
<div class="lp-stats-grid">
    <div class="lp-stat-card">
        <div class="lp-stat-icon earned-icon"><i class="fas fa-peso-sign"></i></div>
        <div>
            <div class="lp-stat-num">₱{{ number_format($totalEarned, 0) }}</div>
            <div class="lp-stat-lbl">Total Earned</div>
        </div>
    </div>
    <div class="lp-stat-card">
        <div class="lp-stat-icon upcoming-icon"><i class="fas fa-calendar-alt"></i></div>
        <div>
            <div class="lp-stat-num">₱{{ number_format($thisMonth, 0) }}</div>
            <div class="lp-stat-lbl">This Month</div>
        </div>
    </div>
    <div class="lp-stat-card">
        <div class="lp-stat-icon pending-icon"><i class="fas fa-hourglass-half"></i></div>
        <div>
            <div class="lp-stat-num">₱{{ number_format($pendingAmount, 0) }}</div>
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
            <div class="lp-stat-num">₱{{ number_format($totalFirmCut, 0) }}</div>
            <div class="lp-stat-lbl">Firm Cut (5%)</div>
        </div>
    </div>
    @endif
</div>

{{-- TRANSACTION TABLE --}}
<div class="lp-card">
    <div class="lp-card-header">
        <h2 class="lp-card-title"><i class="fas fa-list-alt"></i> Transaction History</h2>
    </div>
    @if($payments->isEmpty())
    <div class="lp-empty"><i class="fas fa-receipt"></i><p>No transactions yet</p></div>
    @else
    <div style="overflow-x:auto;">
    <table class="lp-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Client</th>
                <th>Consultation</th>
                <th>Payment Type</th>
                <th>Amount</th>
                <th>Firm Cut</th>
                <th>Your Net</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $p)
            @php
                $typeLabels = ['downpayment' => 'Downpayment 50%', 'balance' => 'Balance 50%', 'full' => 'Full'];
                $typeLabel  = $typeLabels[$p->type] ?? ucfirst($p->type ?? 'full');
                $statusLabel = $p->status === 'downpayment_paid' ? 'Paid (Down)' : ucfirst(str_replace('_',' ',$p->status));
            @endphp
            <tr>
                <td>{{ $p->created_at->format('M j, Y') }}</td>
                <td>
                    <div class="lp-table-client">
                        <div class="lp-tc-avatar">{{ strtoupper(substr($p->client->name,0,1)) }}</div>
                        {{ $p->client->name }}
                    </div>
                </td>
                <td>{{ $p->consultation ? $p->consultation->code : '—' }}</td>
                <td>
                    <span class="lp-type-badge">{{ $typeLabel }}</span>
                </td>
                <td style="font-weight:700;color:#1e2d4d;">₱{{ number_format($p->amount,2) }}</td>
                <td style="color:#e07b00;">{{ $p->firm_cut > 0 ? '₱'.number_format($p->firm_cut,2) : '—' }}</td>
                <td style="font-weight:700;color:#1a6a2e;">₱{{ number_format($p->lawyer_net,2) }}</td>
                <td>
                    <span class="lp-pay-badge {{ $p->status }}">{{ $statusLabel }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    @endif
</div>

@endsection