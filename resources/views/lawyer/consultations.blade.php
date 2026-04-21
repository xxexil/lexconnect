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
@if(session('error'))
    <div class="lp-alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
@endif

{{-- ── SUMMARY BAR ── --}}
<div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:20px;">
    @php
        $summaryItems = [
            ['label'=>'Pending',   'count'=>$pending->count(),   'color'=>'#f59e0b','icon'=>'fa-hourglass-half'],
            ['label'=>'Upcoming',  'count'=>$upcoming->count(),  'color'=>'#3b82f6','icon'=>'fa-calendar-check'],
            ['label'=>'Completed', 'count'=>$completed->count(), 'color'=>'#16a34a','icon'=>'fa-check-circle'],
            ['label'=>'Cancelled', 'count'=>$cancelled->count(), 'color'=>'#dc2626','icon'=>'fa-times-circle'],
            ['label'=>'Expired',   'count'=>$expired->count(),   'color'=>'#6c757d','icon'=>'fa-clock'],
        ];
    @endphp
    @foreach($summaryItems as $s)
    <div style="display:flex;align-items:center;gap:8px;background:#fff;border:1.5px solid #e8edf5;border-radius:10px;padding:10px 18px;box-shadow:0 1px 4px rgba(0,0,0,.04);">
        <i class="fas {{ $s['icon'] }}" style="color:{{ $s['color'] }};font-size:.95rem;"></i>
        <span style="font-size:1.1rem;font-weight:700;color:#1e2d4d;">{{ $s['count'] }}</span>
        <span style="font-size:.82rem;color:#6c757d;">{{ $s['label'] }}</span>
    </div>
    @endforeach
</div>

{{-- ── TABS ── --}}
<div class="lp-tabs">
    <button class="lp-tab active" onclick="showTab('pending',this)">
        Pending @if($pending->count() > 0)<span class="lp-tab-badge">{{ $pending->count() }}</span>@endif
    </button>
    <button class="lp-tab" onclick="showTab('upcoming',this)">
        Upcoming @if($upcoming->count() > 0)<span class="lp-tab-badge upcoming">{{ $upcoming->count() }}</span>@endif
    </button>
    <button class="lp-tab" onclick="showTab('completed',this)">
        Completed
    </button>
    <button class="lp-tab" onclick="showTab('cancelled',this)">Cancelled</button>
    <button class="lp-tab" onclick="showTab('expired',this)">Expired</button>
</div>

{{-- ── PENDING TAB ── --}}
<div id="tab-pending" class="lp-tab-content">
    @forelse($pending as $c)
    @php $sched = \Carbon\Carbon::parse($c->scheduled_at); @endphp
    <div class="cons-card cons-pending">
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
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;">
                <span class="cons-badge cons-badge-pending"><span class="cons-dot"></span> Pending</span>
                <span style="font-size:.75rem;color:#9ca3af;"><i class="fas fa-clock" style="margin-right:3px;"></i>Requested {{ $c->created_at->diffForHumans() }}</span>
            </div>
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
                <div style="display:flex;gap:6px;flex-wrap:wrap;"><a href="{{ asset('storage/' . $c->case_document) }}" target="_blank" rel="noopener" class="cons-doc-link"><i class="fas fa-paperclip"></i> View</a><a href="{{ asset('storage/' . $c->case_document) }}" download class="cons-doc-link" style="background:#f0fdf4;color:#16a34a;border-color:#bbf7d0;"><i class="fas fa-download"></i> Download</a></div>
                @endif
            </div>
            <div class="cons-actions">
                <form method="POST" action="{{ route('lawyer.consultations.accept', $c->id) }}">
                    @csrf
                    <button type="button"
                        class="cons-btn cons-btn-accept js-consultation-confirm"
                        data-action="accept"
                        data-client="{{ $c->client->name }}"
                        data-date="{{ $sched->format('F j, Y') }}"
                        data-time="{{ $sched->format('g:i A') }}">
                        <i class="fas fa-check"></i> Accept
                    </button>
                </form>
                <form method="POST" action="{{ route('lawyer.consultations.decline', $c->id) }}">
                    @csrf
                    <button type="button"
                        class="cons-btn cons-btn-decline js-consultation-confirm"
                        data-action="decline"
                        data-client="{{ $c->client->name }}"
                        data-date="{{ $sched->format('F j, Y') }}"
                        data-time="{{ $sched->format('g:i A') }}">
                        <i class="fas fa-times"></i> Decline
                    </button>
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
        $sched       = \Carbon\Carbon::parse($c->scheduled_at);
        $joinOpensAt = $c->videoJoinOpensAt();
        $canJoin     = $c->canJoinVideoCall();
        $diffHuman   = $sched->isFuture() ? $sched->diffForHumans() : 'Now';
        $isToday     = $sched->isToday();
        $isTomorrow  = $sched->isTomorrow();
        $urgencyLabel = $isToday ? 'Today' : ($isTomorrow ? 'Tomorrow' : $sched->diffForHumans());
        $urgencyColor = $isToday ? '#dc2626' : ($isTomorrow ? '#f59e0b' : '#3b82f6');
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
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;">
                <span class="cons-badge cons-badge-upcoming"><span class="cons-dot"></span> Upcoming</span>
                <span style="font-size:.75rem;font-weight:600;color:{{ $urgencyColor }};"><i class="fas fa-bolt" style="margin-right:3px;"></i>{{ $urgencyLabel }}</span>
            </div>
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
                <div style="display:flex;gap:6px;flex-wrap:wrap;"><a href="{{ asset('storage/' . $c->case_document) }}" target="_blank" rel="noopener" class="cons-doc-link"><i class="fas fa-paperclip"></i> View</a><a href="{{ asset('storage/' . $c->case_document) }}" download class="cons-doc-link" style="background:#f0fdf4;color:#16a34a;border-color:#bbf7d0;"><i class="fas fa-download"></i> Download</a></div>
                @endif
            </div>
            <div class="cons-actions">
                @if($c->type === 'video')
                    @if($canJoin)
                    <a href="{{ route('consultations.video', $c) }}" class="cons-btn cons-btn-join">
                        <i class="fas fa-video"></i> Join Call
                    </a>
                    @else
                    <span class="cons-btn cons-btn-waiting js-video-join-waiting"
                          title="Available at {{ $joinOpensAt->format('g:i A') }}"
                          data-join-opens-at="{{ $joinOpensAt->timestamp * 1000 }}"
                          data-join-url="{{ route('consultations.video', $c) }}">
                        <i class="fas fa-clock"></i> Available {{ $joinOpensAt->format('g:i A') }}
                    </span>
                    @endif
                @endif
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
    @php
        $sched          = \Carbon\Carbon::parse($c->scheduled_at);
        $balancePaid    = $c->balancePayment && $c->balancePayment->status === 'paid';
        $balancePending = $c->balancePayment && $c->balancePayment->status === 'pending';
    @endphp
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
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;">
                <span class="cons-badge cons-badge-completed"><span class="cons-dot"></span> Completed</span>
                @if($balancePaid)
                    <span style="font-size:.75rem;font-weight:600;color:#16a34a;"><i class="fas fa-check-circle" style="margin-right:3px;"></i>Fully Paid</span>
                @elseif($balancePending)
                    <span style="font-size:.75rem;font-weight:600;color:#f59e0b;"><i class="fas fa-hourglass-half" style="margin-right:3px;"></i>Awaiting Balance Payment</span>
                @else
                    <span style="font-size:.75rem;color:#9ca3af;"><i class="fas fa-peso-sign" style="margin-right:3px;"></i>Downpayment only</span>
                @endif
            </div>
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
                @if($c->notes)
                <div class="cons-notes"><i class="fas fa-comment-alt"></i> {{ $c->notes }}</div>
                @endif
                @if($c->case_document)
                <div style="display:flex;gap:6px;flex-wrap:wrap;"><a href="{{ asset('storage/' . $c->case_document) }}" target="_blank" rel="noopener" class="cons-doc-link"><i class="fas fa-paperclip"></i> View</a><a href="{{ asset('storage/' . $c->case_document) }}" download class="cons-doc-link" style="background:#f0fdf4;color:#16a34a;border-color:#bbf7d0;"><i class="fas fa-download"></i> Download</a></div>
                @endif
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
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;">
                <span class="cons-badge cons-badge-cancelled"><span class="cons-dot"></span> Cancelled</span>
                <span style="font-size:.75rem;color:#9ca3af;"><i class="fas fa-calendar-times" style="margin-right:3px;"></i>Cancelled {{ $c->updated_at->diffForHumans() }}</span>
            </div>
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
                    <span class="cons-chip"><i class="fas fa-hourglass-half"></i> {{ $c->duration_label }}</span>
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
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;">
                <span class="cons-badge cons-badge-expired"><span class="cons-dot"></span> Expired</span>
                <span style="font-size:.75rem;color:#9ca3af;" title="Session ended without being completed">
                    <i class="fas fa-info-circle" style="margin-right:3px;"></i>Session time passed
                </span>
            </div>
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

<div class="consult-modal" id="consultActionModal" aria-hidden="true">
    <div class="consult-modal-backdrop" data-modal-close></div>
    <div class="consult-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="consultModalTitle">
        <button type="button" class="consult-modal-close" data-modal-close aria-label="Close">&times;</button>
        <div class="consult-modal-icon" id="consultModalIcon"><i class="fas fa-check"></i></div>
        <h2 id="consultModalTitle">Confirm action</h2>
        <div class="consult-modal-meta" id="consultModalMeta"></div>
        <div class="consult-modal-actions">
            <button type="button" class="consult-modal-cancel" data-modal-close>Cancel</button>
            <button type="button" class="consult-modal-confirm" id="consultModalConfirm">Confirm</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showTab(name, btn) {
    document.querySelectorAll('.lp-tab-content').forEach(t => t.style.display = 'none');
    document.querySelectorAll('.lp-tab').forEach(t => t.classList.remove('active'));
    document.getElementById('tab-' + name).style.display = 'block';
    btn.classList.add('active');
}

// Auto-open tab from URL hash
(function() {
    const hash = window.location.hash.replace('#', '');
    const validTabs = ['pending', 'upcoming', 'completed', 'cancelled', 'expired'];
    if (validTabs.includes(hash)) {
        const btn = document.querySelector('.lp-tab[onclick*="' + hash + '"]');
        if (btn) showTab(hash, btn);
    }
})();

// ── Pagination ──
const PAGE_SIZE = 10;
const tabPages = { pending: 1, upcoming: 1, completed: 1, cancelled: 1, expired: 1 };

function paginateTab(tabName) {
    const container = document.getElementById('tab-' + tabName);
    const cards = Array.from(container.querySelectorAll('.cons-card'));
    const empty  = container.querySelector('.cons-empty');
    const total  = cards.length;
    const page   = tabPages[tabName];
    const totalPages = Math.max(1, Math.ceil(total / PAGE_SIZE));
    const start  = (page - 1) * PAGE_SIZE;
    const end    = start + PAGE_SIZE;

    cards.forEach((c, i) => c.style.display = (i >= start && i < end) ? '' : 'none');
    if (empty) empty.style.display = total === 0 ? '' : 'none';

    const bar = container.querySelector('.cons-pagination');
    if (!bar) return;
    bar.querySelector('.pg-info').textContent =
        total === 0 ? 'No records' : `${start + 1}–${Math.min(end, total)} of ${total}`;
    bar.querySelector('.pg-prev').disabled = page <= 1;
    bar.querySelector('.pg-next').disabled = page >= totalPages;
    bar.style.display = total <= PAGE_SIZE ? 'none' : 'flex';
}

function buildPaginationBar(tabName) {
    const bar = document.createElement('div');
    bar.className = 'cons-pagination';
    bar.innerHTML = `
        <button class="pg-prev" onclick="changePage('${tabName}',-1)"><i class="fas fa-chevron-left"></i> Previous</button>
        <span class="pg-info"></span>
        <button class="pg-next" onclick="changePage('${tabName}',1)">Next <i class="fas fa-chevron-right"></i></button>
    `;
    document.getElementById('tab-' + tabName).appendChild(bar);
}

function changePage(tabName, dir) {
    const container = document.getElementById('tab-' + tabName);
    const cards = container.querySelectorAll('.cons-card');
    const totalPages = Math.max(1, Math.ceil(cards.length / PAGE_SIZE));
    tabPages[tabName] = Math.min(Math.max(1, tabPages[tabName] + dir), totalPages);
    paginateTab(tabName);
    container.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

['pending','upcoming','completed','cancelled','expired'].forEach(tab => {
    buildPaginationBar(tab);
    paginateTab(tab);
});

function refreshJoinButtons() {
    var now = Date.now();
    document.querySelectorAll('.js-video-join-waiting').forEach(function(button) {
        var opensAt = parseInt(button.dataset.joinOpensAt || '0', 10);
        var joinUrl = button.dataset.joinUrl;
        if (!opensAt || !joinUrl || now < opensAt) return;
        var link = document.createElement('a');
        link.href = joinUrl;
        link.className = 'cons-btn cons-btn-join';
        link.innerHTML = '<i class="fas fa-video"></i> Join Call';
        button.replaceWith(link);
    });
}

refreshJoinButtons();
setInterval(refreshJoinButtons, 1000);

const consultModal = document.getElementById('consultActionModal');
const consultModalIcon = document.getElementById('consultModalIcon');
const consultModalTitle = document.getElementById('consultModalTitle');
const consultModalMeta = document.getElementById('consultModalMeta');
const consultModalConfirm = document.getElementById('consultModalConfirm');
let pendingConsultationForm = null;

function openConsultationModal(button) {
    const action = button.dataset.action;
    const client = button.dataset.client || 'this client';
    const date = button.dataset.date || '';
    const time = button.dataset.time || '';
    const isAccept = action === 'accept';

    pendingConsultationForm = button.closest('form');
    consultModalTitle.textContent = isAccept ? 'Accept Consultation?' : 'Decline Consultation?';
    consultModalMeta.textContent = `${client} · ${date} ${time}`.trim();
    consultModalIcon.innerHTML = isAccept ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times"></i>';
    consultModalIcon.className = isAccept ? 'consult-modal-icon accept' : 'consult-modal-icon decline';
    consultModalConfirm.textContent = isAccept ? 'Accept Request' : 'Decline Request';
    consultModalConfirm.className = isAccept ? 'consult-modal-confirm accept' : 'consult-modal-confirm decline';
    consultModal.classList.add('open');
    consultModal.setAttribute('aria-hidden', 'false');
    consultModalConfirm.focus();
}

function closeConsultationModal() {
    consultModal.classList.remove('open');
    consultModal.setAttribute('aria-hidden', 'true');
    pendingConsultationForm = null;
}

document.querySelectorAll('.js-consultation-confirm').forEach(function(button) {
    button.addEventListener('click', function() {
        openConsultationModal(button);
    });
});

document.querySelectorAll('[data-modal-close]').forEach(function(button) {
    button.addEventListener('click', closeConsultationModal);
});

consultModalConfirm.addEventListener('click', function() {
    if (pendingConsultationForm) {
        consultModalConfirm.disabled = true;
        pendingConsultationForm.submit();
    }
});

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape' && consultModal.classList.contains('open')) {
        closeConsultationModal();
    }
});
</script>

<style>
.consult-modal {
    position: fixed; inset: 0; z-index: 9999; display: none; align-items: center; justify-content: center; padding: 18px;
}
.consult-modal.open { display: flex; }
.consult-modal-backdrop {
    position: absolute; inset: 0; background: rgba(15, 23, 42, .55);
}
.consult-modal-dialog {
    position: relative; z-index: 1; width: min(420px, 100%); background: #fff; border-radius: 8px;
    box-shadow: 0 24px 60px rgba(15, 23, 42, .22); padding: 24px; text-align: center;
}
.consult-modal-close {
    position: absolute; top: 10px; right: 12px; width: 30px; height: 30px; border: none; border-radius: 6px;
    background: #f1f5f9; color: #475569; font-size: 1.25rem; line-height: 1; cursor: pointer;
}
.consult-modal-icon {
    width: 46px; height: 46px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center;
    margin-bottom: 14px; font-size: 1.1rem;
}
.consult-modal-icon.accept { background: #dcfce7; color: #16a34a; }
.consult-modal-icon.decline { background: #fee2e2; color: #dc2626; }
.consult-modal-dialog h2 { margin: 0 0 8px; font-size: 1.15rem; color: #0f172a; }
.consult-modal-meta {
    margin: 14px 0 20px; padding: 10px 12px; border-radius: 8px; background: #f8fafc; color: #334155;
    font-size: .85rem; font-weight: 700;
}
.consult-modal-actions { display: flex; gap: 10px; justify-content: center; }
.consult-modal-cancel,
.consult-modal-confirm {
    border: none; border-radius: 8px; padding: 10px 16px; font-size: .88rem; font-weight: 700; cursor: pointer;
    font-family: inherit;
}
.consult-modal-cancel { background: #e5e7eb; color: #334155; }
.consult-modal-confirm.accept { background: #16a34a; color: #fff; }
.consult-modal-confirm.decline { background: #dc2626; color: #fff; }
.consult-modal-confirm:disabled { opacity: .65; cursor: wait; }
.cons-pagination {
    display: flex; align-items: center; justify-content: center; gap: 16px; padding: 18px 0 6px;
}
.cons-pagination .pg-prev, .cons-pagination .pg-next {
    display: flex; align-items: center; gap: 6px; padding: 8px 18px;
    border: 1.5px solid #d1d5db; border-radius: 8px; background: #fff;
    color: #1e2d4d; font-size: .85rem; font-weight: 600; cursor: pointer;
    font-family: inherit; transition: all .15s;
}
.cons-pagination .pg-prev:hover:not(:disabled), .cons-pagination .pg-next:hover:not(:disabled) {
    background: #1e2d4d; color: #fff; border-color: #1e2d4d;
}
.cons-pagination .pg-prev:disabled, .cons-pagination .pg-next:disabled { opacity: .35; cursor: not-allowed; }
.cons-pagination .pg-info { font-size: .85rem; color: #6c757d; min-width: 110px; text-align: center; }
</style>
@endpush

@endsection
