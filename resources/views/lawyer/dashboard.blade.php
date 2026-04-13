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
            @if(($profile?->currentStatus() ?? 'offline') === 'active')
                🟢 Active
            @elseif(($profile?->currentStatus() ?? 'offline') === 'busy')
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

@if($errors->any())
    <div class="lp-alert-success" style="background:#fff5f5;color:#c92a2a;border-color:#ffc9c9;">
        <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
    </div>
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
        <p style="font-size:.85rem;color:#6c757d;margin:0 0 16px;">Click on a day to block the whole date or set a specific time range. Clients will only be able to book outside the blocked schedule.</p>

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
                <label style="display:flex;align-items:center;gap:8px;font-size:.84rem;color:#1e2d4d;font-weight:600;margin-bottom:10px;">
                    <input type="checkbox" name="is_all_day" id="bdIsAllDay" value="1" checked>
                    Block the whole day
                </label>
                <div id="bdTimeRangeFields" style="display:none;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
                    <div>
                        <label for="bdStartTime" style="display:block;font-size:.78rem;color:#6c757d;margin-bottom:4px;">Start time</label>
                        <input type="hidden" name="start_time" id="bdStartTime" value="{{ old('start_time') }}">
                        <div id="bdStartTimeChips" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(88px,1fr));gap:8px;max-height:208px;overflow-y:auto;padding:4px;"></div>
                    </div>
                    <div>
                        <label for="bdEndTime" style="display:block;font-size:.78rem;color:#6c757d;margin-bottom:4px;">End time</label>
                        <input type="hidden" name="end_time" id="bdEndTime" value="{{ old('end_time') }}">
                        <div id="bdEndTimeChips" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(88px,1fr));gap:8px;max-height:208px;overflow-y:auto;padding:4px;"></div>
                    </div>
                </div>
                <input type="text" name="reason" placeholder="Reason (optional, e.g. Personal leave)" maxlength="255"
                    value="{{ old('reason') }}"
                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:.85rem;margin-bottom:10px;font-family:inherit;">
                <div style="display:flex;gap:8px;">
                    <button type="submit" id="bdSubmitButton" style="padding:8px 18px;background:#dc3545;color:#fff;border:none;border-radius:8px;font-size:.85rem;font-weight:600;cursor:pointer;font-family:inherit;">
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
            <span><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#fff3bf;border:1px solid #f59f00;vertical-align:middle;margin-right:4px;"></span> Partial day</span>
            <span><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#e9ecef;vertical-align:middle;margin-right:4px;"></span> Past</span>
            <span><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#fff;border:1px solid #d1d5db;vertical-align:middle;margin-right:4px;"></span> Available</span>
        </div>

        {{-- Upcoming blocked dates list --}}
        @if($blockedDates->count() > 0)
        <div style="margin-top:18px;border-top:1px solid #f0f2f5;padding-top:14px;">
            <h4 style="font-size:.85rem;font-weight:700;color:#1e2d4d;margin:0 0 10px;">Upcoming Blocked Schedule</h4>
            @foreach($blockedDates as $bd)
            <div class="bd-blocked-item">
                <div>
                    <span style="font-weight:600;color:#1e2d4d;">{{ $bd->blocked_date->format('M j, Y (l)') }}</span>
                    <span style="display:inline-block;margin-left:8px;font-size:.8rem;color:#495057;background:#f1f3f5;border-radius:999px;padding:4px 10px;">{{ $bd->isAllDay() ? 'All day' : $bd->formattedTimeRange() }}</span>
                    @if($bd->reason)
                        <span style="color:#6c757d;font-size:.82rem;margin-left:8px;">— {{ $bd->reason }}</span>
                    @endif
                </div>
                <form method="POST" action="{{ route('lawyer.blocked-dates.destroy', $bd->id) }}" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bd-unblock-btn" onclick="return confirm('Remove this blocked schedule?')">
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
    const blockedSchedules = @json($blockedDates->map(fn($blockedDate) => $blockedDate->toScheduleArray())->values());
    const allDayBlockedDates = blockedSchedules
        .filter(function(blockedDate) { return blockedDate.is_all_day; })
        .map(function(blockedDate) { return blockedDate.date; });
    let calYear, calMonth;
    const timeOptions = [];

    for (let hour = 0; hour < 24; hour += 1) {
        ['00', '30'].forEach(function(minute) {
            const labelDate = new Date();
            labelDate.setHours(hour, Number(minute), 0, 0);
            timeOptions.push({
                value: String(hour).padStart(2, '0') + ':' + minute,
                label: labelDate.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' })
            });
        });
    }

    function groupedBlocks() {
        return blockedSchedules.reduce(function(carry, blockedDate) {
            if (!carry[blockedDate.date]) {
                carry[blockedDate.date] = [];
            }

            carry[blockedDate.date].push(blockedDate);
            return carry;
        }, {});
    }

    function formatTimeValue(date) {
        return String(date.getHours()).padStart(2, '0') + ':' + String(date.getMinutes()).padStart(2, '0');
    }

    function roundToNextHalfHour(date) {
        const rounded = new Date(date.getTime());
        rounded.setSeconds(0, 0);

        const minutes = rounded.getMinutes();
        if (minutes === 0 || minutes === 30) {
            return rounded;
        }

        if (minutes < 30) {
            rounded.setMinutes(30);
        } else {
            rounded.setHours(rounded.getHours() + 1);
            rounded.setMinutes(0);
        }

        return rounded;
    }

    function setDefaultTimeRange(dateStr) {
        const startSelect = document.getElementById('bdStartTime');
        const endSelect = document.getElementById('bdEndTime');

        if (startSelect.value && endSelect.value) {
            renderTimeChips();
            return;
        }

        const now = new Date();
        const selectedDate = new Date(dateStr + 'T00:00:00');
        const isToday = selectedDate.toDateString() === now.toDateString();

        let start = isToday ? roundToNextHalfHour(now) : new Date(dateStr + 'T09:00:00');
        let end = new Date(start.getTime());
        end.setMinutes(end.getMinutes() + 60);

        if (end.getDate() !== start.getDate()) {
            end = new Date(start.getTime());
            end.setHours(23, 30, 0, 0);
        }

        startSelect.value = formatTimeValue(start);
        endSelect.value = formatTimeValue(end);
        renderTimeChips();
    }

    function toMinutes(timeValue) {
        if (!timeValue) {
            return null;
        }

        const parts = timeValue.split(':');
        return (Number(parts[0]) * 60) + Number(parts[1]);
    }

    function renderChipGroup(containerId, activeValue, type) {
        const container = document.getElementById(containerId);
        const startValue = document.getElementById('bdStartTime').value;
        const endValue = document.getElementById('bdEndTime').value;
        const startMinutes = toMinutes(startValue);
        const endMinutes = toMinutes(endValue);

        container.innerHTML = '';

        timeOptions.forEach(function(option) {
            const button = document.createElement('button');
            button.type = 'button';
            button.textContent = option.label;
            button.style.padding = '9px 10px';
            button.style.borderRadius = '8px';
            button.style.border = '1px solid #d1d5db';
            button.style.background = '#fff';
            button.style.color = '#1e2d4d';
            button.style.fontSize = '.82rem';
            button.style.fontWeight = '600';
            button.style.cursor = 'pointer';
            button.style.transition = 'all .15s ease';

            const optionMinutes = toMinutes(option.value);
            const isDisabled = type === 'start'
                ? endMinutes !== null && optionMinutes >= endMinutes
                : startMinutes !== null && optionMinutes <= startMinutes;

            if (option.value === activeValue) {
                button.style.background = '#1e3a8a';
                button.style.borderColor = '#1e3a8a';
                button.style.color = '#fff';
            } else if (isDisabled) {
                button.style.background = '#f3f4f6';
                button.style.borderColor = '#e5e7eb';
                button.style.color = '#9ca3af';
                button.style.cursor = 'not-allowed';
            } else {
                button.addEventListener('mouseenter', function() {
                    button.style.borderColor = '#93c5fd';
                    button.style.background = '#eff6ff';
                });
                button.addEventListener('mouseleave', function() {
                    button.style.borderColor = '#d1d5db';
                    button.style.background = '#fff';
                });
            }

            button.disabled = isDisabled;
            button.addEventListener('click', function() {
                if (button.disabled) {
                    return;
                }

                document.getElementById(type === 'start' ? 'bdStartTime' : 'bdEndTime').value = option.value;

                if (type === 'start') {
                    const currentEnd = toMinutes(document.getElementById('bdEndTime').value);
                    if (currentEnd !== null && optionMinutes >= currentEnd) {
                        document.getElementById('bdEndTime').value = '';
                    }
                } else {
                    const currentStart = toMinutes(document.getElementById('bdStartTime').value);
                    if (currentStart !== null && optionMinutes <= currentStart) {
                        document.getElementById('bdStartTime').value = '';
                    }
                }

                renderTimeChips();
            });

            container.appendChild(button);
        });
    }

    function renderTimeChips() {
        renderChipGroup('bdStartTimeChips', document.getElementById('bdStartTime').value, 'start');
        renderChipGroup('bdEndTimeChips', document.getElementById('bdEndTime').value, 'end');
    }

    function updateBlockModeUi() {
        const isAllDay = document.getElementById('bdIsAllDay').checked;
        document.getElementById('bdTimeRangeFields').style.display = isAllDay ? 'none' : 'grid';
        document.getElementById('bdStartTime').disabled = isAllDay;
        document.getElementById('bdEndTime').disabled = isAllDay;
        document.getElementById('bdSubmitButton').innerHTML = isAllDay
            ? '<i class="fas fa-ban"></i> Block This Day'
            : '<i class="fas fa-clock"></i> Block Time';

        if (!isAllDay && document.getElementById('bdBlockDateInput').value) {
            setDefaultTimeRange(document.getElementById('bdBlockDateInput').value);
        } else if (!isAllDay) {
            renderTimeChips();
        }
    }

    function init() {
        const now = new Date();
        calYear = now.getFullYear();
        calMonth = now.getMonth();
        document.getElementById('bdIsAllDay').checked = {{ old('is_all_day') !== null ? (old('is_all_day') ? 'true' : 'false') : ((old('start_time') || old('end_time')) ? 'false' : 'true') }};
        renderTimeChips();
        updateBlockModeUi();
        document.getElementById('bdIsAllDay').addEventListener('change', updateBlockModeUi);
        renderCal();

        @if($errors->any())
            document.getElementById('bdBlockDateInput').value = @json(old('blocked_date'));
            document.getElementById('bdBlockDateLabel').textContent = @json(old('blocked_date') ? \Carbon\Carbon::parse(old('blocked_date'))->format('l, F j, Y') : '');
            document.getElementById('bdBlockForm').style.display = 'block';
        @endif
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
        const blocksByDate = groupedBlocks();

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
            const dayBlocks = blocksByDate[dateStr] || [];
            const isBlocked = allDayBlockedDates.includes(dateStr);
            const hasPartialBlock = dayBlocks.length > 0 && !isBlocked;
            const isToday = dateObj.getTime() === today.getTime();

            cell.className = 'bd-cal-cell';
            if (isPast) cell.classList.add('past');
            if (isBlocked) cell.classList.add('blocked');
            if (hasPartialBlock) {
                cell.style.background = '#fff3bf';
                cell.style.borderColor = '#f59f00';
                cell.style.color = '#8a5a00';
            }
            if (isToday) cell.classList.add('today');
            cell.textContent = d;

            if (!isPast && !isBlocked) {
                cell.style.cursor = 'pointer';
                cell.onclick = function() {
                    document.getElementById('bdBlockDateInput').value = dateStr;
                    document.getElementById('bdBlockDateLabel').textContent = dateObj.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
                    document.getElementById('bdStartTime').value = '';
                    document.getElementById('bdEndTime').value = '';
                    document.getElementById('bdBlockForm').style.display = 'block';
                    if (!document.getElementById('bdIsAllDay').checked) {
                        setDefaultTimeRange(dateStr);
                    }
                    document.getElementById('bdBlockForm').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                };
            }

            if (isBlocked) {
                cell.title = 'Blocked for the whole day';
            } else if (hasPartialBlock) {
                cell.title = dayBlocks.map(function(blockedDate) {
                    return blockedDate.label + (blockedDate.reason ? ' - ' + blockedDate.reason : '');
                }).join('\n');
            }

            grid.appendChild(cell);
        }
    }

    init();
})();
</script>
@endpush
