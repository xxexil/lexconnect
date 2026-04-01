@extends('layouts.lawyer')
@section('title', 'Dashboard')
@section('content')

<div class="lp-page-header">
    <div>
        <h1 class="lp-page-title">Dashboard</h1>
        <p class="lp-page-sub">Welcome back, {{ Auth::user()->name }}</p>
    </div>
    <div class="lp-avail-switcher">
        <span class="lp-avail-label">Status:</span>
        <span class="lp-avail-status">
            @if(($profile->availability_status ?? 'available') === 'available')
                🟢 Available
            @elseif(($profile->availability_status ?? 'available') === 'busy')
                🟡 Busy
            @else
                ⚫ Offline
            @endif
        </span>
    </div>
</div>

@if(session('success'))
    <div class="lp-alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

{{-- STAT CARDS --}}
<div class="lp-stats-grid">
    <div class="lp-stat-card">
        <div class="lp-stat-icon upcoming-icon"><i class="fas fa-calendar-check"></i></div>
        <div>
            <div class="lp-stat-num">{{ $upcomingCount }}</div>
            <div class="lp-stat-lbl">Upcoming Sessions</div>
        </div>
    </div>
    <div class="lp-stat-card">
        <div class="lp-stat-icon clients-icon"><i class="fas fa-user-friends"></i></div>
        <div>
            <div class="lp-stat-num">{{ $totalClients }}</div>
            <div class="lp-stat-lbl">Total Clients</div>
        </div>
    </div>
    <div class="lp-stat-card">
        <div class="lp-stat-icon earned-icon"><i class="fas fa-peso-sign"></i></div>
        <div>
            <div class="lp-stat-num">₱{{ number_format($totalEarned, 0) }}</div>
            <div class="lp-stat-lbl">Total Earned</div>
        </div>
    </div>
</div>


{{-- TODAY'S SCHEDULE --}}
<div class="lp-card">
    <div class="lp-card-header">
        <h2 class="lp-card-title"><i class="fas fa-calendar-day"></i> Today's Schedule
            <span class="lp-date-badge">{{ now()->format('M j, Y') }}</span>
        </h2>
    </div>
    @forelse($todayConsultations as $c)
    <div class="lp-schedule-item">
        <div class="lp-schedule-time">
            <div class="lp-sched-hr">{{ \Carbon\Carbon::parse($c->scheduled_at)->format('g:i') }}</div>
            <div class="lp-sched-ampm">{{ \Carbon\Carbon::parse($c->scheduled_at)->format('A') }}</div>
        </div>
        <div class="lp-schedule-line"></div>
        <div class="lp-schedule-body">
            <div class="lp-sched-client">{{ $c->client->name }}</div>
            <div class="lp-sched-meta">
                <span class="lp-type-badge {{ $c->type }}">{{ ucfirst($c->type) }}</span>
                <span>{{ $c->duration_label }}</span>
                <span>₱{{ number_format($c->price, 0) }}</span>
            </div>
        </div>
        <form method="POST" action="{{ route('lawyer.consultations.complete', $c->id) }}" style="margin-left:auto;">
            @csrf
            <button class="lp-btn-complete" onclick="return confirm('Mark as completed?')"><i class="fas fa-check-circle"></i></button>
        </form>
    </div>
    @empty
    <div class="lp-empty-sm"><i class="fas fa-sun"></i> No sessions today</div>
    @endforelse

    {{-- Recent activity --}}
    <div class="lp-card-divider">Recent Activity</div>
    @foreach($recentConsultations->take(4) as $c)
    <div class="lp-recent-row">
        <div class="lp-recent-dot {{ $c->status }}"></div>
        <div class="lp-recent-info">
            <span class="lp-recent-client">{{ $c->client->name }}</span>
            <span class="lp-recent-status {{ $c->status }}">{{ ucfirst($c->status) }}</span>
        </div>
        <div class="lp-recent-date">{{ \Carbon\Carbon::parse($c->scheduled_at)->format('M j') }}</div>
    </div>
    @endforeach
</div>

{{-- BLOCKED-DATES CALENDAR --}}
<div class="lp-card" style="margin-top:22px;">
    <div class="lp-card-header">
        <h2 class="lp-card-title"><i class="fas fa-calendar-alt"></i> Availability Calendar</h2>
    </div>
    <div style="padding:22px;">
        <p style="font-size:.85rem;color:#6c757d;margin:0 0 16px;">Click on a day to mark it as unavailable. Clients will see blocked days and won't be able to book on those dates.</p>

        {{-- Calendar navigation --}}
        <div class="bd-cal-nav">
            <button type="button" class="bd-cal-nav-btn" onclick="changeCalMonth(-1)"><i class="fas fa-chevron-left"></i></button>
            <span class="bd-cal-month" id="bdCalMonth"></span>
            <button type="button" class="bd-cal-nav-btn" onclick="changeCalMonth(1)"><i class="fas fa-chevron-right"></i></button>
        </div>

        {{-- Calendar grid --}}
        <div class="bd-cal-grid">
            <div class="bd-cal-day-head">Sun</div>
            <div class="bd-cal-day-head">Mon</div>
            <div class="bd-cal-day-head">Tue</div>
            <div class="bd-cal-day-head">Wed</div>
            <div class="bd-cal-day-head">Thu</div>
            <div class="bd-cal-day-head">Fri</div>
            <div class="bd-cal-day-head">Sat</div>
        </div>
        <div class="bd-cal-grid" id="bdCalDays"></div>

        {{-- Block date form (hidden by default) --}}
        <div id="bdBlockForm" style="display:none;margin-top:16px;padding:16px;background:#f8f9fa;border-radius:10px;">
            <form method="POST" action="{{ route('lawyer.blocked-dates.store') }}">
                @csrf
                <input type="hidden" name="blocked_date" id="bdBlockDateInput">
                <p style="font-size:.9rem;font-weight:600;color:#1e2d4d;margin:0 0 8px;">
                    Block <span id="bdBlockDateLabel" style="color:#dc3545;"></span>
                </p>
                <input type="text" name="reason" placeholder="Reason (optional, e.g. Personal leave)" maxlength="255"
                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:.85rem;margin-bottom:10px;font-family:inherit;">
                <div style="display:flex;gap:8px;">
                    <button type="submit" style="padding:8px 18px;background:#dc3545;color:#fff;border:none;border-radius:8px;font-size:.85rem;font-weight:600;cursor:pointer;font-family:inherit;">
                        <i class="fas fa-ban"></i> Block This Day
                    </button>
                    <button type="button" onclick="document.getElementById('bdBlockForm').style.display='none'"
                        style="padding:8px 18px;background:#e9ecef;color:#495057;border:none;border-radius:8px;font-size:.85rem;font-weight:600;cursor:pointer;font-family:inherit;">
                        Cancel
                    </button>
                </div>
            </form>
        </div>

        {{-- Legend --}}
        <div style="display:flex;gap:16px;margin-top:14px;font-size:.78rem;color:#6c757d;">
            <span><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#dc3545;vertical-align:middle;margin-right:4px;"></span> Blocked</span>
            <span><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#e9ecef;vertical-align:middle;margin-right:4px;"></span> Past</span>
            <span><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#fff;border:1px solid #d1d5db;vertical-align:middle;margin-right:4px;"></span> Available</span>
        </div>

        {{-- Upcoming blocked dates list --}}
        @if($blockedDates->count() > 0)
        <div style="margin-top:18px;border-top:1px solid #f0f2f5;padding-top:14px;">
            <h4 style="font-size:.85rem;font-weight:700;color:#1e2d4d;margin:0 0 10px;">Upcoming Blocked Dates</h4>
            @foreach($blockedDates as $bd)
            <div class="bd-blocked-item">
                <div>
                    <span style="font-weight:600;color:#1e2d4d;">{{ $bd->blocked_date->format('M j, Y (l)') }}</span>
                    @if($bd->reason)
                        <span style="color:#6c757d;font-size:.82rem;margin-left:8px;">— {{ $bd->reason }}</span>
                    @endif
                </div>
                <form method="POST" action="{{ route('lawyer.blocked-dates.destroy', $bd->id) }}" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bd-unblock-btn" onclick="return confirm('Unblock this date?')">
                        <i class="fas fa-times"></i> Unblock
                    </button>
                </form>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
(function() {
    const blockedDates = @json($blockedDates->pluck('blocked_date')->map(fn($d) => $d->format('Y-m-d')));
    let calYear, calMonth;

    function init() {
        const now = new Date();
        calYear = now.getFullYear();
        calMonth = now.getMonth();
        renderCal();
    }

    window.changeCalMonth = function(dir) {
        calMonth += dir;
        if (calMonth > 11) { calMonth = 0; calYear++; }
        if (calMonth < 0) { calMonth = 11; calYear--; }
        renderCal();
    };

    function renderCal() {
        const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        document.getElementById('bdCalMonth').textContent = months[calMonth] + ' ' + calYear;

        const grid = document.getElementById('bdCalDays');
        grid.innerHTML = '';

        const first = new Date(calYear, calMonth, 1);
        const last = new Date(calYear, calMonth + 1, 0);
        const today = new Date();
        today.setHours(0,0,0,0);

        // Pad leading empty cells
        for (let i = 0; i < first.getDay(); i++) {
            const empty = document.createElement('div');
            empty.className = 'bd-cal-cell empty';
            grid.appendChild(empty);
        }

        for (let d = 1; d <= last.getDate(); d++) {
            const cell = document.createElement('div');
            const dateObj = new Date(calYear, calMonth, d);
            const dateStr = calYear + '-' + String(calMonth+1).padStart(2,'0') + '-' + String(d).padStart(2,'0');
            const isPast = dateObj < today;
            const isBlocked = blockedDates.includes(dateStr);
            const isToday = dateObj.getTime() === today.getTime();

            cell.className = 'bd-cal-cell';
            if (isPast) cell.classList.add('past');
            if (isBlocked) cell.classList.add('blocked');
            if (isToday) cell.classList.add('today');
            cell.textContent = d;

            if (!isPast && !isBlocked) {
                cell.style.cursor = 'pointer';
                cell.onclick = function() {
                    document.getElementById('bdBlockDateInput').value = dateStr;
                    document.getElementById('bdBlockDateLabel').textContent = dateObj.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
                    document.getElementById('bdBlockForm').style.display = 'block';
                    document.getElementById('bdBlockForm').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                };
            }

            if (isBlocked) {
                cell.title = 'Blocked';
            }

            grid.appendChild(cell);
        }
    }

    init();
})();
</script>
@endpush
