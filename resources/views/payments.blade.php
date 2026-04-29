@extends('layouts.app')
@section('title', 'Payments')
@section('content')
<style>
.pay-toolbar {
    display:flex;
    gap:12px;
    align-items:end;
    justify-content:space-between;
    margin-bottom:20px;
    flex-wrap:wrap;
}
.pay-search-form {
    display:flex;
    gap:12px;
    flex-wrap:wrap;
    align-items:flex-end;
    width:100%;
}
.pay-field {
    display:flex;
    flex-direction:column;
    gap:6px;
}
.pay-field label {
    font-size:.78rem;
    font-weight:600;
    color:#6b7280;
}
.pay-input,
.pay-select {
    min-height:42px;
    border:1.5px solid #dbe3ef;
    border-radius:10px;
    padding:10px 14px;
    font-size:.9rem;
    color:#1e2d4d;
    background:#fff;
    font-family:inherit;
}
.pay-search {
    flex:1 1 280px;
}
.pay-search .pay-input {
    width:100%;
}
.pay-actions {
    display:flex;
    gap:10px;
    align-items:center;
    flex-wrap:wrap;
}
.pay-btn {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    min-height:42px;
    padding:0 16px;
    border:none;
    border-radius:10px;
    font-size:.88rem;
    font-weight:600;
    cursor:pointer;
    text-decoration:none;
    font-family:inherit;
}
.pay-btn-primary {
    background:#1e2d4d;
    color:#fff;
}
.pay-btn-secondary {
    background:#f3f4f6;
    color:#374151;
}
.pay-results-meta {
    font-size:.82rem;
    color:#6b7280;
    margin-bottom:14px;
}
.pay-pagination {
    margin-top:18px;
}
.pay-pagination .pagination {
    justify-content:center;
    margin-bottom:0;
}
@media (max-width: 700px) {
    .pay-search-form {
        align-items:stretch;
    }

    .pay-actions {
        width:100%;
    }

    .pay-btn {
        flex:1 1 100%;
    }
}
</style>
<div class="page-header">
    <div>
        <h1 class="page-title">Payment History</h1>
        <p class="page-subtitle">Track your consultation payments and billing</p>
    </div>
</div>

<div class="stats-grid" style="margin-bottom:28px;">
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
        <div class="stat-info">
            <span class="stat-value">₱{{ number_format($totalSpent,0) }}</span>
            <span class="stat-label">Total Paid</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-clock"></i></div>
        <div class="stat-info">
            <span class="stat-value">₱{{ number_format($totalPending,0) }}</span>
            <span class="stat-label">Pending</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fas fa-undo"></i></div>
        <div class="stat-info">
            <span class="stat-value">₱{{ number_format($totalRefunded,0) }}</span>
            <span class="stat-label">Refunded</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-receipt"></i></div>
        <div class="stat-info">
            <span class="stat-value">{{ $transactionCount }}</span>
            <span class="stat-label">Transactions</span>
        </div>
    </div>
</div>

<div class="content-card" style="padding:20px;margin-bottom:20px;">
    <form method="GET" action="{{ route('payments') }}" class="pay-search-form">
        <div class="pay-field pay-search">
            <label for="paymentSearch">Search</label>
            <input
                id="paymentSearch"
                type="text"
                name="search"
                value="{{ request('search') }}"
                class="pay-input"
                placeholder="Search lawyer, consultation code, type, or status"
            >
        </div>

        <div class="pay-field">
            <label for="paymentStatus">Status</label>
            <select id="paymentStatus" name="status" class="pay-select">
                <option value="">All statuses</option>
                <option value="paid" @selected(request('status') === 'paid')>Paid</option>
                <option value="downpayment_paid" @selected(request('status') === 'downpayment_paid')>Paid (Down)</option>
                <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                <option value="refunded" @selected(request('status') === 'refunded')>Refunded</option>
            </select>
        </div>

        <div class="pay-field">
            <label for="paymentType">Type</label>
            <select id="paymentType" name="type" class="pay-select">
                <option value="">All types</option>
                <option value="downpayment" @selected(request('type') === 'downpayment')>Downpayment 50%</option>
                <option value="balance" @selected(request('type') === 'balance')>Balance 50%</option>
                <option value="full" @selected(request('type') === 'full')>Full</option>
            </select>
        </div>

        <div class="pay-actions">
            <button type="submit" class="pay-btn pay-btn-primary">
                <i class="fas fa-search"></i> Apply
            </button>
            <a href="{{ route('payments') }}" class="pay-btn pay-btn-secondary">
                <i class="fas fa-rotate-left"></i> Clear
            </a>
        </div>
    </form>
</div>

<div class="pay-results-meta">
    Showing {{ $payments->count() }} of {{ $payments->total() }} payment{{ $payments->total() === 1 ? '' : 's' }}
</div>

<div class="content-card" style="overflow-x:auto;">
    <table style="width:100%;border-collapse:collapse;">
        <thead>
            <tr style="border-bottom:2px solid #f0f0f0;">
                <th style="text-align:left;padding:12px 16px;font-size:.8rem;color:#888;font-weight:600;">LAWYER</th>
                <th style="text-align:left;padding:12px 16px;font-size:.8rem;color:#888;font-weight:600;">CONSULTATION</th>
                <th style="text-align:left;padding:12px 16px;font-size:.8rem;color:#888;font-weight:600;">TYPE</th>
                <th style="text-align:left;padding:12px 16px;font-size:.8rem;color:#888;font-weight:600;">DATE</th>
                <th style="text-align:right;padding:12px 16px;font-size:.8rem;color:#888;font-weight:600;">AMOUNT</th>
                <th style="text-align:center;padding:12px 16px;font-size:.8rem;color:#888;font-weight:600;">STATUS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $p)
            @php
                $badges = [
                    'paid'              => ['#d4edda','#155724'],
                    'downpayment_paid'  => ['#d0eaff','#0d4f8c'],
                    'pending'           => ['#fff3cd','#856404'],
                    'refunded'          => ['#f8d7da','#721c24'],
                ];
                [$bg,$fg] = $badges[$p->status] ?? ['#e9ecef','#555'];
                $typeLabels = ['downpayment' => 'Downpayment 50%', 'balance' => 'Balance 50%', 'full' => 'Full'];
                $typeLabel  = $typeLabels[$p->type] ?? ucfirst($p->type ?? 'full');
                $statusLabel = $p->status === 'downpayment_paid' ? 'Paid (Down)' : ucfirst(str_replace('_',' ',$p->status));
            @endphp
            <tr style="border-bottom:1px solid #f8f9fa;">
                <td style="padding:14px 16px;font-weight:600;font-size:.9rem;color:#1e2d4d;">{{ $p->lawyer->name }}</td>
                <td style="padding:14px 16px;">
                    @if($p->consultation)
                        <div style="font-size:.88rem;color:#555;">{{ $p->consultation->code }}</div>
                        <div style="font-size:.78rem;color:#888;">{{ ucfirst($p->consultation->type) }}, {{ $p->consultation->duration_minutes }} min</div>
                    @else
                        <span style="color:#aaa;">—</span>
                    @endif
                </td>
                <td style="padding:14px 16px;font-size:.85rem;color:#555;">{{ $typeLabel }}</td>
                <td style="padding:14px 16px;font-size:.88rem;color:#555;">{{ $p->created_at->format('M d, Y') }}</td>
                <td style="padding:14px 16px;text-align:right;font-weight:700;color:#1e2d4d;">₱{{ number_format($p->amount,2) }}</td>
                <td style="padding:14px 16px;text-align:center;">
                    <span style="background:{{ $bg }};color:{{ $fg }};border-radius:12px;padding:4px 12px;font-size:.78rem;font-weight:600;">
                        {{ $statusLabel }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;padding:60px;color:#888;">
                    <i class="fas fa-receipt" style="font-size:2.5rem;margin-bottom:16px;display:block;"></i>
                    No payments matched your filters.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($payments->hasPages())
<div class="pay-pagination">
    {{ $payments->links('vendor.pagination.client-clean') }}
</div>
@endif
@endsection
