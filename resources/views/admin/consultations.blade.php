@extends('layouts.admin')
@section('title', 'Consultations')
@section('page-title', 'Consultations')
@section('content')

<style>
.ad-filter-bar {
    background: #fff; border-radius: 14px; padding: 16px 22px;
    display: flex; gap: 12px; flex-wrap: wrap; align-items: center;
    margin-bottom: 22px; border: 1px solid #eef0f6;
}
.ad-filter-bar input, .ad-filter-bar select {
    padding: 9px 14px; border: 1.5px solid #e5e7eb; border-radius: 9px;
    font-size: .875rem; font-family: inherit; flex: 1; min-width: 140px;
}
.ad-filter-bar input:focus, .ad-filter-bar select:focus { outline: none; border-color: #7c3aed; }
.ad-filter-btn { padding: 9px 20px; background: #7c3aed; color: #fff; border: none; border-radius: 9px; font-size: .875rem; font-weight: 600; cursor: pointer; font-family: inherit; }
.ad-filter-btn:hover { background: #6d28d9; }
.ad-filter-clear { font-size: .82rem; color: #7c3aed; text-decoration: none; font-weight: 500; }

.ad-summary-row { display: flex; gap: 14px; margin-bottom: 22px; flex-wrap: wrap; }
.ad-summary-card {
    flex: 1; min-width: 120px; background: #fff; border-radius: 12px;
    padding: 16px 20px; border: 1px solid #eef0f6; text-align: center;
}
.ad-summary-num { font-size: 1.5rem; font-weight: 800; line-height: 1; }
.ad-summary-lbl { font-size: .75rem; color: #6b7280; margin-top: 4px; font-weight: 500; }

.ad-consult-card {
    background: #fff; border-radius: 14px; padding: 18px 22px;
    margin-bottom: 14px; border: 1px solid #eef0f6; border-left: 4px solid #e5e7eb;
    box-shadow: 0 1px 4px rgba(0,0,0,.04);
    display: flex; gap: 16px; align-items: flex-start; flex-wrap: wrap;
}
.ad-consult-card.s-pending   { border-left-color: #f59e0b; }
.ad-consult-card.s-upcoming  { border-left-color: #3b82f6; }
.ad-consult-card.s-completed { border-left-color: #10b981; }
.ad-consult-card.s-cancelled { border-left-color: #ef4444; }

.ad-consult-code {
    font-family: monospace; font-size: .8rem; font-weight: 700;
    background: #f3f4f6; color: #374151; padding: 4px 10px;
    border-radius: 7px;
}
.ad-consult-meta { display: flex; flex-wrap: wrap; gap: 12px; font-size: .82rem; color: #6b7280; margin-top: 8px; }
.ad-consult-meta i { color: #9ca3af; }

.ad-pill { display: inline-flex; align-items: center; gap: 4px; font-size: .72rem; font-weight: 600; padding: 4px 11px; border-radius: 20px; }
.pill-pending   { background: #fef9c3; color: #854d0e; }
.pill-upcoming  { background: #dbeafe; color: #1d4ed8; }
.pill-completed { background: #d1fae5; color: #065f46; }
.pill-cancelled { background: #fee2e2; color: #991b1b; }

.ad-type-pill {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: .75rem; font-weight: 600; padding: 3px 10px;
    border-radius: 8px; background: #f3f4f6; color: #374151;
}
</style>

{{-- Revenue + status summary --}}
<div class="ad-summary-row">
    <div class="ad-summary-card" style="border-top: 3px solid #7c3aed;">
        <div class="ad-summary-num" style="color:#7c3aed;">₱{{ number_format($totalRevenue, 0) }}</div>
        <div class="ad-summary-lbl">Total Revenue</div>
    </div>
    <div class="ad-summary-card" style="border-top: 3px solid #d97706;">
        <div class="ad-summary-num" style="color:#d97706;">{{ $statusCounts['pending'] ?? 0 }}</div>
        <div class="ad-summary-lbl">Pending</div>
    </div>
    <div class="ad-summary-card" style="border-top: 3px solid #3b82f6;">
        <div class="ad-summary-num" style="color:#3b82f6;">{{ $statusCounts['upcoming'] ?? 0 }}</div>
        <div class="ad-summary-lbl">Upcoming</div>
    </div>
    <div class="ad-summary-card" style="border-top: 3px solid #10b981;">
        <div class="ad-summary-num" style="color:#10b981;">{{ $statusCounts['completed'] ?? 0 }}</div>
        <div class="ad-summary-lbl">Completed</div>
    </div>
    <div class="ad-summary-card" style="border-top: 3px solid #ef4444;">
        <div class="ad-summary-num" style="color:#ef4444;">{{ $statusCounts['cancelled'] ?? 0 }}</div>
        <div class="ad-summary-lbl">Cancelled</div>
    </div>
</div>

<form method="GET" action="{{ route('admin.consultations') }}">
    <div class="ad-filter-bar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search code, client, or lawyer…">
        <select name="status">
            <option value="">All Statuses</option>
            @foreach(['pending','upcoming','completed','cancelled'] as $s)
            <option value="{{ $s }}" {{ request('status')===$s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <select name="type">
            <option value="">All Types</option>
            <option value="video"     {{ request('type')==='video'     ? 'selected' : '' }}>Video</option>
            <option value="phone"     {{ request('type')==='phone'     ? 'selected' : '' }}>Phone</option>
            <option value="in-person" {{ request('type')==='in-person' ? 'selected' : '' }}>In-Person</option>
        </select>
        <button type="submit" class="ad-filter-btn"><i class="fas fa-search"></i> Filter</button>
        @if(request()->anyFilled(['search','status','type']))
        <a href="{{ route('admin.consultations') }}" class="ad-filter-clear">Clear</a>
        @endif
    </div>
</form>

@forelse($consultations as $c)
@php $sched = \Carbon\Carbon::parse($c->scheduled_at); @endphp
<div class="ad-consult-card s-{{ $c->status }}">
    <div style="flex:1;min-width:0;">
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:8px;">
            <span class="ad-consult-code">{{ $c->code }}</span>
            <span class="ad-pill pill-{{ $c->status }}">{{ ucfirst($c->status) }}</span>
            <span class="ad-type-pill">
                <i class="fas fa-{{ $c->type === 'video' ? 'video' : ($c->type === 'phone' ? 'phone' : 'building') }}"></i>
                {{ ucfirst($c->type) }}
            </span>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;max-width:500px;">
            <div>
                <div style="font-size:.72rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:3px;">Client</div>
                <div style="font-weight:600;color:#1a1a2e;font-size:.9rem;">{{ $c->client->name ?? '—' }}</div>
                <div style="font-size:.78rem;color:#9ca3af;">{{ $c->client->email ?? '' }}</div>
            </div>
            <div>
                <div style="font-size:.72rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:3px;">Lawyer</div>
                <div style="font-weight:600;color:#1a1a2e;font-size:.9rem;">{{ $c->lawyer->name ?? '—' }}</div>
                <div style="font-size:.78rem;color:#9ca3af;">{{ $c->lawyer->email ?? '' }}</div>
            </div>
        </div>
        <div class="ad-consult-meta" style="margin-top:10px;">
            <span><i class="fas fa-calendar"></i> {{ $sched->format('M d, Y') }}</span>
            <span><i class="fas fa-clock"></i> {{ $sched->format('g:i A') }}</span>
            <span><i class="fas fa-stopwatch"></i> {{ $c->duration_label }}</span>
            <span><i class="fas fa-peso-sign"></i> ₱{{ number_format($c->price, 0) }}</span>
        </div>
    </div>
</div>
@empty
<div style="text-align:center;padding:72px 20px;color:#9ca3af;">
    <i class="fas fa-calendar-times" style="font-size:2.5rem;display:block;margin-bottom:14px;opacity:.3;"></i>
    <p>No consultations found.</p>
</div>
@endforelse

@if($consultations->hasPages())
<div style="margin-top:16px;">{{ $consultations->links() }}</div>
@endif

@endsection

