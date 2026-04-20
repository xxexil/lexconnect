@extends('layouts.lawfirm')
@section('title', 'Consultations')
@section('content')

<div class="lp-page-header">
    <div>
        <h1 class="lp-page-title">Firm Consultations</h1>
        <p class="lp-page-sub">All consultations handled by your team</p>
    </div>
</div>

{{-- ── SUMMARY BAR ── --}}
<div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:20px;">
    @foreach([
        ['Awaiting Lawyer', $pending->count(),   '#f59e0b', 'fa-hourglass-half'],
        ['Upcoming',        $upcoming->count(),  '#3b82f6', 'fa-calendar-check'],
        ['Completed',       $completed->count(), '#16a34a', 'fa-check-circle'],
        ['Cancelled',       $cancelled->count(), '#dc2626', 'fa-times-circle'],
        ['Expired',         $expired->count(),   '#6c757d', 'fa-clock'],
    ] as [$label, $count, $color, $icon])
    <div style="display:flex;align-items:center;gap:8px;background:#fff;border:1.5px solid #e8edf5;border-radius:10px;padding:10px 18px;box-shadow:0 1px 4px rgba(0,0,0,.04);">
        <i class="fas {{ $icon }}" style="color:{{ $color }};font-size:.95rem;"></i>
        <span style="font-size:1.1rem;font-weight:700;color:#1e2d4d;">{{ $count }}</span>
        <span style="font-size:.82rem;color:#6c757d;">{{ $label }}</span>
    </div>
    @endforeach
</div>

{{-- ── SEARCH ── --}}
<div style="margin-bottom:16px;max-width:400px;">
    <div style="display:flex;align-items:center;gap:8px;background:#fff;border:1.5px solid #e2e8f0;border-radius:10px;padding:9px 14px;">
        <i class="fas fa-search" style="color:#adb5bd;font-size:.85rem;"></i>
        <input type="text" id="consSearch" placeholder="Search by client or lawyer name..." autocomplete="off"
            style="border:none;outline:none;font-size:.88rem;color:#1e2d4d;width:100%;background:transparent;"
            oninput="filterConsultations(this.value)">
    </div>
</div>

{{-- ── TABS ── --}}
<div class="lp-tabs" style="margin-bottom:24px;">
    <button class="lp-tab active" onclick="showTab('pending', this)">
        Awaiting Lawyer @if($pending->count() > 0)<span class="lp-count-badge" style="margin-left:4px;">{{ $pending->count() }}</span>@endif
    </button>
    <button class="lp-tab" onclick="showTab('upcoming', this)">
        Upcoming @if($upcoming->count() > 0)<span class="lp-count-badge upcoming" style="margin-left:4px;">{{ $upcoming->count() }}</span>@endif
    </button>
    <button class="lp-tab" onclick="showTab('completed', this)">Completed</button>
    <button class="lp-tab" onclick="showTab('cancelled', this)">Cancelled</button>
    <button class="lp-tab" onclick="showTab('expired', this)">Expired</button>
</div>

@php
$tabs = [
    'pending'   => ['consultations' => $pending,   'color' => '#f59e0b', 'label' => 'Awaiting Lawyer'],
    'upcoming'  => ['consultations' => $upcoming,  'color' => '#3b82f6', 'label' => 'Upcoming'],
    'completed' => ['consultations' => $completed, 'color' => '#16a34a', 'label' => 'Completed'],
    'cancelled' => ['consultations' => $cancelled, 'color' => '#dc2626', 'label' => 'Cancelled'],
    'expired'   => ['consultations' => $expired,   'color' => '#6c757d', 'label' => 'Expired'],
];
@endphp

@foreach($tabs as $status => $tab)
<div id="tab-{{ $status }}" class="lf-tab-content" @if($status !== 'pending') style="display:none;" @endif>
    @forelse($tab['consultations'] as $c)
    @php
        $sched    = \Carbon\Carbon::parse($c->scheduled_at);
        $isToday  = $sched->isToday();
        $isTomorrow = $sched->isTomorrow();
    @endphp
    <div class="lp-consult-card cons-search-item"
         data-search="{{ strtolower($c->client->name . ' ' . $c->lawyer->name) }}"
         style="border-left-color:{{ $tab['color'] }};">
        <div style="display:flex;align-items:flex-start;gap:16px;flex-wrap:wrap;">
            <div style="flex:1;min-width:200px;">
                {{-- Client → Lawyer row --}}
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:6px;">
                    <div style="width:36px;height:36px;border-radius:50%;background:#1e2d4d;color:#fff;display:flex;align-items:center;justify-content:center;font-size:.85rem;font-weight:700;flex-shrink:0;">
                        {{ strtoupper(substr($c->client->name,0,1)) }}
                    </div>
                    <div>
                        <span style="font-weight:700;color:#1e2d4d;font-size:.95rem;">{{ $c->client->name }}</span>
                        <span style="color:#adb5bd;margin:0 4px;">→</span>
                        <span style="color:#6c757d;font-size:.88rem;font-weight:600;">Atty. {{ $c->lawyer->name }}</span>
                    </div>
                    <span class="lp-type-badge {{ $c->type }}">{{ ucfirst($c->type) }}</span>
                    @if($status === 'upcoming')
                        @if($isToday)
                            <span style="font-size:.75rem;font-weight:700;color:#dc2626;background:#fff5f5;padding:2px 8px;border-radius:20px;border:1px solid #fca5a5;">Today</span>
                        @elseif($isTomorrow)
                            <span style="font-size:.75rem;font-weight:700;color:#f59e0b;background:#fffbeb;padding:2px 8px;border-radius:20px;border:1px solid #fcd34d;">Tomorrow</span>
                        @endif
                    @endif
                    @if($status === 'completed' && isset($c->balancePayment))
                        @if($c->balancePayment->status === 'paid')
                            <span style="font-size:.73rem;font-weight:600;color:#16a34a;background:#f0fdf4;padding:2px 8px;border-radius:20px;border:1px solid #bbf7d0;"><i class="fas fa-check-circle" style="margin-right:3px;"></i>Fully Paid</span>
                        @elseif($c->balancePayment->status === 'pending')
                            <span style="font-size:.73rem;font-weight:600;color:#f59e0b;background:#fffbeb;padding:2px 8px;border-radius:20px;border:1px solid #fcd34d;"><i class="fas fa-hourglass-half" style="margin-right:3px;"></i>Balance Pending</span>
                        @endif
                    @endif
                </div>
                {{-- Meta row --}}
                <div style="display:flex;flex-wrap:wrap;gap:6px 14px;font-size:.8rem;color:#6c757d;">
                    <span><i class="fas fa-calendar" style="margin-right:3px;"></i>{{ $sched->format('M j, Y') }}</span>
                    <span><i class="fas fa-clock" style="margin-right:3px;"></i>{{ $sched->format('g:i A') }}</span>
                    <span><i class="fas fa-hourglass-half" style="margin-right:3px;"></i>{{ $c->duration_label }}</span>
                    <span style="font-weight:600;color:#1e2d4d;"><i class="fas fa-peso-sign" style="margin-right:2px;"></i>₱{{ number_format($c->price, 2) }}</span>
                    <span style="background:#f0f2f5;padding:2px 8px;border-radius:4px;font-size:.75rem;">{{ $c->code ?? 'N/A' }}</span>
                </div>
                @if($c->notes)
                <div style="margin-top:6px;font-size:.8rem;color:#6c757d;font-style:italic;background:#f8f9fa;padding:5px 10px;border-radius:6px;border-left:2px solid #d1d5db;">
                    "{{ \Illuminate\Support\Str::limit($c->notes, 100) }}"
                </div>
                @endif
                @if($c->case_document)
                <div style="margin-top:8px;display:flex;gap:6px;flex-wrap:wrap;">
                    <a href="{{ asset('storage/' . $c->case_document) }}" target="_blank" rel="noopener"
                       style="display:inline-flex;align-items:center;gap:6px;font-size:.78rem;font-weight:600;color:#2563eb;background:#eff6ff;padding:4px 12px;border-radius:7px;text-decoration:none;border:1px solid #bfdbfe;">
                        <i class="fas fa-paperclip"></i> View
                    </a>
                    <a href="{{ asset('storage/' . $c->case_document) }}" download
                       style="display:inline-flex;align-items:center;gap:6px;font-size:.78rem;font-weight:600;color:#16a34a;background:#f0fdf4;padding:4px 12px;border-radius:7px;text-decoration:none;border:1px solid #bbf7d0;">
                        <i class="fas fa-download"></i> Download
                    </a>
                </div>
                @endif
            </div>
            <span class="lp-status-badge {{ $c->status }}">{{ ucfirst($c->status) }}</span>
        </div>
    </div>
    @empty
    <div style="padding:40px;text-align:center;">
        <i class="fas fa-calendar" style="font-size:2rem;color:#dee2e6;display:block;margin-bottom:12px;"></i>
        <div style="font-size:.9rem;color:#6c757d;">No {{ $tab['label'] }} consultations</div>
    </div>
    @endforelse

    {{-- Pagination bar --}}
    <div id="pg-bar-{{ $status }}" style="display:none;align-items:center;justify-content:center;gap:16px;padding:18px 0 6px;">
        <button onclick="changePage('{{ $status }}',-1)" class="lf-cons-pg-btn"><i class="fas fa-chevron-left"></i> Previous</button>
        <span id="pg-info-{{ $status }}" style="font-size:.85rem;color:#6c757d;min-width:110px;text-align:center;"></span>
        <button onclick="changePage('{{ $status }}',1)" class="lf-cons-pg-btn">Next <i class="fas fa-chevron-right"></i></button>
    </div>
</div>
@endforeach

@endsection

@push('styles')
<style>
.lf-cons-pg-btn { display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border:1.5px solid #d1d5db;border-radius:8px;background:#fff;color:#1e2d4d;font-size:.85rem;font-weight:600;cursor:pointer;font-family:inherit;transition:all .15s; }
.lf-cons-pg-btn:hover:not(:disabled) { background:#1e2d4d;color:#fff;border-color:#1e2d4d; }
.lf-cons-pg-btn:disabled { opacity:.35;cursor:not-allowed; }
</style>
@endpush

@push('scripts')
<script>
const PAGE_SIZE = 10;
const tabPages = { pending:1, upcoming:1, completed:1, cancelled:1, expired:1 };

function paginateTab(tabName) {
    const container = document.getElementById('tab-' + tabName);
    const cards = Array.from(container.querySelectorAll('.cons-search-item')).filter(c => c.style.display !== 'none');
    const total = cards.length;
    const page  = tabPages[tabName];
    const totalPages = Math.max(1, Math.ceil(total / PAGE_SIZE));
    const start = (page - 1) * PAGE_SIZE;
    const end   = start + PAGE_SIZE;

    // Hide all visible cards first
    Array.from(container.querySelectorAll('.cons-search-item')).forEach(c => {
        if (c.style.display !== 'none') c.style.display = 'none';
    });
    cards.forEach((c, i) => { c.style.display = (i >= start && i < end) ? '' : 'none'; });

    const bar  = document.getElementById('pg-bar-' + tabName);
    const info = document.getElementById('pg-info-' + tabName);
    if (!bar || !info) return;
    info.textContent = total === 0 ? 'No records' : (start+1) + '–' + Math.min(end,total) + ' of ' + total;
    bar.querySelectorAll('.lf-cons-pg-btn')[0].disabled = page <= 1;
    bar.querySelectorAll('.lf-cons-pg-btn')[1].disabled = page >= totalPages;
    bar.style.display = total <= PAGE_SIZE ? 'none' : 'flex';
}

function changePage(tabName, dir) {
    const container = document.getElementById('tab-' + tabName);
    const cards = Array.from(container.querySelectorAll('.cons-search-item')).filter(c => c.style.display !== 'none');
    const totalPages = Math.max(1, Math.ceil(cards.length / PAGE_SIZE));
    tabPages[tabName] = Math.min(Math.max(1, tabPages[tabName] + dir), totalPages);
    paginateTab(tabName);
    container.scrollIntoView({ behavior:'smooth', block:'start' });
}

function filterConsultations(q) {
    q = q.toLowerCase();
    ['pending','upcoming','completed','cancelled','expired'].forEach(function(tab) {
        document.querySelectorAll('#tab-' + tab + ' .cons-search-item').forEach(function(card) {
            card.style.display = card.dataset.search.includes(q) ? '' : 'none';
        });
        tabPages[tab] = 1;
        paginateTab(tab);
    });
}

function showTab(name, btn) {
    document.querySelectorAll('.lf-tab-content').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.lp-tab').forEach(el => el.classList.remove('active'));
    document.getElementById('tab-' + name).style.display = 'block';
    btn.classList.add('active');
}

// Auto-open tab from URL hash
(function() {
    const hash = window.location.hash.replace('#', '');
    const valid = ['pending','upcoming','completed','cancelled','expired'];
    if (valid.includes(hash)) {
        const btn = document.querySelector('.lp-tab[onclick*="' + hash + '"]');
        if (btn) showTab(hash, btn);
    }
    // Init pagination for all tabs
    valid.forEach(paginateTab);
})();
</script>
@endpush
