@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')

@php $user = Auth::user(); @endphp

<style>
/* -- Hero Banner -- */
.db-hero {
    background: linear-gradient(135deg, #1e2d4d 0%, #2a3f6f 100%);
    border-radius: 16px;
    padding: 36px 40px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 28px;
    color: #fff;
}
.db-hero-text small { font-size: .82rem; color: rgba(255,255,255,.65); letter-spacing: .03em; }
.db-hero-text h1 { font-size: 2rem; font-weight: 700; margin: 4px 0 6px; }
.db-hero-text p { font-size: .92rem; color: rgba(255,255,255,.7); }
.db-hero-badges { display: flex; gap: 12px; }
.db-hero-badge {
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 14px;
    padding: 14px 22px;
    text-align: center;
    min-width: 100px;
}
.db-hero-badge .badge-num { font-size: 1.7rem; font-weight: 700; display: block; }
.db-hero-badge .badge-lbl { font-size: .78rem; color: rgba(255,255,255,.65); margin-top: 2px; }

/* -- Stat cards -- */
.db-stats { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; margin-bottom: 28px; }
.db-stat-card {
    background: #fff;
    border: 1px solid #eef0f3;
    border-radius: 14px;
    padding: 20px 22px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 1px 4px rgba(0,0,0,.04);
}
.db-stat-icon {
    width: 48px; height: 48px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; flex-shrink: 0;
}
.db-stat-icon.blue  { background: #eff5ff; color: #2563eb; }
.db-stat-icon.amber { background: #fff8ea; color: #d97706; }
.db-stat-icon.teal  { background: #edfaf6; color: #0d9488; }
.db-stat-icon.green { background: #ecfdf5; color: #059669; }
.db-stat-val  { font-size: 1.5rem; font-weight: 700; color: #1e2d4d; line-height: 1; }
.db-stat-lbl  { font-size: .78rem; color: #6b7280; margin-top: 2px; }
.db-stat-sub  { font-size: .73rem; color: #059669; font-weight: 600; margin-top: 3px; }

/* -- Main two-column layout -- */
.db-main { display: grid; grid-template-columns: 1fr 380px; gap: 24px; }

/* -- Cards -- */
.db-card {
    background: #fff;
    border: 1px solid #eef0f3;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 1px 4px rgba(0,0,0,.04);
    margin-bottom: 24px;
}
.db-card-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 18px;
}
.db-card-title { font-size: 1rem; font-weight: 700; color: #1e2d4d; }
.db-card-link  { font-size: .82rem; color: #2563eb; text-decoration: none; font-weight: 600; }
.db-card-link:hover { text-decoration: underline; }

/* -- Tabs -- */
.db-tabs { display: flex; gap: 0; border-bottom: 2px solid #f0f0f0; margin-bottom: 20px; }
.db-tab {
    padding: 8px 18px; font-size: .88rem; font-weight: 600;
    color: #6b7280; border: none; background: none; cursor: pointer;
    border-bottom: 3px solid transparent; margin-bottom: -2px;
    transition: color .2s;
}
.db-tab.active { color: #1e2d4d; border-bottom-color: #1e2d4d; }

/* -- Appointment cards -- */
.appt-card {
    border: 1px solid #eef0f3;
    border-radius: 14px;
    padding: 18px 20px;
    margin-bottom: 14px;
    background: #fafbfc;
}
.appt-top    { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 10px; }
.appt-lawyer { display: flex; align-items: center; gap: 13px; }
.appt-avatar {
    width: 50px; height: 50px; border-radius: 50%; object-fit: cover;
    background: #1e2d4d; color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: .95rem; font-weight: 700; flex-shrink: 0; overflow: hidden;
}
.appt-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
.appt-name      { font-size: .95rem; font-weight: 700; color: #1e2d4d; }
.appt-specialty { font-size: .8rem; color: #6b7280; margin-top: 2px; }
.appt-status-badge {
    font-size: .75rem; font-weight: 600; padding: 4px 12px; border-radius: 20px;
    display: inline-flex; align-items: center; gap: 5px;
}
.appt-status-badge.upcoming  { background: #eff8ff; color: #2563eb; }
.appt-status-badge.completed { background: #ecfdf5; color: #059669; }
.appt-status-badge.cancelled { background: #fef2f2; color: #dc2626; }
.appt-status-badge.expired   { background: #f3f4f6; color: #6b7280; }
.appt-status-badge .dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
.appt-meta { display: flex; align-items: center; gap: 20px; font-size: .82rem; color: #6b7280; flex-wrap: wrap; margin-bottom: 14px; }
.appt-meta span { display: inline-flex; align-items: center; gap: 5px; }
.appt-meta .price { font-weight: 700; color: #1e2d4d; font-size: .88rem; }
.appt-btns { display: flex; gap: 10px; }
.appt-btn-join {
    flex: 1; padding: 10px; border-radius: 10px;
    background: #1e2d4d; color: #fff; border: none;
    font-size: .88rem; font-weight: 600; cursor: pointer;
    text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 7px;
    font-family: inherit; transition: background .2s;
}
.appt-btn-join:hover { background: #162340; color: #fff; }
.appt-btn-join.disabled {
    background: #d1d5db; color: #9ca3af; cursor: not-allowed; pointer-events: none;
}
.appt-btn-join.disabled:hover { background: #d1d5db; color: #9ca3af; }
.appt-btn-cancel {
    flex: 1; padding: 10px; border-radius: 10px;
    background: #f4f6f8; color: #6b7280; border: none;
    font-size: .88rem; font-weight: 600; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 7px;
    font-family: inherit; transition: background .2s;
}
.appt-btn-cancel:hover { background: #ffe4e4; color: #dc2626; }
.appt-empty { text-align: center; padding: 40px 20px; color: #9ca3af; }
.appt-empty i { font-size: 2.2rem; display: block; margin-bottom: 12px; opacity: .4; }
.appt-empty a { color: #2563eb; text-decoration: none; }

/* -- Quick search -- */
.qs-form { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }
.qs-field label { font-size: .78rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 5px; }
.qs-field select {
    width: 100%; padding: 9px 12px; border-radius: 8px;
    border: 1px solid #e2e6ea; font-size: .88rem; color: #1e2d4d;
    background: #fff; appearance: none; cursor: pointer;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 10px center;
}
.qs-rate-label { font-size: .78rem; font-weight: 600; color: #6b7280; margin-bottom: 5px; }
.qs-rate-val   { color: #1e2d4d; font-weight: 700; }
.qs-range { width: 100%; accent-color: #1e2d4d; margin: 6px 0 4px; }
.qs-range-bounds { display: flex; justify-content: space-between; font-size: .73rem; color: #9ca3af; }
.qs-search-btn {
    width: 100%; padding: 12px; border-radius: 10px;
    background: #1e2d4d; color: #fff; border: none;
    font-size: .92rem; font-weight: 600; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    font-family: inherit; margin-top: 6px; transition: background .2s;
}
.qs-search-btn:hover { background: #162340; }

/* -- Activity feed -- */
.activity-item { display: flex; gap: 14px; padding: 14px 0; border-bottom: 1px solid #f0f2f5; }
.activity-item:last-child { border-bottom: none; }
.activity-icon {
    width: 38px; height: 38px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem; flex-shrink: 0;
}
.activity-icon.msg  { background: #eff5ff; color: #2563eb; }
.activity-icon.cal  { background: #fff8ea; color: #d97706; }
.activity-icon.pay  { background: #ecfdf5; color: #059669; }
.activity-icon.bell { background: #fef2f2; color: #dc2626; }
.activity-title { font-size: .88rem; font-weight: 700; color: #1e2d4d; }
.activity-desc  { font-size: .8rem; color: #6b7280; margin-top: 2px; line-height: 1.45; }
.activity-time  { font-size: .75rem; color: #9ca3af; margin-top: 4px; }
.activity-empty { text-align: center; padding: 30px; color: #9ca3af; font-size: .88rem; }

/* -- Payment history -- */
.pay-item { display: flex; align-items: center; gap: 14px; padding: 14px 0; border-bottom: 1px solid #f0f2f5; }
.pay-item:last-child { border-bottom: none; }
.pay-icon { width: 36px; height: 36px; border-radius: 8px; background: #f0f4ff; color: #2563eb; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.pay-info { flex: 1; min-width: 0; }
.pay-lawyer { font-size: .88rem; font-weight: 600; color: #1e2d4d; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.pay-sub    { font-size: .75rem; color: #9ca3af; margin-top: 1px; }
.pay-right  { text-align: right; flex-shrink: 0; }
.pay-amount { font-size: .92rem; font-weight: 700; color: #1e2d4d; }
.pay-badge  { font-size: .7rem; font-weight: 600; padding: 2px 8px; border-radius: 10px; display: inline-block; margin-top: 3px; }
.pay-badge.paid    { background: #ecfdf5; color: #059669; }
.pay-badge.pending { background: #fff8ea; color: #d97706; }
.pay-badge.refunded{ background: #f3f4f6; color: #6b7280; }
</style>

@if(session('success'))
<div style="background:#ecfdf5;border:1px solid #6ee7b7;color:#065f46;border-radius:10px;padding:12px 18px;margin-bottom:20px;display:flex;align-items:center;gap:8px;">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

{{-- -- HERO BANNER -- --}}
<div class="db-hero">
    <div class="db-hero-text">
        <small>Welcome back,</small>
        <h1>{{ $user->name }}</h1>
        <p>Here's your legal services overview for today</p>
    </div>
    <div class="db-hero-badges">
        <div class="db-hero-badge">
            <span class="badge-num">₱{{ number_format($totalSpent, 0) }}</span>
            <div class="badge-lbl">Total Spent</div>
            <div style="font-size:.72rem;color:#059669;margin-top:4px;font-weight:600;">this month</div>
        </div>
    </div>
</div>

{{-- -- STAT CARDS -- --}}
<div class="db-stats">
    <div class="db-stat-card">
        <div class="db-stat-icon blue"><i class="fas fa-video"></i></div>
        <div>
            <div class="db-stat-val">{{ $totalConsultations }}</div>
            <div class="db-stat-lbl">Total Consultations</div>
            @if($thisMonthConsultations > 0)
            <div class="db-stat-sub">+{{ $thisMonthConsultations }} this month</div>
            @endif
        </div>
    </div>
    <div class="db-stat-card">
        <div class="db-stat-icon amber"><i class="fas fa-calendar-alt"></i></div>
        <div>
            <div class="db-stat-val">{{ $upcomingCount }}</div>
            <div class="db-stat-lbl">Upcoming Sessions</div>
        </div>
    </div>
    <div class="db-stat-card">
        <div class="db-stat-icon teal"><i class="fas fa-comment-dots"></i></div>
        <div>
            <div class="db-stat-val">{{ $unreadMessages }}</div>
            <div class="db-stat-lbl">Unread Messages</div>
        </div>
    </div>
</div>

{{-- -- MAIN TWO-COLUMN LAYOUT -- --}}
<div class="db-main">

    {{-- LEFT COLUMN --}}
    <div>

        {{-- My Appointments --}}
        <div class="db-card">
            <div class="db-card-header">
                <span class="db-card-title">My Appointments</span>
                <a href="{{ route('consultations') }}" class="db-card-link">Book New +</a>
            </div>

            <div class="db-tabs">
                <button class="db-tab active" onclick="dbTab('upcoming',this)">Upcoming</button>
                <button class="db-tab" onclick="dbTab('completed',this)">Completed</button>
                <button class="db-tab" onclick="dbTab('cancelled',this)">Cancelled</button>
                <button class="db-tab" onclick="dbTab('expired',this)">Expired</button>
            </div>

            {{-- Upcoming tab --}}
            <div id="dbt-upcoming">
                @forelse($upcomingConsultations as $c)
                @php $scheduled = \Carbon\Carbon::parse($c->scheduled_at); @endphp
                <div class="appt-card">
                    <div class="appt-top">
                        <div class="appt-lawyer">
                            <div class="appt-avatar">
                                @if($c->lawyer->avatar_url)
                                    <img src="{{ $c->lawyer->avatar_url }}" alt="{{ $c->lawyer->name }}">
                                @else
                                    {{ strtoupper(substr($c->lawyer->name,0,2)) }}
                                @endif
                            </div>
                            <div>
                                <div class="appt-name">{{ $c->lawyer->name }}</div>
                                <div class="appt-specialty">{{ optional($c->lawyer->lawyerProfile)->specialty ?? 'Attorney at Law' }}</div>
                            </div>
                        </div>
                        <span class="appt-status-badge upcoming"><span class="dot"></span> Upcoming</s  pan>
                    </div>
                    <div class="appt-meta">
                        <span><i class="fas fa-calendar"></i> {{ $scheduled->format('m/d/Y') }}</span>
                        <span><i class="fas fa-clock"></i> {{ $scheduled->format('g:i A') }} &bull; {{ $c->duration_label }}</span>
                        <span><i class="fas fa-{{ $c->type === 'video' ? 'video' : ($c->type === 'phone' ? 'phone' : 'building') }}"></i> {{ ucfirst($c->type) }}</span>
                        <span class="price"><i class="fas fa-peso-sign"></i> {{ number_format($c->price, 0) }}</span>
                    </div>
                    <div class="appt-btns">
                        @if($c->type === 'video')
                        @php $canJoin = now()->gte($scheduled->copy()->subMinutes(5)); @endphp
                        @if($canJoin)
                        <a href="{{ route('consultations.video', $c) }}" class="appt-btn-join">
                            <i class="fas fa-video"></i> Join Call
                        </a>
                        @else
                        <span class="appt-btn-join disabled" title="Available at {{ $scheduled->format('g:i A') }}">
                            <i class="fas fa-clock"></i> Starts {{ $scheduled->format('g:i A') }}
                        </span>
                        @endif
                        @else
                        <a href="{{ route('consultations') }}" class="appt-btn-join">
                            <i class="fas fa-calendar-check"></i> View Details
                        </a>
                        @endif
                        <form method="POST" action="{{ route('consultations.cancel', $c) }}" style="flex:1;">
                            @csrf
                            <button type="submit" class="appt-btn-cancel" style="width:100%;"
                                onclick="return confirm('Cancel this consultation?')">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="appt-empty">
                    <i class="fas fa-calendar"></i>
                    No upcoming consultations.<br>
                    <a href="{{ route('find-lawyers') }}">Book one now</a>
                </div>
                @endforelse
            </div>

            {{-- Completed tab --}}
            <div id="dbt-completed" style="display:none;">
                @forelse($completedConsultations as $c)
                @php $scheduled = \Carbon\Carbon::parse($c->scheduled_at); @endphp
                <div class="appt-card">
                    <div class="appt-top">
                        <div class="appt-lawyer">
                            <div class="appt-avatar">
                                @if($c->lawyer->avatar_url)
                                    <img src="{{ $c->lawyer->avatar_url }}" alt="{{ $c->lawyer->name }}">
                                @else
                                    {{ strtoupper(substr($c->lawyer->name,0,2)) }}
                                @endif
                            </div>
                            <div>
                                <div class="appt-name">{{ $c->lawyer->name }}</div>
                                <div class="appt-specialty">{{ optional($c->lawyer->lawyerProfile)->specialty ?? 'Attorney at Law' }}</div>
                            </div>
                        </div>
                        <span class="appt-status-badge completed"><span class="dot"></span> Completed</span>
                    </div>
                    <div class="appt-meta">
                        <span><i class="fas fa-calendar"></i> {{ $scheduled->format('m/d/Y') }}</span>
                        <span><i class="fas fa-clock"></i> {{ $scheduled->format('g:i A') }} &bull; {{ $c->duration_label }}</span>
                        <span><i class="fas fa-{{ $c->type === 'video' ? 'video' : ($c->type === 'phone' ? 'phone' : 'building') }}"></i> {{ ucfirst($c->type) }}</span>
                        <span class="price"><i class="fas fa-peso-sign"></i> {{ number_format($c->price, 0) }}</span>
                    </div>
                </div>
                @empty
                <div class="appt-empty">
                    <i class="fas fa-check-circle"></i>
                    No completed consultations yet.
                </div>
                @endforelse
            </div>

            {{-- Cancelled tab --}}
            <div id="dbt-cancelled" style="display:none;">
                @forelse($cancelledConsultations as $c)
                @php $scheduled = \Carbon\Carbon::parse($c->scheduled_at); @endphp
                <div class="appt-card">
                    <div class="appt-top">
                        <div class="appt-lawyer">
                            <div class="appt-avatar">
                                @if($c->lawyer->avatar_url)
                                    <img src="{{ $c->lawyer->avatar_url }}" alt="{{ $c->lawyer->name }}">
                                @else
                                    {{ strtoupper(substr($c->lawyer->name,0,2)) }}
                                @endif
                            </div>
                            <div>
                                <div class="appt-name">{{ $c->lawyer->name }}</div>
                                <div class="appt-specialty">{{ optional($c->lawyer->lawyerProfile)->specialty ?? 'Attorney at Law' }}</div>
                            </div>
                        </div>
                        <span class="appt-status-badge cancelled"><span class="dot"></span> Cancelled</span>
                    </div>
                    <div class="appt-meta">
                        <span><i class="fas fa-calendar"></i> {{ $scheduled->format('m/d/Y') }}</span>
                        <span><i class="fas fa-clock"></i> {{ $scheduled->format('g:i A') }} &bull; {{ $c->duration_label }}</span>
                        <span><i class="fas fa-{{ $c->type === 'video' ? 'video' : ($c->type === 'phone' ? 'phone' : 'building') }}"></i> {{ ucfirst($c->type) }}</span>
                        <span class="price"><i class="fas fa-peso-sign"></i> {{ number_format($c->price, 0) }}</span>
                    </div>
                </div>
                @empty
                <div class="appt-empty">
                    <i class="fas fa-ban"></i>
                    No cancelled consultations.
                </div>
                @endforelse
            </div>

            {{-- Expired tab --}}
            <div id="dbt-expired" style="display:none;">
                @forelse($expiredConsultations as $c)
                @php $scheduled = \Carbon\Carbon::parse($c->scheduled_at); @endphp
                <div class="appt-card">
                    <div class="appt-top">
                        <div class="appt-lawyer">
                            <div class="appt-avatar">
                                @if($c->lawyer->avatar_url)
                                    <img src="{{ $c->lawyer->avatar_url }}" alt="{{ $c->lawyer->name }}">
                                @else
                                    {{ strtoupper(substr($c->lawyer->name,0,2)) }}
                                @endif
                            </div>
                            <div>
                                <div class="appt-name">{{ $c->lawyer->name }}</div>
                                <div class="appt-specialty">{{ optional($c->lawyer->lawyerProfile)->specialty ?? 'Attorney at Law' }}</div>
                            </div>
                        </div>
                        <span class="appt-status-badge expired"><span class="dot"></span> Expired</span>
                    </div>
                    <div class="appt-meta">
                        <span><i class="fas fa-calendar"></i> {{ $scheduled->format('m/d/Y') }}</span>
                        <span><i class="fas fa-clock"></i> {{ $scheduled->format('g:i A') }} &bull; {{ $c->duration_label }}</span>
                        <span><i class="fas fa-{{ $c->type === 'video' ? 'video' : ($c->type === 'phone' ? 'phone' : 'building') }}"></i> {{ ucfirst($c->type) }}</span>
                        <span class="price"><i class="fas fa-peso-sign"></i> {{ number_format($c->price, 0) }}</span>
                    </div>
                </div>
                @empty
                <div class="appt-empty">
                    <i class="fas fa-clock"></i>
                    No expired consultations.
                </div>
                @endforelse
            </div>
        </div>

        {{-- Quick Lawyer Search --}}
        <div class="db-card">
            <div class="db-card-header">
                <span class="db-card-title">Quick Lawyer Search</span>
                <a href="{{ route('find-lawyers') }}" class="db-card-link">Advanced Search &rarr;</a>
            </div>
            <form action="{{ route('find-lawyers') }}" method="GET">
                <div class="qs-form">
                    <div class="qs-field">
                        <label>Specialty</label>
                        <select name="specialty">
                            <option value="">All Specialties</option>
                            <option>Corporate Law</option>
                            <option>Family Law</option>
                            <option>Criminal Defense</option>
                            <option>Immigration Law</option>
                            <option>Real Estate</option>
                            <option>Intellectual Property</option>
                            <option>Tax Law</option>
                            <option>Labor Law</option>
                            <option>Civil Litigation</option>
                        </select>
                    </div>
                    <div class="qs-field">
                        <label>Location</label>
                        <select name="search">
                            <option value="">All Locations</option>
                            <option>Manila</option>
                            <option>Cebu</option>
                            <option>Davao</option>
                            <option>Quezon City</option>
                            <option>Makati</option>
                            <option>Pasig</option>
                        </select>
                    </div>
                </div>
                <div>
                    <div class="qs-rate-label">Max Rate: <span class="qs-rate-val" id="rateVal">₱500/hr</span></div>
                    <input type="range" class="qs-range" name="max_rate" min="50" max="1000" step="50" value="500"
                        oninput="document.getElementById('rateVal').textContent='₱'+this.value+'/hr'">
                    <div class="qs-range-bounds"><span>₱50</span><span>₱1,000</span></div>
                </div>
                <button type="submit" class="qs-search-btn">
                    <i class="fas fa-search"></i> Search Lawyers
                </button>
            </form>
        </div>

    </div>{{-- end left column --}}

    {{-- RIGHT COLUMN --}}
    <div>

        {{-- Recent Activity --}}
        <div class="db-card">
            <div class="db-card-header">
                <span class="db-card-title">Recent Activity</span>
            </div>
            @php
                $activities = collect();
                // Upcoming consultation reminders
                foreach($upcomingConsultations->take(2) as $c) {
                    $activities->push([
                        'icon'  => 'bell',
                        'fa'    => 'bell',
                        'title' => 'Appointment reminder',
                        'desc'  => 'Your consultation with '.$c->lawyer->name.' is on '.
                                   \Carbon\Carbon::parse($c->scheduled_at)->format('M d').' at '.
                                   \Carbon\Carbon::parse($c->scheduled_at)->format('g:i A').'. Prepare your documents.',
                        'time'  => \Carbon\Carbon::parse($c->scheduled_at)->diffForHumans(),
                    ]);
                }
                // Recent payments
                foreach($recentPayments->take(3) as $p) {
                    if ($p->status === 'paid') {
                        $activities->push([
                            'icon'  => 'pay',
                            'fa'    => 'credit-card',
                            'title' => 'Payment processed',
                            'desc'  => 'Payment of &#8369;'.number_format($p->amount,0).' for consultation with '.$p->lawyer->name.' was successful.',
                            'time'  => $p->created_at->diffForHumans(),
                        ]);
                    } elseif ($p->status === 'pending') {
                        $activities->push([
                            'icon'  => 'cal',
                            'fa'    => 'calendar-check',
                            'title' => 'Appointment confirmed',
                            'desc'  => 'Your consultation with '.$p->lawyer->name.' has been confirmed.',
                            'time'  => $p->created_at->diffForHumans(),
                        ]);
                    }
                }
            @endphp

            @if($activities->isEmpty())
            <div class="activity-empty">No recent activity yet.</div>
            @else
            @foreach($activities->take(5) as $act)
            <div class="activity-item">
                <div class="activity-icon {{ $act['icon'] }}">
                    <i class="fas fa-{{ $act['fa'] }}"></i>
                </div>
                <div>
                    <div class="activity-title">{{ $act['title'] }}</div>
                    <div class="activity-desc">{{ $act['desc'] }}</div>
                    <div class="activity-time">{{ $act['time'] }}</div>
                </div>
            </div>
            @endforeach
            @endif
        </div>

        {{-- Payment History --}}
        <div class="db-card">
            <div class="db-card-header">
                <span class="db-card-title">Payment History</span>
                <a href="{{ route('payments') }}" class="db-card-link">View All &rarr;</a>
            </div>
            @forelse($recentPayments as $p)
            <div class="pay-item">
                <div class="pay-icon"><i class="fas fa-credit-card"></i></div>
                <div class="pay-info">
                    <div class="pay-lawyer">{{ $p->lawyer->name }}</div>
                    <div class="pay-sub">{{ optional($p->consultation)->type ? ucfirst(optional($p->consultation)->type).' Consultation' : 'Consultation' }} &bull; {{ $p->created_at->format('m/d/Y') }}</div>
                </div>
                <div class="pay-right">
                    <div class="pay-amount">&#8369;{{ number_format($p->amount, 0) }}</div>
                    <span class="pay-badge {{ $p->status }}">{{ ucfirst($p->status) }}</span>
                </div>
            </div>
            @empty
            <p style="text-align:center;color:#9ca3af;padding:20px;font-size:.88rem;">No payments yet.</p>
            @endforelse
        </div>

    </div>{{-- end right column --}}

</div>{{-- end db-main --}}

<script>
function dbTab(name, el) {
    ['upcoming','completed','cancelled','expired'].forEach(function(t) {
        document.getElementById('dbt-'+t).style.display = t === name ? '' : 'none';
    });
    document.querySelectorAll('.db-tab').forEach(function(btn) { btn.classList.remove('active'); });
    el.classList.add('active');
}
</script>

@endsection


