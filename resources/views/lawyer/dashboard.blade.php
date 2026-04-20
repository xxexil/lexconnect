@extends('layouts.lawyer')
@section('title', 'Dashboard')
@section('content')

<div class="lp-page-header">
    <div>
        <h1 class="lp-page-title">Dashboard</h1>
        <p class="lp-page-sub">Welcome back, {{ Auth::user()->name }}</p>
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

{{-- ── STAT CARDS ── --}}
<div class="lp-stats-grid" style="grid-template-columns:repeat(5,1fr);">
    <div class="lp-stat-card">
        <div class="lp-stat-icon upcoming-icon"><i class="fas fa-calendar-check"></i></div>
        <div>
            <div class="lp-stat-num">{{ $upcomingCount }}</div>
            <div class="lp-stat-lbl">Upcoming Sessions</div>
        </div>
    </div>
    <div class="lp-stat-card" style="border-left:3px solid #f59e0b;">
        <div class="lp-stat-icon" style="background:rgba(245,158,11,.1);color:#f59e0b;"><i class="fas fa-hourglass-half"></i></div>
        <div>
            <div class="lp-stat-num">{{ $pendingCount }}</div>
            <div class="lp-stat-lbl">Pending Requests</div>
        </div>
    </div>
    <div class="lp-stat-card">
        <div class="lp-stat-icon clients-icon"><i class="fas fa-user-friends"></i></div>
        <div>
            <div class="lp-stat-num">{{ $totalClients }}</div>
            <div class="lp-stat-lbl">Total Clients</div>
        </div>
    </div>
    <div class="lp-stat-card" style="border-left:3px solid #3b82f6;">
        <div class="lp-stat-icon" style="background:rgba(59,130,246,.1);color:#3b82f6;"><i class="fas fa-envelope"></i></div>
        <div>
            <div class="lp-stat-num">{{ $unreadMessages }}</div>
            <div class="lp-stat-lbl">Unread Messages</div>
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

{{-- ── TODAY'S SCHEDULE + RECENT ACTIVITY ── --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:22px;align-items:stretch;">

{{-- LEFT: Today's Schedule --}}
<div class="lp-card" style="border-top:4px solid #1e2d4d;margin-bottom:0;">
    <div class="lp-card-header" style="background:linear-gradient(135deg,#1e2d4d,#2a3f6f);border-radius:0;padding:18px 22px;margin:-1px -1px 0;">
        <div style="display:flex;align-items:center;gap:12px;">
            <div style="width:40px;height:40px;background:rgba(255,255,255,.15);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fas fa-calendar-day" style="color:#fff;font-size:1.1rem;"></i>
            </div>
            <div>
                <h2 style="color:#fff;font-size:1rem;font-weight:700;margin:0;line-height:1.2;">Today's Schedule</h2>
                <div style="color:rgba(255,255,255,.65);font-size:.78rem;margin-top:2px;">{{ now()->format('l, F j, Y') }}</div>
            </div>
            @if($todayConsultations->count() > 0)
            <span style="background:#b5860d;color:#fff;font-size:.75rem;font-weight:700;padding:3px 10px;border-radius:20px;margin-left:4px;">
                {{ $todayConsultations->count() }} session{{ $todayConsultations->count() > 1 ? 's' : '' }}
            </span>
            @endif
        </div>
        <a href="{{ route('lawyer.consultations') }}#upcoming" style="font-size:.82rem;color:rgba(255,255,255,.85);font-weight:600;text-decoration:none;padding:7px 14px;border:1.5px solid rgba(255,255,255,.35);border-radius:8px;white-space:nowrap;transition:all .15s;display:inline-flex;align-items:center;gap:6px;"
           onmouseover="this.style.background='rgba(255,255,255,.15)'" onmouseout="this.style.background='transparent'">
            <i class="fas fa-list"></i> View All
        </a>
    </div>

    @forelse($todayConsultations as $loop_index => $c)
    @php $sched = \Carbon\Carbon::parse($c->scheduled_at); @endphp
    <div style="border-left:3px solid {{ $c->type === 'video' ? '#3b82f6' : '#16a34a' }};margin:12px 16px;border-radius:10px;background:#f8fafc;padding:14px 16px;box-shadow:0 1px 4px rgba(0,0,0,.05);{{ $loop_index >= 3 ? 'display:none;' : '' }}" data-sched-item>
        {{-- Top row: avatar + name + time + status --}}
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px;">
            <div style="width:42px;height:42px;border-radius:50%;background:#1e2d4d;color:#fff;display:flex;align-items:center;justify-content:center;font-size:.95rem;font-weight:700;flex-shrink:0;">
                {{ strtoupper(substr($c->client->name,0,2)) }}
            </div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:.95rem;font-weight:700;color:#1e2d4d;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $c->client->name }}</div>
                <div style="font-size:.75rem;color:#6c757d;">{{ $c->client->email ?? '' }}</div>
            </div>
            <span style="font-size:.75rem;font-weight:700;color:{{ $c->type === 'video' ? '#1d4ed8' : '#15803d' }};background:{{ $c->type === 'video' ? '#eff6ff' : '#f0fdf4' }};padding:3px 10px;border-radius:20px;white-space:nowrap;">
                ● Upcoming
            </span>
        </div>
        {{-- Info row --}}
        <div style="display:flex;flex-wrap:wrap;gap:10px;font-size:.82rem;color:#6c757d;margin-bottom:10px;">
            <span><i class="fas fa-calendar-alt" style="margin-right:3px;color:#1e2d4d;"></i>{{ $sched->format('m/d/Y') }}</span>
            <span><i class="fas fa-clock" style="margin-right:3px;color:#1e2d4d;"></i>{{ $sched->format('g:i A') }} · {{ $c->duration_label }}</span>
            @if($c->type === 'video')
                <span><i class="fas fa-video" style="margin-right:3px;color:#3b82f6;"></i>Video</span>
            @else
                <span><i class="fas fa-handshake" style="margin-right:3px;color:#16a34a;"></i>In-Person</span>
            @endif
            <span style="font-weight:700;color:#1e2d4d;">₱{{ number_format($c->price, 0) }}</span>
        </div>
        {{-- Document --}}
        @if($c->case_document)
        <div style="margin-bottom:10px;display:flex;gap:6px;flex-wrap:wrap;">
            <a href="{{ asset('storage/' . $c->case_document) }}" target="_blank" rel="noopener"
               style="display:inline-flex;align-items:center;gap:6px;font-size:.78rem;font-weight:600;color:#2563eb;background:#eff6ff;padding:5px 12px;border-radius:7px;text-decoration:none;border:1px solid #bfdbfe;">
                <i class="fas fa-paperclip"></i> View
            </a>
            <a href="{{ asset('storage/' . $c->case_document) }}" download
               style="display:inline-flex;align-items:center;gap:6px;font-size:.78rem;font-weight:600;color:#16a34a;background:#f0fdf4;padding:5px 12px;border-radius:7px;text-decoration:none;border:1px solid #bbf7d0;">
                <i class="fas fa-download"></i> Download
            </a>
        </div>
        @endif
        {{-- Actions --}}
        <div style="display:flex;gap:8px;">
            @if($c->type === 'video')
            @php
                $joinOpensAt = $c->videoJoinOpensAt();
                $canJoin     = $c->canJoinVideoCall();
            @endphp
            @if($canJoin)
            <a href="{{ route('consultations.video', $c) }}"
               style="flex:1;text-align:center;padding:8px 12px;background:#1e2d4d;color:#fff;border-radius:8px;font-size:.82rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:6px;">
                <i class="fas fa-video"></i> Join Video Call
            </a>
            @else
            <span class="js-video-join-waiting"
                  data-join-opens-at="{{ $joinOpensAt->timestamp * 1000 }}"
                  data-join-url="{{ route('consultations.video', $c) }}"
                  style="flex:1;text-align:center;padding:8px 12px;background:#e9ecef;color:#6c757d;border-radius:8px;font-size:.82rem;font-weight:600;display:inline-flex;align-items:center;justify-content:center;gap:6px;cursor:not-allowed;"
                  title="Available at {{ $joinOpensAt->format('g:i A') }}">
                <i class="fas fa-clock"></i> Available {{ $joinOpensAt->format('g:i A') }}
            </span>
            @endif
            @endif
        </div>
    </div>
    @empty
    <div style="padding:32px 20px;text-align:center;">
        <i class="fas fa-sun" style="font-size:2rem;margin-bottom:10px;display:block;color:#f59e0b;opacity:.6;"></i>
        <div style="font-size:.9rem;font-weight:600;color:#6c757d;">No sessions scheduled for today</div>
        <div style="font-size:.8rem;color:#adb5bd;margin-top:4px;">Enjoy your free day!</div>
    </div>

    {{-- Show up to 5 upcoming sessions when no sessions today --}}
    @if($upcomingSessions->count() > 0)
    <div style="padding:0 16px 16px;">
        <div style="font-size:.75rem;font-weight:700;color:#3b82f6;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;margin-top:200px;text-align:center;">
            <i class="fas fa-calendar-check" style="margin-right:4px;"></i>Upcoming Sessions
        </div>
        @foreach($upcomingSessions as $us)
        @php
            $usSched = \Carbon\Carbon::parse($us->scheduled_at);
            $usJoinOpensAt = $us->videoJoinOpensAt();
            $usCanJoin = $us->canJoinVideoCall();
        @endphp
        <div style="display:flex;align-items:center;gap:12px;padding:10px 14px;background:#f8fafc;border-radius:10px;margin-bottom:8px;border-left:3px solid {{ $us->type === 'video' ? '#3b82f6' : '#16a34a' }};">
            <div style="width:36px;height:36px;border-radius:50%;background:#1e2d4d;color:#fff;display:flex;align-items:center;justify-content:center;font-size:.82rem;font-weight:700;flex-shrink:0;overflow:hidden;">
                @if($us->client->avatar_url)
                    <img src="{{ $us->client->avatar_url }}" style="width:100%;height:100%;object-fit:cover;" alt="{{ $us->client->name }}">
                @else
                    {{ strtoupper(substr($us->client->name,0,1)) }}
                @endif
            </div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:.88rem;font-weight:700;color:#1e2d4d;">{{ $us->client->name }}</div>
                <div style="font-size:.75rem;color:#6c757d;">
                    {{ $usSched->format('M j, Y') }} · {{ $usSched->format('g:i A') }} · {{ $us->duration_label }}
                    @if($usSched->isToday())<span style="color:#dc2626;font-weight:600;margin-left:4px;">Today</span>@endif
                </div>
            </div>
            @if($us->type === 'video')
                @if($usCanJoin)
                <a href="{{ route('consultations.video', $us) }}" style="font-size:.75rem;font-weight:600;color:#fff;background:#1e2d4d;padding:4px 10px;border-radius:6px;text-decoration:none;white-space:nowrap;">
                    <i class="fas fa-video"></i> Join
                </a>
                @else
                <span class="js-video-join-waiting"
                      data-join-opens-at="{{ $usJoinOpensAt->timestamp * 1000 }}"
                      data-join-url="{{ route('consultations.video', $us) }}"
                      style="font-size:.72rem;color:#9ca3af;white-space:nowrap;cursor:not-allowed;"
                      title="Available at {{ $usJoinOpensAt->format('g:i A') }}">
                    <i class="fas fa-clock"></i> {{ $usJoinOpensAt->format('g:i A') }}
                </span>
                @endif
            @endif
        </div>
        @endforeach
    </div>
    @endif
    @endforelse

    @if($todayConsultations->count() > 3)
    <div style="text-align:center;padding:8px 16px 14px;">
        <button id="schedShowMore" onclick="toggleSchedItems()" style="background:none;border:1.5px solid #d1d5db;border-radius:8px;padding:7px 20px;font-size:.82rem;font-weight:600;color:#1e2d4d;cursor:pointer;font-family:inherit;"
            onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='none'">
            <i class="fas fa-chevron-down" id="schedChevron" style="margin-right:5px;"></i>
            Show {{ $todayConsultations->count() - 3 }} more session{{ $todayConsultations->count() - 3 > 1 ? 's' : '' }}
        </button>
    </div>
    @endif

    {{-- Next Upcoming Session (only when there ARE sessions today) --}}
    @if($todayConsultations->count() > 0 && $nextSession && !$todayConsultations->contains('id', $nextSession->id))
    <div style="margin:12px 16px 14px;padding:12px 16px;background:linear-gradient(135deg,#eef2fc,#f0f7ff);border-radius:10px;border-left:3px solid #3b82f6;display:flex;align-items:center;gap:14px;">
        <i class="fas fa-clock" style="color:#3b82f6;font-size:1.1rem;flex-shrink:0;"></i>
        <div style="flex:1;">
            <div style="font-size:.75rem;font-weight:700;color:#3b82f6;text-transform:uppercase;letter-spacing:.5px;margin-bottom:2px;">Next Upcoming Session</div>
            <div style="font-size:.88rem;font-weight:600;color:#1e2d4d;">
                {{ $nextSession->client->name }} ·
                <span style="font-weight:400;color:#6c757d;">{{ \Carbon\Carbon::parse($nextSession->scheduled_at)->format('M j, Y · g:i A') }}</span>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- RIGHT: Recent Activity --}}
<div class="lp-card" style="margin-bottom:0;">
    <div class="lp-card-header">
        <h2 class="lp-card-title"><i class="fas fa-history"></i> Recent Activity</h2>
        <a href="{{ route('lawyer.consultations') }}#completed" style="font-size:.78rem;color:#2563eb;font-weight:600;text-decoration:none;">View All →</a>
    </div>
    <div style="padding:8px 0 4px;">
    @forelse($recentConsultations->take(10) as $c)
    @php
        $iconMap   = ['completed'=>['fas fa-check-circle','#16a34a','#f0fdf4'],'upcoming'=>['fas fa-calendar-check','#3b82f6','#eff6ff'],'cancelled'=>['fas fa-times-circle','#dc2626','#fff5f5'],'expired'=>['fas fa-clock','#6c757d','#f8f9fa'],'pending'=>['fas fa-hourglass-half','#f59e0b','#fffbeb']];
        $icon = $iconMap[$c->status] ?? ['fas fa-circle','#6c757d','#f8f9fa'];
    @endphp
    <div style="display:flex;align-items:flex-start;gap:12px;padding:12px 20px;border-bottom:1px solid #f0f2f5;">
        <div style="width:36px;height:36px;border-radius:10px;background:{{ $icon[2] }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="{{ $icon[0] }}" style="color:{{ $icon[1] }};font-size:.9rem;"></i>
        </div>
        <div style="flex:1;min-width:0;">
            <div style="font-size:.88rem;font-weight:700;color:#1e2d4d;margin-bottom:2px;">
                @if($c->status === 'completed') Consultation Completed
                @elseif($c->status === 'upcoming') Session Confirmed
                @elseif($c->status === 'cancelled') Consultation Cancelled
                @elseif($c->status === 'expired') Session Expired
                @else Pending Consultation
                @endif
            </div>
            <div style="font-size:.8rem;color:#6c757d;line-height:1.5;">
                {{ ucfirst($c->type) }} consultation with <strong style="color:#374151;">{{ $c->client->name }}</strong>
                @if($c->status === 'upcoming') is on {{ \Carbon\Carbon::parse($c->scheduled_at)->format('M j') }} at {{ \Carbon\Carbon::parse($c->scheduled_at)->format('g:i A') }}.
                @else on {{ \Carbon\Carbon::parse($c->scheduled_at)->format('M j, Y') }}.
                @endif
            </div>
            <div style="font-size:.73rem;color:#adb5bd;margin-top:3px;">{{ $c->updated_at->diffForHumans() }}</div>
        </div>
    </div>
    @empty
    <div style="padding:32px 20px;text-align:center;">
        <i class="fas fa-history" style="font-size:2rem;color:#d1d5db;margin-bottom:10px;display:block;"></i>
        <div style="font-size:.9rem;color:#6c757d;">No recent activity yet</div>
    </div>
    @endforelse
    </div>
</div>

</div>{{-- end two-col grid --}}

{{-- ── AVAILABILITY CALENDAR ── --}}
<div class="lp-card" style="margin-top:22px;">
    <div class="lp-card-header">
        <h2 class="lp-card-title"><i class="fas fa-calendar-alt"></i> Availability Calendar</h2>
    </div>
    <div style="padding:22px;">
        <p style="font-size:.85rem;color:#6c757d;margin:0 0 16px;">Click on a day to block the whole date or set a specific time range. Clients will only be able to book outside the blocked schedule.</p>

        <div class="bd-cal-nav">
            <button type="button" class="bd-cal-nav-btn" onclick="changeCalMonth(-1)"><i class="fas fa-chevron-left"></i></button>
            <span class="bd-cal-month" id="bdCalMonth"></span>
            <button type="button" class="bd-cal-nav-btn" onclick="changeCalMonth(1)"><i class="fas fa-chevron-right"></i></button>
        </div>

        <div class="bd-cal-grid">
            <div class="bd-cal-day-head">Sun</div><div class="bd-cal-day-head">Mon</div>
            <div class="bd-cal-day-head">Tue</div><div class="bd-cal-day-head">Wed</div>
            <div class="bd-cal-day-head">Thu</div><div class="bd-cal-day-head">Fri</div>
            <div class="bd-cal-day-head">Sat</div>
        </div>
        <div class="bd-cal-grid" id="bdCalDays"></div>

        {{-- Block date form --}}
        <div id="bdBlockForm" style="display:none;margin-top:16px;padding:16px;background:#f8f9fa;border-radius:10px;">
            <form method="POST" action="{{ route('lawyer.blocked-dates.store') }}">
                @csrf
                <input type="hidden" name="blocked_date" id="bdBlockDateInput">
                <p style="font-size:.9rem;font-weight:600;color:#1e2d4d;margin:0 0 8px;">
                    Block <span id="bdBlockDateLabel" style="color:#dc3545;"></span>
                </p>
                <label style="display:flex;align-items:center;gap:8px;font-size:.84rem;color:#1e2d4d;font-weight:600;margin-bottom:10px;">
                    <input type="checkbox" name="is_all_day" id="bdIsAllDay" value="1" checked> Block the whole day
                </label>
                <div id="bdTimeRangeFields" style="display:none;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
                    <div>
                        <label style="display:block;font-size:.78rem;color:#6c757d;margin-bottom:4px;">Start time</label>
                        <input type="hidden" name="start_time" id="bdStartTime" value="{{ old('start_time') }}">
                        <div id="bdStartTimeChips" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(88px,1fr));gap:8px;max-height:208px;overflow-y:auto;padding:4px;"></div>
                    </div>
                    <div>
                        <label style="display:block;font-size:.78rem;color:#6c757d;margin-bottom:4px;">End time</label>
                        <input type="hidden" name="end_time" id="bdEndTime" value="{{ old('end_time') }}">
                        <div id="bdEndTimeChips" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(88px,1fr));gap:8px;max-height:208px;overflow-y:auto;padding:4px;"></div>
                    </div>
                </div>
                <input type="text" name="reason" placeholder="Reason (optional)" maxlength="255" value="{{ old('reason') }}"
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
        <div style="display:flex;gap:16px;flex-wrap:wrap;margin-top:14px;font-size:.78rem;color:#6c757d;">
            <span><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#dc3545;vertical-align:middle;margin-right:4px;"></span> Blocked</span>
            <span><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#dbeafe;border:1px solid #3b82f6;vertical-align:middle;margin-right:4px;"></span> Booked</span>
            <span><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#fff3bf;border:1px solid #f59f00;vertical-align:middle;margin-right:4px;"></span> Partial block</span>
            <span><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#e9ecef;vertical-align:middle;margin-right:4px;"></span> Past</span>
            <span><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#fff;border:1px solid #d1d5db;vertical-align:middle;margin-right:4px;"></span> Available</span>
        </div>

        @if($blockedDates->count() > 0)
        <div style="margin-top:18px;border-top:1px solid #f0f2f5;padding-top:14px;">
            <h4 style="font-size:.85rem;font-weight:700;color:#1e2d4d;margin:0 0 10px;">Upcoming Blocked Schedule</h4>
            @foreach($blockedDates as $bd)
            <div class="bd-blocked-item">
                <div>
                    <span style="font-weight:600;color:#1e2d4d;">{{ $bd->blocked_date->format('M j, Y (l)') }}</span>
                    <span style="display:inline-block;margin-left:8px;font-size:.8rem;color:#495057;background:#f1f3f5;border-radius:999px;padding:4px 10px;">{{ $bd->isAllDay() ? 'All day' : $bd->formattedTimeRange() }}</span>
                    @if($bd->reason)<span style="color:#6c757d;font-size:.82rem;margin-left:8px;">— {{ $bd->reason }}</span>@endif
                </div>
                <form method="POST" action="{{ route('lawyer.blocked-dates.destroy', $bd->id) }}" style="display:inline;">
                    @csrf @method('DELETE')
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

{{-- ── CHARTS ROW ── --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:22px;margin-top:22px;">

    {{-- Monthly Earnings Chart --}}
    <div class="lp-card">
        <div class="lp-card-header">
            <h2 class="lp-card-title"><i class="fas fa-chart-line"></i> Monthly Earnings</h2>
            <select id="earningsYearSelect" onchange="switchEarningsYear(this.value)"
                style="height:32px;padding:0 10px;border:1px solid #d9e2ef;border-radius:8px;font-size:.8rem;color:#1e2d4d;background:#fff;cursor:pointer;">
                <option value="rolling" selected>Last 12 months</option>
                @for($y = now()->year; $y >= now()->year - 4; $y--)
                <option value="{{ $y }}">{{ $y }}</option>
                @endfor
            </select>
        </div>
        @php $hasEarnings = $monthlyEarnings->sum('total') > 0; @endphp
        @if(!$hasEarnings)
        <div style="padding:40px 24px;text-align:center;color:#adb5bd;">
            <i class="fas fa-chart-line" style="font-size:2.5rem;margin-bottom:12px;display:block;opacity:.3;"></i>
            <p style="font-size:.9rem;font-weight:600;color:#6c757d;margin:0 0 4px;">No earnings data yet.</p>
            <p style="font-size:.82rem;margin:0;">Start accepting consultations to generate income.</p>
        </div>
        @else
        <div style="padding:12px 20px 6px;display:flex;gap:20px;flex-wrap:wrap;" id="earningsInsights">
            <div style="font-size:.82rem;color:#6c757d;">
                <i class="fas fa-trophy" style="color:#f59e0b;margin-right:4px;"></i>
                <strong>Highest:</strong> <span id="insightHighest">{{ $highestMonth['month'] }} — ₱{{ number_format($highestMonth['total'], 0) }}</span>
            </div>
            <div style="font-size:.82rem;color:#6c757d;">
                <i class="fas fa-calendar" style="color:#3b82f6;margin-right:4px;"></i>
                <strong><span id="insightYearLabel">This year</span>:</strong> <span id="insightTotal">₱{{ number_format($totalThisYear, 0) }}</span>
            </div>
        </div>
        <div style="padding:8px 24px 24px;">
            <div style="position:relative;height:240px;">
                <canvas id="earningsChart"></canvas>
            </div>
        </div>
        @endif
    </div>

    {{-- Monthly Consultations Chart --}}
    <div class="lp-card">
        <div class="lp-card-header">
            <h2 class="lp-card-title"><i class="fas fa-calendar-check"></i> Consultations per Month</h2>
            <select id="consultYearSelect" onchange="switchConsultYear(this.value)"
                style="height:32px;padding:0 10px;border:1px solid #d9e2ef;border-radius:8px;font-size:.8rem;color:#1e2d4d;background:#fff;cursor:pointer;">
                <option value="rolling" selected>Last 12 months</option>
                @for($y = now()->year; $y >= now()->year - 4; $y--)
                <option value="{{ $y }}">{{ $y }}</option>
                @endfor
            </select>
        </div>
        @php $hasConsultations = $monthlyConsultations->sum('count') > 0; @endphp
        @if(!$hasConsultations)
        <div style="padding:40px 24px;text-align:center;color:#adb5bd;">
            <i class="fas fa-calendar-times" style="font-size:2.5rem;margin-bottom:12px;display:block;opacity:.3;"></i>
            <p style="font-size:.9rem;font-weight:600;color:#6c757d;margin:0 0 4px;">No completed consultations yet.</p>
            <p style="font-size:.82rem;margin:0;">Complete your first session to see data here.</p>
        </div>
        @else
        <div style="padding:20px 24px 24px;">
            <div style="position:relative;height:240px;">
                <canvas id="consultationsChart"></canvas>
            </div>
        </div>
        @endif
    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ── Today's Schedule show/hide extra items ──
var schedExpanded = false;
function toggleSchedItems() {
    schedExpanded = !schedExpanded;
    document.querySelectorAll('[data-sched-item]').forEach(function(el, i) {
        if (i >= 3) el.style.display = schedExpanded ? 'flex' : 'none';
    });
    var btn = document.getElementById('schedShowMore');
    var chev = document.getElementById('schedChevron');
    if (schedExpanded) {
        btn.innerHTML = '<i class="fas fa-chevron-up" id="schedChevron" style="margin-right:5px;"></i> Show less';
    } else {
        var hidden = document.querySelectorAll('[data-sched-item]').length - 3;
        btn.innerHTML = '<i class="fas fa-chevron-down" id="schedChevron" style="margin-right:5px;"></i> Show ' + hidden + ' more session' + (hidden > 1 ? 's' : '');
    }
}

// ── Auto-activate video call buttons when time arrives ──
function refreshDashJoinButtons() {
    var now = Date.now();
    document.querySelectorAll('.js-video-join-waiting').forEach(function(btn) {
        var opensAt = parseInt(btn.dataset.joinOpensAt || '0', 10);
        var joinUrl = btn.dataset.joinUrl;
        if (!opensAt || !joinUrl || now < opensAt) return;
        var link = document.createElement('a');
        link.href = joinUrl;
        link.style.cssText = btn.style.cssText;
        link.style.background = '#1e2d4d';
        link.style.color = '#fff';
        link.style.cursor = 'pointer';
        link.style.textDecoration = 'none';
        link.style.flex = '1';
        link.innerHTML = '<i class="fas fa-video" style="margin-right:6px;"></i> Join Video Call';
        btn.replaceWith(link);
    });
}
refreshDashJoinButtons();
setInterval(refreshDashJoinButtons, 1000);
// ── Earnings Chart ──
@if($monthlyEarnings->sum('total') > 0)
(function () {
    const rollingLabels = @json($monthlyEarnings->pluck('month'));
    const rollingData   = @json($monthlyEarnings->pluck('total'));

    const yearlyData = @json($yearlyEarnings);

    const ctx = document.getElementById('earningsChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: { labels: rollingLabels, datasets: [{
            label: 'Monthly Earnings (₱)', data: rollingData,
            fill: true, backgroundColor: 'rgba(30,45,77,0.08)',
            borderColor: '#1e2d4d', borderWidth: 2.5,
            pointBackgroundColor: '#b5860d', pointBorderColor: '#fff',
            pointBorderWidth: 2, pointRadius: 5, pointHoverRadius: 7, tension: 0.4,
        }]},
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => ' ₱' + Number(c.parsed.y).toLocaleString() }}},
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#6c757d' }},
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { font: { size: 11 }, color: '#6c757d', callback: v => '₱' + Number(v).toLocaleString() }}
            }
        }
    });

    window.switchEarningsYear = function(val) {
        if (val === 'rolling') {
            chart.data.labels = rollingLabels;
            chart.data.datasets[0].data = rollingData;
            const maxVal = Math.max(...rollingData);
            const maxIdx = rollingData.indexOf(maxVal);
            document.getElementById('insightHighest').textContent = rollingLabels[maxIdx] + ' — ₱' + Number(maxVal).toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2});
            document.getElementById('insightYearLabel').textContent = 'This year';
            document.getElementById('insightTotal').textContent = '₱' + Number(rollingData.reduce((a,b) => a+b, 0)).toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2});
        } else {
            const yd = yearlyData[val];
            const data = yd.map(m => parseFloat(m.total) || 0);
            chart.data.labels = yd.map(m => m.month);
            chart.data.datasets[0].data = data;
            const maxVal = Math.max(...data);
            const maxIdx = data.indexOf(maxVal);
            document.getElementById('insightHighest').textContent = yd[maxIdx].month + ' ' + val + ' — ₱' + Number(maxVal).toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2});
            document.getElementById('insightYearLabel').textContent = val;
            document.getElementById('insightTotal').textContent = '₱' + Number(data.reduce((a,b) => a+b, 0)).toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2});
        }
        chart.update();
    };
})();
@endif

// ── Consultations Chart ──
@if($monthlyConsultations->sum('count') > 0)
(function () {
    const rollingLabels = @json($monthlyConsultations->pluck('month'));
    const rollingData   = @json($monthlyConsultations->pluck('count'));
    const yearlyData    = @json($yearlyConsultations);

    const ctx = document.getElementById('consultationsChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: { labels: rollingLabels, datasets: [{
            label: 'Completed Consultations', data: rollingData,
            fill: true, backgroundColor: 'rgba(59,130,246,0.08)',
            borderColor: '#3b82f6', borderWidth: 2.5,
            pointBackgroundColor: '#1e2d4d', pointBorderColor: '#fff',
            pointBorderWidth: 2, pointRadius: 5, pointHoverRadius: 7, tension: 0.4,
        }]},
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => ' ' + c.parsed.y + ' session' + (c.parsed.y !== 1 ? 's' : '') }}},
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#6c757d' }},
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { font: { size: 11 }, color: '#6c757d', stepSize: 1 }}
            }
        }
    });

    window.switchConsultYear = function(val) {
        if (val === 'rolling') {
            chart.data.labels = rollingLabels;
            chart.data.datasets[0].data = rollingData;
        } else {
            const yd = yearlyData[val];
            chart.data.labels = yd.map(m => m.month);
            chart.data.datasets[0].data = yd.map(m => m.count);
        }
        chart.update();
    };
})();
@endif

// ── Calendar ──
(function() {
    const blockedSchedules = @json($blockedDates->map(fn($b) => $b->toScheduleArray())->values());
    const bookedSlots      = @json($bookedSlots);
    const allDayBlocked    = blockedSchedules.filter(b => b.is_all_day).map(b => b.date);
    let calYear, calMonth;
    const timeOptions = [];

    for (let h = 0; h < 24; h++) {
        ['00','30'].forEach(m => {
            const d = new Date(); d.setHours(h, +m, 0, 0);
            timeOptions.push({ value: String(h).padStart(2,'0')+':'+m, label: d.toLocaleTimeString('en-US',{hour:'numeric',minute:'2-digit'}) });
        });
    }

    function groupedBlocks() {
        return blockedSchedules.reduce((c,b) => { if(!c[b.date]) c[b.date]=[]; c[b.date].push(b); return c; }, {});
    }
    function fmtTime(d) { return String(d.getHours()).padStart(2,'0')+':'+String(d.getMinutes()).padStart(2,'0'); }
    function roundHalf(d) {
        const r=new Date(d); r.setSeconds(0,0);
        const m=r.getMinutes();
        if(m===0||m===30) return r;
        if(m<30) r.setMinutes(30); else { r.setHours(r.getHours()+1); r.setMinutes(0); }
        return r;
    }
    function setDefaultRange(ds) {
        const s=document.getElementById('bdStartTime'), e=document.getElementById('bdEndTime');
        if(s.value&&e.value){renderChips();return;}
        const now=new Date(), sel=new Date(ds+'T00:00:00'), isToday=sel.toDateString()===now.toDateString();
        let st=isToday?roundHalf(now):new Date(ds+'T09:00:00'), en=new Date(st); en.setMinutes(en.getMinutes()+60);
        if(en.getDate()!==st.getDate()){en=new Date(st);en.setHours(23,30,0,0);}
        s.value=fmtTime(st); e.value=fmtTime(en); renderChips();
    }
    function toMin(v){if(!v)return null;const p=v.split(':');return +p[0]*60+ +p[1];}
    function renderChipGroup(cid, active, type) {
        const cont=document.getElementById(cid), sv=document.getElementById('bdStartTime').value, ev=document.getElementById('bdEndTime').value;
        const sm=toMin(sv), em=toMin(ev); cont.innerHTML='';
        timeOptions.forEach(opt => {
            const btn=document.createElement('button'); btn.type='button'; btn.textContent=opt.label;
            Object.assign(btn.style,{padding:'9px 10px',borderRadius:'8px',border:'1px solid #d1d5db',background:'#fff',color:'#1e2d4d',fontSize:'.82rem',fontWeight:'600',cursor:'pointer',transition:'all .15s'});
            const om=toMin(opt.value), dis=type==='start'?(em!==null&&om>=em):(sm!==null&&om<=sm);
            if(opt.value===active){btn.style.background='#1e3a8a';btn.style.borderColor='#1e3a8a';btn.style.color='#fff';}
            else if(dis){Object.assign(btn.style,{background:'#f3f4f6',borderColor:'#e5e7eb',color:'#9ca3af',cursor:'not-allowed'});}
            else{btn.addEventListener('mouseenter',()=>{btn.style.borderColor='#93c5fd';btn.style.background='#eff6ff';});btn.addEventListener('mouseleave',()=>{btn.style.borderColor='#d1d5db';btn.style.background='#fff';});}
            btn.disabled=dis;
            btn.addEventListener('click',()=>{
                if(btn.disabled)return;
                document.getElementById(type==='start'?'bdStartTime':'bdEndTime').value=opt.value;
                if(type==='start'){const ce=toMin(document.getElementById('bdEndTime').value);if(ce!==null&&om>=ce)document.getElementById('bdEndTime').value='';}
                else{const cs=toMin(document.getElementById('bdStartTime').value);if(cs!==null&&om<=cs)document.getElementById('bdStartTime').value='';}
                renderChips();
            });
            cont.appendChild(btn);
        });
    }
    function renderChips(){renderChipGroup('bdStartTimeChips',document.getElementById('bdStartTime').value,'start');renderChipGroup('bdEndTimeChips',document.getElementById('bdEndTime').value,'end');}
    function updateBlockUI(){
        const all=document.getElementById('bdIsAllDay').checked;
        document.getElementById('bdTimeRangeFields').style.display=all?'none':'grid';
        document.getElementById('bdStartTime').disabled=all; document.getElementById('bdEndTime').disabled=all;
        document.getElementById('bdSubmitButton').innerHTML=all?'<i class="fas fa-ban"></i> Block This Day':'<i class="fas fa-clock"></i> Block Time';
        if(!all&&document.getElementById('bdBlockDateInput').value) setDefaultRange(document.getElementById('bdBlockDateInput').value);
        else if(!all) renderChips();
    }

    window.changeCalMonth = function(dir){
        calMonth+=dir;
        if(calMonth>11){calMonth=0;calYear++;} if(calMonth<0){calMonth=11;calYear--;}
        renderCal();
    };

    function renderCal(){
        const months=['January','February','March','April','May','June','July','August','September','October','November','December'];
        document.getElementById('bdCalMonth').textContent=months[calMonth]+' '+calYear;
        const grid=document.getElementById('bdCalDays'); grid.innerHTML='';
        const first=new Date(calYear,calMonth,1), last=new Date(calYear,calMonth+1,0);
        const today=new Date(); today.setHours(0,0,0,0);
        const blocksByDate=groupedBlocks();

        for(let i=0;i<first.getDay();i++){const e=document.createElement('div');e.className='bd-cal-cell empty';grid.appendChild(e);}

        for(let d=1;d<=last.getDate();d++){
            const cell=document.createElement('div');
            const dateObj=new Date(calYear,calMonth,d);
            const ds=calYear+'-'+String(calMonth+1).padStart(2,'0')+'-'+String(d).padStart(2,'0');
            const isPast=dateObj<today, isToday=dateObj.getTime()===today.getTime();
            const dayBlocks=blocksByDate[ds]||[], isBlocked=allDayBlocked.includes(ds);
            const hasPartial=dayBlocks.length>0&&!isBlocked;
            const booked=bookedSlots[ds]||[];

            cell.className='bd-cal-cell';
            if(isPast) cell.classList.add('past');
            if(isBlocked) cell.classList.add('blocked');
            if(hasPartial){cell.style.background='#fff3bf';cell.style.borderColor='#f59f00';cell.style.color='#8a5a00';}
            if(booked.length>0&&!isBlocked){
                cell.style.background='#dbeafe'; cell.style.borderColor='#3b82f6'; cell.style.color='#1e40af';
            }
            if(isToday) cell.classList.add('today');

            // Day number
            const num=document.createElement('span'); num.textContent=d; cell.appendChild(num);

            // Booked dot indicator
            if(booked.length>0&&!isBlocked){
                const dot=document.createElement('span');
                dot.style.cssText='display:block;width:6px;height:6px;border-radius:50%;background:#3b82f6;margin:2px auto 0;';
                cell.appendChild(dot);
                cell.title=booked.map(b=>b.time+' – '+b.client+' ('+b.type+')').join('\n');
            }

            if(!isPast&&!isBlocked){
                cell.style.cursor='pointer';
                cell.onclick=function(){
                    document.getElementById('bdBlockDateInput').value=ds;
                    document.getElementById('bdBlockDateLabel').textContent=dateObj.toLocaleDateString('en-US',{weekday:'long',month:'long',day:'numeric',year:'numeric'});
                    document.getElementById('bdStartTime').value=''; document.getElementById('bdEndTime').value='';
                    document.getElementById('bdBlockForm').style.display='block';
                    if(!document.getElementById('bdIsAllDay').checked) setDefaultRange(ds);
                    document.getElementById('bdBlockForm').scrollIntoView({behavior:'smooth',block:'nearest'});
                };
            }
            if(isBlocked) cell.title='Blocked for the whole day';
            else if(hasPartial) cell.title=dayBlocks.map(b=>b.label+(b.reason?' - '+b.reason:'')).join('\n');

            grid.appendChild(cell);
        }
    }

    // Init
    const now=new Date(); calYear=now.getFullYear(); calMonth=now.getMonth();
    document.getElementById('bdIsAllDay').checked={{ old('is_all_day') !== null ? (old('is_all_day') ? 'true' : 'false') : ((old('start_time') || old('end_time')) ? 'false' : 'true') }};
    renderChips(); updateBlockUI();
    document.getElementById('bdIsAllDay').addEventListener('change',updateBlockUI);
    renderCal();
    @if($errors->any())
        document.getElementById('bdBlockDateInput').value=@json(old('blocked_date'));
        document.getElementById('bdBlockDateLabel').textContent=@json(old('blocked_date') ? \Carbon\Carbon::parse(old('blocked_date'))->format('l, F j, Y') : '');
        document.getElementById('bdBlockForm').style.display='block';
    @endif
})();
</script>
@endpush
