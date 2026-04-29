@extends('layouts.app')
@section('title', 'My Consultations')
@section('content')

<style>
/* ── Page header ── */
.mc-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:28px; }
.mc-title   { font-size:1.6rem; font-weight:700; color:#1e2d4d; }
.mc-sub     { font-size:.88rem; color:#6b7280; margin-top:3px; }
.mc-summary {
    display:grid;
    grid-template-columns:repeat(5, minmax(0, 1fr));
    gap:14px;
    margin-bottom:28px;
}
.mc-summary-card {
    display:flex;
    align-items:center;
    gap:12px;
    background:#fff;
    border:1px solid #e7edf6;
    border-radius:16px;
    padding:16px 18px;
    box-shadow:0 1px 4px rgba(0,0,0,.04);
}
.mc-summary-icon {
    width:38px;
    height:38px;
    border-radius:12px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:1rem;
    flex-shrink:0;
}
.mc-summary-icon.pending { background:#fff8ea; color:#d97706; }
.mc-summary-icon.upcoming { background:#eff6ff; color:#2563eb; }
.mc-summary-icon.completed { background:#ecfdf5; color:#16a34a; }
.mc-summary-icon.cancelled { background:#fef2f2; color:#dc2626; }
.mc-summary-icon.expired { background:#f3f4f6; color:#6b7280; }
.mc-summary-metric {
    display:flex;
    align-items:baseline;
    gap:8px;
    min-width:0;
}
.mc-summary-value {
    font-size:1.9rem;
    line-height:1;
    font-weight:800;
    color:#1e2d4d;
}
.mc-summary-label {
    font-size:.82rem;
    color:#667085;
    font-weight:500;
}
.mc-status-tabs {
    display:flex;
    align-items:center;
    gap:4px;
    flex-wrap:wrap;
    padding:6px;
    margin:-8px 0 20px;
    background:#fff;
    border-radius:12px;
    box-shadow:0 2px 8px rgba(0,0,0,.05);
    width:fit-content;
    max-width:100%;
}
.mc-status-tab {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:7px;
    padding:10px 22px;
    border:none;
    border-radius:8px;
    background:transparent;
    color:#6c757d;
    font-size:.88rem;
    font-weight:600;
    font-family:inherit;
    cursor:pointer;
    transition:all .2s;
    white-space:nowrap;
}
.mc-status-tab:hover {
    background:#f4f7fc;
    color:#1e2d4d;
}
.mc-status-tab.is-active {
    background:#1e2d4d;
    color:#fff;
    box-shadow:0 6px 18px rgba(30,45,77,.12);
}
.mc-status-tab-badge {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width:auto;
    height:auto;
    padding:2px 7px;
    border-radius:10px;
    background:#dc3545;
    color:#fff;
    font-size:.7rem;
    font-weight:700;
    line-height:1;
}
.mc-status-tab.is-active .mc-status-tab-badge {
    background:rgba(255,255,255,.18);
}
.mc-book-btn {
    display:inline-flex; align-items:center; gap:7px;
    padding:10px 22px; background:#1e2d4d; color:#fff;
    border-radius:10px; font-size:.9rem; font-weight:600;
    text-decoration:none; transition:background .2s;
}
.mc-book-btn:hover { background:#162340; color:#fff; text-decoration:none; }

/* ── Section label ── */
.mc-section-label {
    display:inline-flex; align-items:center; gap:8px;
    font-size:.95rem; font-weight:700; color:#1e2d4d;
    margin-bottom:16px; margin-top:10px;
}

/* ── Consultation card ── */
.mc-card {
    background:#fff;
    border:1px solid #e8edf5;
    border-radius:16px;
    padding:22px 24px;
    margin-bottom:16px;
    box-shadow:0 1px 4px rgba(0,0,0,.05);
    display:flex;
    gap:18px;
    align-items:flex-start;
    transition:box-shadow .15s;
}
.mc-card:hover { box-shadow:0 4px 16px rgba(0,0,0,.08); }

/* Avatar */
.mc-avatar {
    width:56px; height:56px; border-radius:50%; flex-shrink:0;
    object-fit:cover; border:2px solid #e8edf5;
    background:#e8edf6; color:#1e2d4d;
    display:flex; align-items:center; justify-content:center;
    font-size:1rem; font-weight:700; overflow:hidden;
}
.mc-avatar img { width:100%; height:100%; object-fit:cover; border-radius:50%; }

/* Info section */
.mc-info { flex:1; min-width:0; }
.mc-lawyer-name { font-size:1rem; font-weight:700; color:#1e2d4d; display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
.mc-code-badge  { font-size:.72rem; background:#f0f4ff; color:#2563eb; border:1px solid #c7d9fb; border-radius:20px; padding:3px 10px; font-weight:600; }
.mc-specialty   { font-size:.83rem; color:#6b7280; margin-top:3px; }
.mc-meta        { display:flex; flex-wrap:wrap; gap:14px; margin-top:10px; }
.mc-meta-item   { display:inline-flex; align-items:center; gap:5px; font-size:.82rem; color:#6b7280; }
.mc-meta-item i { color:#9ca3af; }
.mc-notes       { font-size:.8rem; color:#6b7280; margin-top:8px; background:#f8faff; border-left:3px solid #c7d9fb; padding:6px 10px; border-radius:0 6px 6px 0; }

/* Right column */
.mc-right { display:flex; flex-direction:column; align-items:flex-end; gap:10px; flex-shrink:0; }
.mc-price { font-size:1.15rem; font-weight:700; color:#1e2d4d; }

/* Status badges */
.mc-badge { display:inline-flex; align-items:center; gap:5px; font-size:.75rem; font-weight:600; padding:4px 12px; border-radius:20px; }
.mc-badge.upcoming  { background:#eff8ff; color:#2563eb; }
.mc-badge.pending   { background:#fff8ea; color:#d97706; }
.mc-badge.completed { background:#ecfdf5; color:#059669; }
.mc-badge.expired   { background:#f3f4f6; color:#6b7280; }

/* review modal */
.rv-overlay {
    position: fixed; inset: 0; background: rgba(0,0,0,.45);
    display: flex; align-items: center; justify-content: center; z-index: 1000;
}
.rv-modal {
    background: #fff; border-radius: 16px; padding: 32px 36px;
    max-width: 480px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,.2);
}
.rv-modal h3 { font-size: 1.1rem; font-weight: 800; color: #1e2d4d; margin: 0 0 4px; }
.rv-modal p  { font-size: .85rem; color: #777; margin: 0 0 20px; }
.rv-stars { display: flex; gap: 6px; margin-bottom: 16px; }
.rv-star { font-size: 1.6rem; color: #d1d5db; cursor: pointer; transition: color .15s; }
.rv-star.active, .rv-star:hover ~ .rv-star { color: #d1d5db; }
.rv-star:hover, .rv-stars:hover .rv-star { color: #f59e0b; }
.rv-stars .rv-star:hover ~ .rv-star { color: #d1d5db !important; }
.rv-textarea {
    width: 100%; border: 1px solid #d1d5db; border-radius: 8px;
    padding: 10px 12px; font-size: .9rem; font-family: inherit;
    resize: vertical; min-height: 90px; margin-bottom: 18px;
    outline: none;
}
.rv-textarea:focus { border-color: #2563eb; }
.rv-actions { display: flex; gap: 10px; }
.rv-btn-submit {
    flex: 1; padding: 11px; background: #1e2d4d; color: #fff;
    border: none; border-radius: 8px; font-size: .9rem; font-weight: 700;
    cursor: pointer; font-family: inherit;
}
.rv-btn-submit:hover { background: #2563eb; }
.rv-btn-cancel {
    padding: 11px 20px; background: #f3f4f6; color: #444;
    border: none; border-radius: 8px; font-size: .9rem; font-weight: 600;
    cursor: pointer; font-family: inherit;
}
.rv-already { font-size: .8rem; color: #059669; font-weight: 600; display: flex; align-items: center; gap: 5px; }
.mc-badge.cancelled { background:#fef2f2; color:#dc2626; }
.mc-badge .dot      { width:6px; height:6px; border-radius:50%; background:currentColor; }

/* Type pill */
.mc-type { display:inline-flex; align-items:center; gap:5px; font-size:.78rem; font-weight:600; padding:4px 10px; border-radius:8px; background:#f3f4f6; color:#374151; }

/* Action buttons */
.mc-btn-join {
    display:inline-flex; align-items:center; gap:7px;
    padding:9px 20px; background:#2563eb; color:#fff;
    border-radius:9px; font-size:.87rem; font-weight:700;
    text-decoration:none; white-space:nowrap; border:none; cursor:pointer;
    font-family:inherit; transition:background .2s;
}
.mc-btn-join:hover { background:#1d4ed8; color:#fff; text-decoration:none; }
.mc-btn-cancel {
    display:inline-flex; align-items:center; gap:6px;
    padding:8px 16px; background:#fff; color:#dc2626;
    border:1.5px solid #fca5a5; border-radius:9px;
    font-size:.84rem; font-weight:600; cursor:pointer;
    font-family:inherit; white-space:nowrap; transition:background .15s;
}
.mc-btn-cancel:hover { background:#fef2f2; }

/* Alert */
.mc-alert-success {
    background:#ecfdf5; border:1px solid #6ee7b7; color:#065f46;
    border-radius:10px; padding:12px 18px; margin-bottom:20px;
    display:flex; align-items:center; gap:8px; font-size:.9rem;
}

/* Empty state */
.mc-empty { text-align:center; padding:72px 20px; color:#9ca3af; }
.mc-empty i { font-size:3rem; display:block; margin-bottom:16px; opacity:.35; }
.mc-empty h3 { font-size:1.1rem; font-weight:700; color:#374151; margin-bottom:8px; }
.mc-empty p  { font-size:.9rem; margin-bottom:20px; }
.mc-pagination { margin:10px 0 24px; }
.mc-pagination .pagination { justify-content:center; margin-bottom:0; }
.mc-panel { display:none; }
.mc-panel.is-active { display:block; }

@media (max-width: 1180px) {
    .mc-summary {
        grid-template-columns:repeat(3, minmax(0, 1fr));
    }
}

@media (max-width: 840px) {
    .mc-header {
        flex-direction:column;
        align-items:flex-start;
        gap:14px;
    }

    .mc-summary {
        grid-template-columns:repeat(2, minmax(0, 1fr));
    }

    .mc-status-tabs {
        gap:6px;
        padding:6px;
    }

    .mc-status-tab {
        flex:0 0 auto;
    }

    .mc-card {
        flex-direction:column;
    }

    .mc-right {
        width:100%;
        align-items:flex-start;
    }
}

@media (max-width: 560px) {
    .mc-summary {
        grid-template-columns:1fr;
    }

    .mc-summary-card {
        padding:14px 16px;
    }

    .mc-summary-value {
        font-size:1.6rem;
    }

    .mc-status-tabs {
        display:flex;
    }

    .mc-status-tab {
        width:auto;
        justify-content:center;
        padding:10px 22px;
        font-size:.88rem;
    }
}
</style>

{{-- Page header --}}
<div class="mc-header">
    <div>
        <div class="mc-title">My Consultations</div>
        <div class="mc-sub">Manage your legal consultations and appointments</div>
    </div>
    <a href="{{ route('find-lawyers') }}" class="mc-book-btn">
        <i class="fas fa-plus"></i> Book New
    </a>
</div>

@php
    $activePanel = request('section');

    if (!$activePanel) {
        if (request()->has('expired_page')) {
            $activePanel = 'expired';
        } elseif (request()->has('cancelled_page')) {
            $activePanel = 'cancelled';
        } elseif (request()->has('completed_page')) {
            $activePanel = 'completed';
        } elseif ($pendingConsultations->isNotEmpty()) {
            $activePanel = 'pending';
        } elseif ($upcomingConsultations->isNotEmpty()) {
            $activePanel = 'upcoming';
        } elseif ($completedConsultations->total() > 0) {
            $activePanel = 'completed';
        } elseif ($expiredConsultations->total() > 0) {
            $activePanel = 'expired';
        } elseif ($cancelledConsultations->total() > 0) {
            $activePanel = 'cancelled';
        } else {
            $activePanel = 'pending';
        }
    }
@endphp

<div class="mc-summary">
    <div class="mc-summary-card">
        <div class="mc-summary-icon pending"><i class="fas fa-hourglass-half"></i></div>
        <div class="mc-summary-metric">
            <span class="mc-summary-value">{{ $consultationSummary['pending'] }}</span>
            <span class="mc-summary-label">Pending</span>
        </div>
    </div>
    <div class="mc-summary-card">
        <div class="mc-summary-icon upcoming"><i class="fas fa-calendar-check"></i></div>
        <div class="mc-summary-metric">
            <span class="mc-summary-value">{{ $consultationSummary['upcoming'] }}</span>
            <span class="mc-summary-label">Upcoming</span>
        </div>
    </div>
    <div class="mc-summary-card">
        <div class="mc-summary-icon completed"><i class="fas fa-check-circle"></i></div>
        <div class="mc-summary-metric">
            <span class="mc-summary-value">{{ $consultationSummary['completed'] }}</span>
            <span class="mc-summary-label">Completed</span>
        </div>
    </div>
    <div class="mc-summary-card">
        <div class="mc-summary-icon cancelled"><i class="fas fa-times-circle"></i></div>
        <div class="mc-summary-metric">
            <span class="mc-summary-value">{{ $consultationSummary['cancelled'] }}</span>
            <span class="mc-summary-label">Cancelled</span>
        </div>
    </div>
    <div class="mc-summary-card">
        <div class="mc-summary-icon expired"><i class="fas fa-clock"></i></div>
        <div class="mc-summary-metric">
            <span class="mc-summary-value">{{ $consultationSummary['expired'] }}</span>
            <span class="mc-summary-label">Expired</span>
        </div>
    </div>
</div>

<div class="mc-status-tabs" aria-label="Consultation sections">
    <button type="button" class="mc-status-tab {{ $activePanel === 'pending' ? 'is-active' : '' }}" data-panel-target="pending">
        <span>Pending</span>
        @if($consultationSummary['pending'] > 0)
            <span class="mc-status-tab-badge">{{ $consultationSummary['pending'] }}</span>
        @endif
    </button>
    <button type="button" class="mc-status-tab {{ $activePanel === 'upcoming' ? 'is-active' : '' }}" data-panel-target="upcoming">
        <span>Upcoming</span>
        @if($consultationSummary['upcoming'] > 0)
            <span class="mc-status-tab-badge">{{ $consultationSummary['upcoming'] }}</span>
        @endif
    </button>
    <button type="button" class="mc-status-tab {{ $activePanel === 'completed' ? 'is-active' : '' }}" data-panel-target="completed">
        <span>Completed</span>
        @if($consultationSummary['completed'] > 0)
            <span class="mc-status-tab-badge">{{ $consultationSummary['completed'] }}</span>
        @endif
    </button>
    <button type="button" class="mc-status-tab {{ $activePanel === 'cancelled' ? 'is-active' : '' }}" data-panel-target="cancelled">
        <span>Cancelled</span>
        @if($consultationSummary['cancelled'] > 0)
            <span class="mc-status-tab-badge">{{ $consultationSummary['cancelled'] }}</span>
        @endif
    </button>
    <button type="button" class="mc-status-tab {{ $activePanel === 'expired' ? 'is-active' : '' }}" data-panel-target="expired">
        <span>Expired</span>
        @if($consultationSummary['expired'] > 0)
            <span class="mc-status-tab-badge">{{ $consultationSummary['expired'] }}</span>
        @endif
    </button>
</div>

@if(session('success'))
<div class="mc-alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

{{-- ── PENDING ── --}}
<div class="mc-panel {{ $activePanel === 'pending' ? 'is-active' : '' }}" data-panel="pending">
@if($pendingConsultations->isNotEmpty())
<div class="mc-section-label">
    <i class="fas fa-hourglass-half" style="color:#d97706;"></i> Awaiting Confirmation ({{ $pendingConsultations->count() }})
</div>
@foreach($pendingConsultations as $c)
@php $sched = \Carbon\Carbon::parse($c->scheduled_at); @endphp
<div class="mc-card" style="border-left:4px solid #f59e0b;">
    <div class="mc-avatar">
        <img src="{{ $c->lawyer->avatar_url ?? asset('images/default-avatar.png') }}" alt="{{ $c->lawyer->name }}">
    </div>
    <div class="mc-info">
        <div class="mc-lawyer-name">
            {{ $c->lawyer->name }}
            <span class="mc-code-badge">{{ $c->code }}</span>
        </div>
        <div class="mc-specialty">{{ optional($c->lawyer->lawyerProfile)->specialty ?? 'Attorney at Law' }}</div>
        <div class="mc-meta">
            <span class="mc-meta-item"><i class="fas fa-calendar"></i> {{ $sched->format('M d, Y') }}</span>
            <span class="mc-meta-item"><i class="fas fa-clock"></i> {{ $sched->format('g:i A') }}</span>
            <span class="mc-meta-item"><i class="fas fa-stopwatch"></i> {{ $c->duration_label }}</span>
            <span class="mc-type">
                <i class="fas fa-{{ $c->type === 'video' ? 'video' : ($c->type === 'phone' ? 'phone' : 'building') }}"></i>
                {{ ucfirst($c->type) }}
            </span>
        </div>
        @if($c->notes)
        <div class="mc-notes"><i class="fas fa-sticky-note"></i> {{ $c->notes }}</div>
        @endif

        {{-- Case Document --}}
        @if($c->case_document)
        <div style="margin-top:8px;display:flex;align-items:center;gap:8px;">
            <a href="{{ Storage::url($c->case_document) }}" target="_blank"
               style="display:inline-flex;align-items:center;gap:5px;font-size:.8rem;font-weight:600;color:#2563eb;text-decoration:none;background:#eff8ff;border:1px solid #bfdbfe;border-radius:7px;padding:5px 11px;">
                <i class="fas fa-paperclip"></i> View Attached Document
            </a>
            <button onclick="openReplaceDoc({{ $c->id }})" title="Replace document"
                style="background:none;border:none;font-size:.78rem;color:#9ca3af;cursor:pointer;text-decoration:underline;padding:0;">
                replace
            </button>
        </div>
        @else
        <div style="margin-top:8px;">
            <button onclick="openAttachDoc({{ $c->id }})"
                style="display:inline-flex;align-items:center;gap:5px;font-size:.8rem;font-weight:600;color:#b5860d;background:#fdfaf3;border:1.5px dashed #b5860d;border-radius:7px;padding:5px 11px;cursor:pointer;">
                <i class="fas fa-paperclip"></i> Attach Supporting Document
            </button>
        </div>
        @endif
    </div>
    <div class="mc-right">
        <span class="mc-badge pending"><span class="dot"></span> Pending Approval</span>
        <div class="mc-price">₱{{ number_format($c->price, 0) }}</div>
        <form method="POST" action="{{ route('consultations.cancel', $c) }}">
            @csrf
            <button type="submit" class="mc-btn-cancel"
                onclick="return confirm('Cancel this request?')">
                <i class="fas fa-times"></i> Cancel Request
            </button>
        </form>
    </div>
</div>
@endforeach
@endif
</div>

{{-- ── UPCOMING ── --}}
<div class="mc-panel {{ $activePanel === 'upcoming' ? 'is-active' : '' }}" data-panel="upcoming">
@if($upcomingConsultations->isNotEmpty())
<div class="mc-section-label" style="{{ $pendingConsultations->isNotEmpty() ? 'margin-top:28px;' : '' }}">
    <i class="fas fa-calendar-check" style="color:#2563eb;"></i> Upcoming ({{ $upcomingConsultations->count() }})
</div>
@foreach($upcomingConsultations as $c)
@php $sched = \Carbon\Carbon::parse($c->scheduled_at); @endphp
<div class="mc-card" style="border-left:4px solid #2563eb;">
    <div class="mc-avatar">
        <img src="{{ $c->lawyer->avatar_url ?? asset('images/default-avatar.png') }}" alt="{{ $c->lawyer->name }}">
    </div>
    <div class="mc-info">
        <div class="mc-lawyer-name">
            {{ $c->lawyer->name }}
            <span class="mc-code-badge">{{ $c->code }}</span>
        </div>
        <div class="mc-specialty">{{ optional($c->lawyer->lawyerProfile)->specialty ?? 'Attorney at Law' }}</div>
        <div class="mc-meta">
            <span class="mc-meta-item"><i class="fas fa-calendar"></i> {{ $sched->format('M d, Y') }}</span>
            <span class="mc-meta-item"><i class="fas fa-clock"></i> {{ $sched->format('g:i A') }}</span>
            <span class="mc-meta-item"><i class="fas fa-stopwatch"></i> {{ $c->duration_label }}</span>
            <span class="mc-type">
                <i class="fas fa-{{ $c->type === 'video' ? 'video' : ($c->type === 'phone' ? 'phone' : 'building') }}"></i>
                {{ ucfirst($c->type) }}
            </span>
        </div>
        @if($c->notes)
        <div class="mc-notes"><i class="fas fa-sticky-note"></i> {{ $c->notes }}</div>
        @endif

        {{-- Case Document --}}
        @if($c->case_document)
        <div style="margin-top:8px;">
            <a href="{{ Storage::url($c->case_document) }}" target="_blank"
               style="display:inline-flex;align-items:center;gap:5px;font-size:.8rem;font-weight:600;color:#2563eb;text-decoration:none;background:#eff8ff;border:1px solid #bfdbfe;border-radius:7px;padding:5px 11px;">
                <i class="fas fa-paperclip"></i> View Attached Document
            </a>
        </div>
        @endif
    </div>
    <div class="mc-right">
        <span class="mc-badge upcoming"><span class="dot"></span> Upcoming</span>
        <div class="mc-price">₱{{ number_format($c->price, 0) }}</div>
        @if($c->type === 'video')
        <a href="{{ route('consultations.video', $c) }}" class="mc-btn-join">
            <i class="fas fa-video"></i> Join Video Call
        </a>
        @endif
        <form method="POST" action="{{ route('consultations.cancel', $c) }}">
            @csrf
            <button type="submit" class="mc-btn-cancel"
                onclick="return confirm('Cancel this consultation?')">
                <i class="fas fa-times"></i> Cancel
            </button>
        </form>
    </div>
</div>
@endforeach
@endif

{{-- ── COMPLETED ── --}}
</div>
<div class="mc-panel {{ $activePanel === 'completed' ? 'is-active' : '' }}" data-panel="completed">
@if($completedConsultations->total() > 0)
<div class="mc-section-label" style="margin-top:28px;">
    <i class="fas fa-check-circle" style="color:#059669;"></i> Completed ({{ $completedConsultations->total() }})
</div>
@foreach($completedConsultations as $c)
@php $sched = \Carbon\Carbon::parse($c->scheduled_at); @endphp
@php $balancePayment = $c->payments->firstWhere('type', 'balance'); @endphp
<div class="mc-card" style="border-left:4px solid #059669;">
    <div class="mc-avatar">
        <img src="{{ $c->lawyer->avatar_url ?? asset('images/default-avatar.png') }}" alt="{{ $c->lawyer->name }}">
    </div>
    <div class="mc-info">
        <div class="mc-lawyer-name">{{ $c->lawyer->name }}</div>
        <div class="mc-specialty">{{ optional($c->lawyer->lawyerProfile)->specialty ?? 'Attorney at Law' }}</div>
        <div class="mc-meta">
            <span class="mc-meta-item"><i class="fas fa-calendar"></i> {{ $sched->format('M d, Y') }}</span>
            <span class="mc-meta-item"><i class="fas fa-stopwatch"></i> {{ $c->duration_label }}</span>
            <span class="mc-type">
                <i class="fas fa-{{ $c->type === 'video' ? 'video' : ($c->type === 'phone' ? 'phone' : 'building') }}"></i>
                {{ ucfirst($c->type) }}
            </span>
        </div>
        <div style="margin-top:8px;">
            @if($c->review)
                <span class="rv-already">
                    <i class="fas fa-star" style="color:#f59e0b;"></i>
                    You rated this {{ $c->review->rating }}/5
                </span>
            @else
                <button type="button" class="mc-action-btn"
                    style="background:#1e2d4d;color:#fff;border:none;border-radius:7px;padding:6px 14px;font-size:.8rem;font-weight:600;cursor:pointer;"
                    onclick="openReview({{ $c->id }}, '{{ addslashes($c->lawyer->name) }}')">
                    <i class="fas fa-star"></i> Leave a Review
                </button>
            @endif
        </div>
        @if($balancePayment)
        <div style="margin-top:10px;font-size:.82rem;color:#475569;">
            Remaining balance:
            @if($balancePayment->status === 'paid')
                <strong style="color:#059669;">Paid (₱{{ number_format($balancePayment->amount, 2) }})</strong>
            @else
                <strong style="color:#d97706;">Pending (₱{{ number_format($balancePayment->amount, 2) }})</strong>
            @endif
        </div>
        @endif
    </div>
    <div class="mc-right">
        <span class="mc-badge completed"><span class="dot"></span> Completed</span>
        @if($balancePayment && $balancePayment->status === 'pending')
        <a href="{{ route('payment.balance.start', $balancePayment) }}" class="mc-btn-join" style="background:#059669;">
            <i class="fas fa-credit-card"></i> Pay Remaining Balance
        </a>
        @endif
        <div class="mc-price">₱{{ number_format($c->price, 0) }}</div>
    </div>
</div>
@endforeach
@endif

{{-- ── CANCELLED ── --}}
@if($completedConsultations->hasPages())
<div class="mc-pagination">
    {{ $completedConsultations->appends(['section' => 'completed'])->links('vendor.pagination.client-clean') }}
</div>
@endif
</div>
<div class="mc-panel {{ $activePanel === 'cancelled' ? 'is-active' : '' }}" data-panel="cancelled">
@if($cancelledConsultations->total() > 0)
<div class="mc-section-label" style="margin-top:28px;">
    <i class="fas fa-ban" style="color:#dc2626;"></i> Cancelled ({{ $cancelledConsultations->total() }})
</div>
@foreach($cancelledConsultations as $c)
@php $sched = \Carbon\Carbon::parse($c->scheduled_at); @endphp
<div class="mc-card" style="border-left:4px solid #fca5a5; opacity:.8;">
    <div class="mc-avatar" style="opacity:.7;">
        <img src="{{ $c->lawyer->avatar_url ?? asset('images/default-avatar.png') }}" alt="{{ $c->lawyer->name }}">
    </div>
    <div class="mc-info">
        <div class="mc-lawyer-name">{{ $c->lawyer->name }}</div>
        <div class="mc-specialty">{{ optional($c->lawyer->lawyerProfile)->specialty ?? 'Attorney at Law' }}</div>
        <div class="mc-meta">
            <span class="mc-meta-item"><i class="fas fa-calendar"></i> {{ $sched->format('M d, Y') }}</span>
            <span class="mc-meta-item"><i class="fas fa-stopwatch"></i> {{ $c->duration_label }}</span>
        </div>
    </div>
    <div class="mc-right">
        <span class="mc-badge cancelled"><span class="dot"></span> Cancelled</span>
        <div class="mc-price" style="color:#9ca3af;">₱{{ number_format($c->price, 0) }}</div>
    </div>
</div>
@endforeach
@endif

{{-- ── EMPTY STATE ── --}}
@if($cancelledConsultations->hasPages())
<div class="mc-pagination">
    {{ $cancelledConsultations->appends(['section' => 'cancelled'])->links('vendor.pagination.client-clean') }}
</div>
@endif
 </div>
<div class="mc-panel {{ $activePanel === 'expired' ? 'is-active' : '' }}" data-panel="expired">
@if($expiredConsultations->total() > 0)
<div class="mc-section-label" style="margin-top:28px;">
    <i class="fas fa-clock" style="color:#6b7280;"></i> Expired ({{ $expiredConsultations->total() }})
</div>
@foreach($expiredConsultations as $c)
@php $sched = \Carbon\Carbon::parse($c->scheduled_at); @endphp
<div class="mc-card" style="border-left:4px solid #d1d5db; opacity:.88;">
    <div class="mc-avatar" style="opacity:.78;">
        <img src="{{ $c->lawyer->avatar_url ?? asset('images/default-avatar.png') }}" alt="{{ $c->lawyer->name }}">
    </div>
    <div class="mc-info">
        <div class="mc-lawyer-name">{{ $c->lawyer->name }}</div>
        <div class="mc-specialty">{{ optional($c->lawyer->lawyerProfile)->specialty ?? 'Attorney at Law' }}</div>
        <div class="mc-meta">
            <span class="mc-meta-item"><i class="fas fa-calendar"></i> {{ $sched->format('M d, Y') }}</span>
            <span class="mc-meta-item"><i class="fas fa-clock"></i> {{ $sched->format('g:i A') }}</span>
            <span class="mc-meta-item"><i class="fas fa-stopwatch"></i> {{ $c->duration_label }}</span>
            <span class="mc-type">
                <i class="fas fa-{{ $c->type === 'video' ? 'video' : ($c->type === 'phone' ? 'phone' : 'building') }}"></i>
                {{ ucfirst($c->type) }}
            </span>
        </div>
    </div>
    <div class="mc-right">
        <span class="mc-badge expired"><span class="dot"></span> Expired</span>
        <div class="mc-price" style="color:#6b7280;">&#8369;{{ number_format($c->price, 0) }}</div>
    </div>
</div>
@endforeach
@endif
@if($expiredConsultations->hasPages())
<div class="mc-pagination">
    {{ $expiredConsultations->appends(['section' => 'expired'])->links('vendor.pagination.client-clean') }}
</div>
@endif
 </div>
@if(!$hasConsultations)
<div class="mc-empty">
    <i class="fas fa-calendar-times"></i>
    <h3>No consultations yet</h3>
    <p>Book your first consultation with one of our certified lawyers.</p>
    <a href="{{ route('find-lawyers') }}" class="mc-book-btn" style="display:inline-flex;padding:9px 20px;font-size:.88rem;">
        <i class="fas fa-search"></i> Find a Lawyer
    </a>
</div>
@endif

{{-- ── Attach / Replace Document Modal ── --}}
<div id="docModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9000;align-items:center;justify-content:center;"
     onclick="if(event.target===this)closeDocModal()">
    <div style="background:#fff;border-radius:14px;max-width:420px;width:90%;padding:28px;box-shadow:0 20px 60px rgba(0,0,0,.2);">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
            <div style="font-size:1rem;font-weight:700;color:#1e2d4d;"><i class="fas fa-paperclip" style="color:#b5860d;margin-right:6px;"></i> <span id="docModalTitle">Attach Document</span></div>
            <button onclick="closeDocModal()" style="background:none;border:none;font-size:1.4rem;color:#9ca3af;cursor:pointer;">&times;</button>
        </div>
        <p style="font-size:.83rem;color:#6b7280;margin-bottom:16px;">Upload any supporting documents — contracts, receipts, IDs, court papers, etc. — to help your lawyer prepare.</p>
        <form method="POST" action="{{ route('consultations.attach-document') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="consultation_id" id="docConsultationId">
            <label style="display:flex;align-items:center;justify-content:center;gap:8px;padding:14px;border:2px dashed #b5860d;border-radius:8px;cursor:pointer;font-size:.88rem;font-weight:600;color:#b5860d;background:#fdfaf3;">
                <i class="fas fa-cloud-upload-alt" style="font-size:1.2rem;"></i>
                <span id="docUploadName">Click to choose file</span>
                <input type="file" name="case_document" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" required
                       style="display:none;" onchange="document.getElementById('docUploadName').textContent=this.files[0]?.name||'Click to choose file'">
            </label>
            <p style="font-size:.75rem;color:#9ca3af;margin:8px 0 18px;">Accepted: JPG, PNG, PDF, DOC, DOCX · Max 10 MB</p>
            <button type="submit" style="width:100%;padding:12px;background:#1e2d4d;color:#fff;border:none;border-radius:9px;font-size:.93rem;font-weight:700;cursor:pointer;font-family:inherit;">
                <i class="fas fa-upload"></i> Upload Document
            </button>
        </form>
    </div>
</div>

{{-- ── Review Modal ── --}}
<div id="reviewModal" class="rv-overlay" style="display:none;" onclick="if(event.target===this) closeReview()">
    <div class="rv-modal">
        <h3><i class="fas fa-star" style="color:#f59e0b;"></i> Leave a Review</h3>
        <p id="rv-lawyer-name" style="margin-bottom:12px;color:#1e2d4d;font-weight:600;font-size:.9rem;"></p>
        <form method="POST" action="{{ route('reviews.store') }}" id="reviewForm">
            @csrf
            <input type="hidden" name="consultation_id" id="rv-consultation-id">
            <input type="hidden" name="rating" id="rv-rating-input" value="0">

            <div class="rv-stars" id="rv-stars">
                @for($i = 1; $i <= 5; $i++)
                    <i class="fas fa-star rv-star" data-val="{{ $i }}" onclick="setRating({{ $i }})"></i>
                @endfor
            </div>
            <p style="font-size:.75rem;color:#aaa;margin:-10px 0 14px;">Click to select a rating</p>

            <textarea name="comment" class="rv-textarea" placeholder="Share your experience (optional)…"></textarea>

            <div class="rv-actions">
                <button type="submit" class="rv-btn-submit" id="rv-submit" disabled>
                    <i class="fas fa-paper-plane"></i> Submit Review
                </button>
                <button type="button" class="rv-btn-cancel" onclick="closeReview()">Cancel</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openAttachDoc(id) {
    document.getElementById('docConsultationId').value = id;
    document.getElementById('docModalTitle').textContent = 'Attach Document';
    document.getElementById('docUploadName').textContent = 'Click to choose file';
    document.getElementById('docModal').style.display = 'flex';
}
function openReplaceDoc(id) {
    document.getElementById('docConsultationId').value = id;
    document.getElementById('docModalTitle').textContent = 'Replace Document';
    document.getElementById('docUploadName').textContent = 'Click to choose file';
    document.getElementById('docModal').style.display = 'flex';
}
function closeDocModal() {
    document.getElementById('docModal').style.display = 'none';
}
function openReview(consultationId, lawyerName) {
    document.getElementById('rv-consultation-id').value = consultationId;
    document.getElementById('rv-lawyer-name').textContent = 'for ' + lawyerName;
    setRating(0);
    document.getElementById('reviewModal').style.display = 'flex';
}
function closeReview() {
    document.getElementById('reviewModal').style.display = 'none';
}
function setRating(val) {
    document.getElementById('rv-rating-input').value = val;
    document.querySelectorAll('.rv-star').forEach(function(s) {
        s.style.color = parseInt(s.dataset.val) <= val ? '#f59e0b' : '#d1d5db';
    });
    document.getElementById('rv-submit').disabled = val < 1;
}

document.addEventListener('DOMContentLoaded', function () {
    var tabButtons = document.querySelectorAll('[data-panel-target]');
    var panels = document.querySelectorAll('[data-panel]');

    function setActivePanel(panelName) {
        tabButtons.forEach(function (button) {
            button.classList.toggle('is-active', button.dataset.panelTarget === panelName);
        });

        panels.forEach(function (panel) {
            panel.classList.toggle('is-active', panel.dataset.panel === panelName);
        });
    }

    tabButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            setActivePanel(button.dataset.panelTarget);
        });
    });
});
</script>
@endpush

@endsection

