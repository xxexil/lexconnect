@extends('layouts.lawyer')
@section('title', 'Consultations')
@section('content')

<div class="lp-page-header">
    <div>
        <h1 class="lp-page-title">Consultations</h1>
        <p class="lp-page-sub">Manage all your client consultations</p>
    </div>
</div>

@if(session('success'))
    <div class="lp-alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

{{-- TABS --}}
<div class="lp-tabs">
    <button class="lp-tab active" onclick="showTab('pending',this)">
        Pending @if($pending->count() > 0)<span class="lp-tab-badge">{{ $pending->count() }}</span>@endif
    </button>
    <button class="lp-tab" onclick="showTab('upcoming',this)">
        Upcoming @if($upcoming->count() > 0)<span class="lp-tab-badge upcoming">{{ $upcoming->count() }}</span>@endif
    </button>
    <button class="lp-tab" onclick="showTab('completed',this)">
        Completed @if($completed->count() > 0)<span class="lp-tab-badge">{{ $completed->count() }}</span>@endif
    </button>
    <button class="lp-tab" onclick="showTab('cancelled',this)">Cancelled</button>
    <button class="lp-tab" onclick="showTab('expired',this)">Expired</button>
</div>

{{-- ── PENDING TAB ── --}}
<div id="tab-pending" class="lp-tab-content">
    @forelse($pending as $c)
    @php $sched = \Carbon\Carbon::parse($c->scheduled_at); @endphp
    <div class="cons-card cons-pending">
        {{-- Header --}}
        <div class="cons-header">
            <div class="cons-avatar" style="{{ $c->client->avatar_url ? 'padding:0;overflow:hidden;' : '' }}">
                @if($c->client->avatar_url)
                    <img src="{{ $c->client->avatar_url }}" style="width:46px;height:46px;border-radius:50%;object-fit:cover;display:block;">
                @else
                    {{ strtoupper(substr($c->client->name,0,2)) }}
                @endif
            </div>
            <div class="cons-head-info">
                <div class="cons-client-name">{{ $c->client->name }}</div>
                <div class="cons-code">{{ $c->code }}</div>
            </div>
            <span class="cons-badge cons-badge-pending"><span class="cons-dot"></span> Pending</span>
        </div>
        {{-- Body --}}
        <div class="cons-body">
            <div class="cons-body-left">
                <div class="cons-datetime">
                    <i class="fas fa-calendar-alt"></i>
                    <strong>{{ $sched->format('l, F j, Y') }}</strong>
                    <span class="cons-time-sep">·</span>
                    <i class="fas fa-clock"></i> {{ $sched->format('g:i A') }}
                </div>
                <div class="cons-chips">
                    @if($c->type === 'video')
                        <span class="cons-chip cons-chip-video">📲 Video Call</span>
                    @else
                        <span class="cons-chip cons-chip-person">🤝 In-Person</span>
                    @endif
                    <span class="cons-chip"><i class="fas fa-hourglass-half"></i> {{ $c->duration_label }}</span>
                    <span class="cons-chip cons-chip-price"><i class="fas fa-peso-sign"></i> {{ number_format($c->price, 2) }}</span>
                </div>
                @if($c->notes)
                <div class="cons-notes"><i class="fas fa-comment-alt"></i> {{ $c->notes }}</div>
                @endif
                @if($c->case_document)
                <a href="{{ asset('storage/' . $c->case_document) }}" target="_blank" rel="noopener" class="cons-doc-link">
                    <i class="fas fa-paperclip"></i> View Client Document
                </a>
                @endif
            </div>
            <div class="cons-actions">
                <form method="POST" action="{{ route('lawyer.consultations.accept', $c->id) }}">
                    @csrf
                    <button class="cons-btn cons-btn-accept"><i class="fas fa-check"></i> Accept</button>
                </form>
                <form method="POST" action="{{ route('lawyer.consultations.decline', $c->id) }}">
                    @csrf
                    <button class="cons-btn cons-btn-decline" onclick="return confirm('Decline this request?')"><i class="fas fa-times"></i> Decline</button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="cons-empty"><i class="fas fa-inbox"></i><p>No pending requests</p></div>
    @endforelse
</div>

{{-- ── UPCOMING TAB ── --}}
<div id="tab-upcoming" class="lp-tab-content" style="display:none;">
    @forelse($upcoming as $c)
    @php
        $sched   = \Carbon\Carbon::parse($c->scheduled_at);
        $canJoin = now()->gte($sched->copy()->subMinutes(5));
    @endphp
    <div class="cons-card cons-upcoming">
        <div class="cons-header">
            <div class="cons-avatar cons-avatar-blue" style="{{ $c->client->avatar_url ? 'padding:0;overflow:hidden;' : '' }}">
                @if($c->client->avatar_url)
                    <img src="{{ $c->client->avatar_url }}" style="width:46px;height:46px;border-radius:50%;object-fit:cover;display:block;">
                @else
                    {{ strtoupper(substr($c->client->name,0,2)) }}
                @endif
            </div>
            <div class="cons-head-info">
                <div class="cons-client-name">{{ $c->client->name }}</div>
                <div class="cons-code">{{ $c->code }}</div>
            </div>
            <span class="cons-badge cons-badge-upcoming"><span class="cons-dot"></span> Upcoming</span>
        </div>
        <div class="cons-body">
            <div class="cons-body-left">
                <div class="cons-datetime">
                    <i class="fas fa-calendar-alt"></i>
                    <strong>{{ $sched->format('l, F j, Y') }}</strong>
                    <span class="cons-time-sep">·</span>
                    <i class="fas fa-clock"></i> {{ $sched->format('g:i A') }}
                </div>
                <div class="cons-chips">
                    @if($c->type === 'video')
                        <span class="cons-chip cons-chip-video">📲 Video Call</span>
                    @else
                        <span class="cons-chip cons-chip-person">🤝 In-Person</span>
                    @endif
                    <span class="cons-chip"><i class="fas fa-hourglass-half"></i> {{ $c->duration_label }}</span>
                    <span class="cons-chip cons-chip-price"><i class="fas fa-peso-sign"></i> {{ number_format($c->price, 2) }}</span>
                </div>
                @if($c->notes)
                <div class="cons-notes"><i class="fas fa-comment-alt"></i> {{ $c->notes }}</div>
                @endif
                @if($c->case_document)
                <a href="{{ asset('storage/' . $c->case_document) }}" target="_blank" rel="noopener" class="cons-doc-link">
                    <i class="fas fa-paperclip"></i> View Client Document
                </a>
                @endif
            </div>
            <div class="cons-actions">
                @if($c->type === 'video')
                    @if($canJoin)
                    <a href="{{ route('consultations.video', $c) }}" class="cons-btn cons-btn-join">
                        <i class="fas fa-video"></i> Join Call
                    </a>
                    @else
                    <span class="cons-btn cons-btn-waiting" title="Available at {{ $sched->format('g:i A') }}">
                        <i class="fas fa-clock"></i> Starts {{ $sched->format('g:i A') }}
                    </span>
                    @endif
                @endif
                <form method="POST" action="{{ route('lawyer.consultations.complete', $c->id) }}">
                    @csrf
                    <button class="cons-btn cons-btn-complete" onclick="return confirm('Mark session as completed?')">
                        <i class="fas fa-check-circle"></i> Mark Complete
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="cons-empty"><i class="fas fa-calendar-alt"></i><p>No upcoming consultations</p></div>
    @endforelse
</div>

{{-- ── COMPLETED TAB ── --}}
<div id="tab-completed" class="lp-tab-content" style="display:none;">
    @forelse($completed as $c)
    @php $sched = \Carbon\Carbon::parse($c->scheduled_at); @endphp
    <div class="cons-card cons-completed">
        <div class="cons-header">
            <div class="cons-avatar cons-avatar-green" style="{{ $c->client->avatar_url ? 'padding:0;overflow:hidden;' : '' }}">
                @if($c->client->avatar_url)
                    <img src="{{ $c->client->avatar_url }}" style="width:46px;height:46px;border-radius:50%;object-fit:cover;display:block;">
                @else
                    {{ strtoupper(substr($c->client->name,0,2)) }}
                @endif
            </div>
            <div class="cons-head-info">
                <div class="cons-client-name">{{ $c->client->name }}</div>
                <div class="cons-code">{{ $c->code }}</div>
            </div>
            <span class="cons-badge cons-badge-completed"><span class="cons-dot"></span> Completed</span>
        </div>
        <div class="cons-body">
            <div class="cons-body-left">
                <div class="cons-datetime">
                    <i class="fas fa-calendar-check"></i>
                    <strong>{{ $sched->format('l, F j, Y') }}</strong>
                    <span class="cons-time-sep">·</span>
                    <i class="fas fa-clock"></i> {{ $sched->format('g:i A') }}
                </div>
                <div class="cons-chips">
                    @if($c->type === 'video')
                        <span class="cons-chip cons-chip-video">📲 Video Call</span>
                    @else
                        <span class="cons-chip cons-chip-person">🤝 In-Person</span>
                    @endif
                    <span class="cons-chip"><i class="fas fa-hourglass-half"></i> {{ $c->duration_label }}</span>
                    <span class="cons-chip cons-chip-price"><i class="fas fa-peso-sign"></i> {{ number_format($c->price, 2) }}</span>
                </div>
            </div>
            <div class="cons-actions" style="justify-content:flex-start;">
                <div class="cons-earned">
                    <div style="font-size:.72rem;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;margin-bottom:2px;">Earned</div>
                    <div style="font-size:1.2rem;font-weight:800;color:#059669;">₱{{ number_format($c->price, 2) }}</div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="cons-empty"><i class="fas fa-check-circle"></i><p>No completed consultations yet</p></div>
    @endforelse
</div>

{{-- ── CANCELLED TAB ── --}}
<div id="tab-cancelled" class="lp-tab-content" style="display:none;">
    @forelse($cancelled as $c)
    @php $sched = \Carbon\Carbon::parse($c->scheduled_at); @endphp
    <div class="cons-card cons-cancelled">
        <div class="cons-header">
            <div class="cons-avatar cons-avatar-red" style="{{ $c->client->avatar_url ? 'padding:0;overflow:hidden;' : '' }}">
                @if($c->client->avatar_url)
                    <img src="{{ $c->client->avatar_url }}" style="width:46px;height:46px;border-radius:50%;object-fit:cover;display:block;">
                @else
                    {{ strtoupper(substr($c->client->name,0,2)) }}
                @endif
            </div>
            <div class="cons-head-info">
                <div class="cons-client-name">{{ $c->client->name }}</div>
                <div class="cons-code">{{ $c->code }}</div>
            </div>
            <span class="cons-badge cons-badge-cancelled"><span class="cons-dot"></span> Cancelled</span>
        </div>
        <div class="cons-body">
            <div class="cons-body-left">
                <div class="cons-datetime">
                    <i class="fas fa-calendar-times"></i>
                    <strong>{{ $sched->format('l, F j, Y') }}</strong>
                    <span class="cons-time-sep">·</span>
                    <i class="fas fa-clock"></i> {{ $sched->format('g:i A') }}
                </div>
                <div class="cons-chips">
                    @if($c->type === 'video')
                        <span class="cons-chip cons-chip-video">📲 Video Call</span>
                    @else
                        <span class="cons-chip cons-chip-person">🤝 In-Person</span>
                    @endif
                    <span class="cons-chip cons-chip-price"><i class="fas fa-peso-sign"></i> {{ number_format($c->price, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="cons-empty"><i class="fas fa-times-circle"></i><p>No cancelled consultations</p></div>
    @endforelse
</div>

{{-- ── EXPIRED TAB ── --}}
<div id="tab-expired" class="lp-tab-content" style="display:none;">
    @forelse($expired as $c)
    @php $sched = \Carbon\Carbon::parse($c->scheduled_at); @endphp
    <div class="cons-card cons-expired">
        <div class="cons-header">
            <div class="cons-avatar cons-avatar-gray" style="{{ $c->client->avatar_url ? 'padding:0;overflow:hidden;' : '' }}">
                @if($c->client->avatar_url)
                    <img src="{{ $c->client->avatar_url }}" style="width:46px;height:46px;border-radius:50%;object-fit:cover;display:block;">
                @else
                    {{ strtoupper(substr($c->client->name,0,2)) }}
                @endif
            </div>
            <div class="cons-head-info">
                <div class="cons-client-name">{{ $c->client->name }}</div>
                <div class="cons-code">{{ $c->code }}</div>
            </div>
            <span class="cons-badge cons-badge-expired"><span class="cons-dot"></span> Expired</span>
        </div>
        <div class="cons-body">
            <div class="cons-body-left">
                <div class="cons-datetime">
                    <i class="fas fa-clock"></i>
                    <strong>{{ $sched->format('l, F j, Y') }}</strong>
                    <span class="cons-time-sep">·</span>
                    {{ $sched->format('g:i A') }}
                </div>
                <div class="cons-chips">
                    @if($c->type === 'video')
                        <span class="cons-chip cons-chip-video">📲 Video Call</span>
                    @else
                        <span class="cons-chip cons-chip-person">🤝 In-Person</span>
                    @endif
                    <span class="cons-chip"><i class="fas fa-hourglass-half"></i> {{ $c->duration_label }}</span>
                    <span class="cons-chip cons-chip-price"><i class="fas fa-peso-sign"></i> {{ number_format($c->price, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="cons-empty"><i class="fas fa-clock"></i><p>No expired consultations</p></div>
    @endforelse
</div>

@push('scripts')
<script>
function showTab(name, btn) {
    document.querySelectorAll('.lp-tab-content').forEach(t => t.style.display = 'none');
    document.querySelectorAll('.lp-tab').forEach(t => t.classList.remove('active'));
    document.getElementById('tab-' + name).style.display = 'block';
    btn.classList.add('active');
}
</script>
@endpush

@endsection
