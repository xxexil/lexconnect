
@extends('layouts.admin')
@section('title', 'Lawyers')
@section('page-title', 'Lawyers')
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
    background: linear-gradient(135deg, #6366f1, #7c3aed);
    color: #fff; font-size: 1.1rem; font-weight: 800;
    display: flex; align-items: center; justify-content: center; letter-spacing: -1px;
    overflow: hidden;
}
.ad-firm-init img {
    width: 100%; height: 100%; object-fit: cover; border-radius: 14px;
    display: block;
}
.ad-firm-name   { font-size: 1.08rem; font-weight: 700; color: #1a1a2e; }
.ad-lawyer-email  { font-size: .93rem; color: #64748b; margin-bottom: 2px; }
.ad-firm-meta   { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 8px; font-size: .85rem; color: #6b7280; }
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
.pill-certified   { background: #d1fae5; color: #065f46; }
.pill-uncertified { background: #fee2e2; color: #991b1b; }
.pill-available   { background: #dbeafe; color: #1e40af; }
.pill-busy        { background: #fef9c3; color: #92400e; }
.pill-offline     { background: #e5e7eb; color: #374151; }
.ad-alert-success { background: #ecfdf5; border: 1px solid #6ee7b7; color: #065f46; border-radius: 12px; padding: 12px 18px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; font-size: .9rem; }
</style>

@if(session('success'))
<div class="ad-alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

<form method="GET" action="{{ route('admin.lawyers') }}">
    <div class="ad-filter-bar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, or specialty…">
        <select name="certified">
            <option value="">All Lawyers</option>
            <option value="1" {{ request('certified')==='1' ? 'selected' : '' }}>Certified Only</option>
            <option value="0" {{ request('certified')==='0' ? 'selected' : '' }}>Uncertified Only</option>
        </select>
        <button type="submit" class="ad-filter-btn"><i class="fas fa-search"></i> Filter</button>
        @if(request()->anyFilled(['search','certified']))
        <a href="{{ route('admin.lawyers') }}" class="ad-filter-clear">Clear</a>
        @endif
    </div>
</form>

@forelse($lawyers as $lp)
<div class="ad-firm-card">
    <div class="ad-firm-init">
        @if($lp->user->avatar_url)
            <img src="{{ $lp->user->avatar_url }}" alt="{{ $lp->user->name }}">
        @else
            {{ strtoupper(substr($lp->user->name ?? 'L', 0, 2)) }}
        @endif
    </div>
    <div style="flex:1;min-width:0;">
        <div class="ad-firm-name">{{ $lp->user->name ?? '—' }}</div>
        <div class="ad-lawyer-email">{{ $lp->user->email ?? '—' }}</div>
        <div class="ad-firm-meta">
            <span><i class="fas fa-balance-scale"></i> {{ $lp->specialty ?? 'Not specified' }}</span>
            @if($lp->firm)<span><i class="fas fa-building"></i> {{ $lp->firm }}</span>@endif
            @if($lp->location)<span><i class="fas fa-map-marker-alt"></i> {{ $lp->location }}</span>@endif
            <span><i class="fas fa-clock"></i> {{ $lp->experience_years }} yrs exp.</span>
            <span><i class="fas fa-peso-sign"></i> ₱{{ number_format($lp->hourly_rate, 0) }}/hr</span>
            <span><i class="fas fa-star" style="color:#f59e0b;"></i> {{ number_format($lp->rating,1) }}</span>
            <span class="ad-pill pill-{{ $lp->currentStatusClass() }}">{{ $lp->currentStatusLabel() }}</span>
        </div>
    </div>
    <div class="ad-firm-right">
        <button class="ad-verify-btn" onclick="openDocsModal({{ $lp->id }})">Review Docs</button>
        @if($lp->is_certified)
            <span class="ad-pill pill-certified"><i class="fas fa-circle-check"></i> Certified</span>
            <form method="POST" action="{{ route('admin.lawyers.uncertify', $lp) }}">
                @csrf
                <button type="submit" class="ad-unverify-btn"
                    onclick="return confirm('Revoke certification for {{ addslashes($lp->user->name ?? '') }}?')">
                    <i class="fas fa-times-circle"></i> Revoke
                </button>
            </form>
        @else
            <span class="ad-pill pill-uncertified"><i class="fas fa-clock"></i> Uncertified</span>
            <form method="POST" action="{{ route('admin.lawyers.certify', $lp) }}">
                @csrf
                <button type="submit" class="ad-verify-btn">
                    <i class="fas fa-circle-check"></i> Certify
                </button>
            </form>
        @endif
    </div>
</div>
@empty
<div style="text-align:center;padding:72px 20px;color:#9ca3af;">
    <i class="fas fa-gavel" style="font-size:2.5rem;display:block;margin-bottom:14px;opacity:.3;"></i>
    <p>No lawyers found.</p>
</div>
@endforelse

@if($lawyers->hasPages())
<div style="margin-top:16px;">{{ $lawyers->links() }}</div>
@endif

<!-- Modal Structure -->
<div id="lawyerDocsModal" style="display:none;position:fixed;z-index:9999;top:0;left:0;width:100vw;height:100vh;background:rgba(15,23,42,.28);backdrop-filter:blur(6px);align-items:center;justify-content:center;padding:24px;">
    <div id="lawyerDocsModalContent" style="background:#fff;border-radius:18px;max-width:540px;width:96vw;max-height:90vh;overflow-y:auto;box-shadow:0 18px 48px rgba(15,23,42,.22);padding:32px 28px;position:relative;">
        <button onclick="closeDocsModal()" style="position:absolute;top:18px;right:18px;background:none;border:none;font-size:1.5rem;color:#64748b;cursor:pointer;">&times;</button>
        <div id="lawyerDocsModalBody">
            <div style="text-align:center;padding:40px 0;color:#64748b;">Loading…</div>
        </div>
    </div>
</div>

<script>
function openDocsModal(lawyerId) {
    document.getElementById('lawyerDocsModal').style.display = 'flex';
    var body = document.getElementById('lawyerDocsModalBody');
    var panel = document.getElementById('lawyerDocsModalContent');
    var panelClose = panel.querySelector('button');
    body.innerHTML = '<div style="text-align:center;padding:40px 0;color:#64748b;">Loading…</div>';
    panel.style.background = '#fff';
    panel.style.borderRadius = '18px';
    panel.style.maxWidth = '540px';
    panel.style.padding = '32px 28px';
    panel.style.boxShadow = '0 18px 48px rgba(15,23,42,.22)';
    if (panelClose) panelClose.style.display = 'block';
    fetch('/admin/lawyers/' + lawyerId)
        .then(resp => resp.text())
        .then(html => {
            // Extract only the content inside #lawyer-details-content if present
            var temp = document.createElement('div');
            temp.innerHTML = html;
            var details = temp.querySelector('#lawyer-details-content');
            if (details) {
                body.innerHTML = details.outerHTML;
                panel.style.background = 'transparent';
                panel.style.borderRadius = '0';
                panel.style.maxWidth = '680px';
                panel.style.padding = '0';
                panel.style.boxShadow = 'none';
                if (panelClose) panelClose.style.display = 'none';
            } else {
                body.innerHTML = html;
            }
        })
        .catch(() => {
            body.innerHTML = '<div style="color:#dc2626;text-align:center;padding:40px 0;">Failed to load details.</div>';
        });
}
function closeDocsModal() {
    document.getElementById('lawyerDocsModal').style.display = 'none';
}
</script>
@endsection


