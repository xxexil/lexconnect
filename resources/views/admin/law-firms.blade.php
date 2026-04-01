@extends('layouts.admin')
@section('title', 'Law Firms')
@section('page-title', 'Law Firms')
@section('content')

<style>
.ad-filter-bar {
    background: #fff; border-radius: 14px; padding: 16px 22px;
    display: flex; gap: 12px; flex-wrap: wrap; align-items: center;
    margin-bottom: 22px; border: 1px solid #eef0f6;
}
.ad-filter-bar input, .ad-filter-bar select {
    padding: 9px 14px; border: 1.5px solid #e5e7eb; border-radius: 9px;
    font-size: .875rem; font-family: inherit; flex: 1; min-width: 160px;
}
.ad-filter-bar input:focus, .ad-filter-bar select:focus { outline: none; border-color: #7c3aed; }
.ad-filter-btn { padding: 9px 20px; background: #7c3aed; color: #fff; border: none; border-radius: 9px; font-size: .875rem; font-weight: 600; cursor: pointer; font-family: inherit; }
.ad-filter-btn:hover { background: #6d28d9; }
.ad-filter-clear { font-size: .82rem; color: #7c3aed; text-decoration: none; font-weight: 500; }

.ad-firm-card {
    background: #fff; border-radius: 16px; padding: 22px 24px;
    margin-bottom: 16px; border: 1px solid #eef0f6;
    box-shadow: 0 1px 4px rgba(0,0,0,.04);
    display: flex; gap: 18px; align-items: flex-start;
}
.ad-firm-init {
    width: 60px; height: 60px; border-radius: 14px; flex-shrink: 0;
    background: linear-gradient(135deg, #1a3d2b, #1e5235);
    color: #fff; font-size: 1.1rem; font-weight: 800;
    display: flex; align-items: center; justify-content: center; letter-spacing: -1px;
}
.ad-firm-name   { font-size: 1rem; font-weight: 700; color: #1a1a2e; }
.ad-firm-tagline{ font-size: .83rem; color: #6b7280; margin-top: 2px; }
.ad-firm-meta   { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 8px; font-size: .82rem; color: #6b7280; }
.ad-firm-meta i { color: #9ca3af; }
.ad-firm-right  { margin-left: auto; display: flex; flex-direction: column; align-items: flex-end; gap: 10px; flex-shrink: 0; }

.ad-verify-btn {
    padding: 8px 18px; background: #059669; color: #fff;
    border: none; border-radius: 9px; font-size: .84rem; font-weight: 700;
    cursor: pointer; font-family: inherit; display: inline-flex; align-items: center; gap: 6px;
    transition: background .15s;
}
.ad-verify-btn:hover { background: #047857; }
.ad-unverify-btn {
    padding: 8px 18px; background: #fff; color: #dc2626;
    border: 1.5px solid #fca5a5; border-radius: 9px; font-size: .84rem; font-weight: 600;
    cursor: pointer; font-family: inherit; display: inline-flex; align-items: center; gap: 6px;
    transition: background .15s;
}
.ad-unverify-btn:hover { background: #fef2f2; }

.ad-pill { display: inline-flex; align-items: center; gap: 4px; font-size: .72rem; font-weight: 600; padding: 4px 11px; border-radius: 20px; }
.pill-verified   { background: #d1fae5; color: #065f46; }
.pill-unverified { background: #fee2e2; color: #991b1b; }

.ad-alert-success { background: #ecfdf5; border: 1px solid #6ee7b7; color: #065f46; border-radius: 12px; padding: 12px 18px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; font-size: .9rem; }
</style>

@if(session('success'))
<div class="ad-alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

<form method="GET" action="{{ route('admin.law-firms') }}">
    <div class="ad-filter-bar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search firm name or city…">
        <select name="verified">
            <option value="">All Firms</option>
            <option value="1" {{ request('verified')==='1' ? 'selected' : '' }}>Verified Only</option>
            <option value="0" {{ request('verified')==='0' ? 'selected' : '' }}>Unverified Only</option>
        </select>
        <button type="submit" class="ad-filter-btn"><i class="fas fa-search"></i> Filter</button>
        @if(request()->anyFilled(['search','verified']))
        <a href="{{ route('admin.law-firms') }}" class="ad-filter-clear">Clear</a>
        @endif
    </div>
</form>

@forelse($firms as $firm)
<div class="ad-firm-card">
    <div class="ad-firm-init">{{ strtoupper(substr($firm->firm_name, 0, 2)) }}</div>
    <div style="flex:1;min-width:0;">
        <div class="ad-firm-name">{{ $firm->firm_name }}</div>
        @if($firm->tagline)
        <div class="ad-firm-tagline">{{ $firm->tagline }}</div>
        @endif
        <div class="ad-firm-meta">
            @if($firm->city)<span><i class="fas fa-map-marker-alt"></i> {{ $firm->city }}</span>@endif
            @if($firm->phone)<span><i class="fas fa-phone"></i> {{ $firm->phone }}</span>@endif
            <span><i class="fas fa-user"></i> Admin: {{ $firm->user->name ?? '—' }}</span>
            <span><i class="fas fa-envelope"></i> {{ $firm->user->email ?? '—' }}</span>
            <span><i class="fas fa-star" style="color:#f59e0b;"></i> {{ number_format($firm->rating,1) }} ({{ $firm->reviews_count }} reviews)</span>
            @if($firm->founded_year)<span><i class="fas fa-landmark"></i> Est. {{ $firm->founded_year }}</span>@endif
        </div>
        @if(!empty($firm->specialties))
        <div style="margin-top:10px;display:flex;flex-wrap:wrap;gap:5px;">
            @foreach($firm->specialties as $sp)
            <span style="background:#f0f7f2;color:#1a3d2b;font-size:.75rem;padding:2px 9px;border-radius:20px;border:1px solid #bfd9c8;">{{ $sp }}</span>
            @endforeach
        </div>
        @endif
    </div>
    <div class="ad-firm-right">
        @if($firm->is_verified)
            <span class="ad-pill pill-verified"><i class="fas fa-circle-check"></i> Verified</span>
            <form method="POST" action="{{ route('admin.law-firms.unverify', $firm) }}">
                @csrf
                <button type="submit" class="ad-unverify-btn"
                    onclick="return confirm('Revoke verification for {{ addslashes($firm->firm_name) }}?')">
                    <i class="fas fa-times-circle"></i> Revoke
                </button>
            </form>
        @else
            <span class="ad-pill pill-unverified"><i class="fas fa-clock"></i> Unverified</span>
            <form method="POST" action="{{ route('admin.law-firms.verify', $firm) }}">
                @csrf
                <button type="submit" class="ad-verify-btn">
                    <i class="fas fa-circle-check"></i> Verify Firm
                </button>
            </form>
        @endif
    </div>
</div>
@empty
<div style="text-align:center;padding:72px 20px;color:#9ca3af;">
    <i class="fas fa-building-columns" style="font-size:2.5rem;display:block;margin-bottom:14px;opacity:.3;"></i>
    <p>No law firms found.</p>
</div>
@endforelse

@if($firms->hasPages())
<div style="margin-top:16px;">{{ $firms->links() }}</div>
@endif

@endsection
