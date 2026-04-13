@extends('layouts.app')
@section('title', 'Find Lawyers')

@push('styles')
<style>
/* Quick Book section on lawyer card */
.lc-quick-book { border-top: 1px solid #f0f0f0; padding-top: 10px; margin-top: 10px; }
.lc-qb-label { font-size: .72rem; font-weight: 700; color: #888; letter-spacing: .5px; text-transform: uppercase; margin-bottom: 6px; }
.lc-qb-label i { color: #f59e0b; margin-right: 3px; }
.lc-qb-slots { display: flex; flex-wrap: wrap; gap: 5px; }
.lc-qb-chip {
    background: #f0fdf4; color: #15803d; border: 1.5px solid #86efac;
    border-radius: 6px; padding: 4px 9px; font-size: .75rem; font-weight: 600;
    cursor: pointer; transition: background .15s, border-color .15s; white-space: nowrap; font-family: inherit;
}
.lc-qb-chip:hover { background: #dcfce7; border-color: #4ade80; }
.lc-qb-more {
    background: #f0f4ff; color: #1e2d4d; border: 1.5px solid #c7d7f5;
    border-radius: 6px; padding: 4px 9px; font-size: .75rem; font-weight: 600;
    cursor: pointer; transition: background .15s; font-family: inherit;
}
.lc-qb-more:hover { background: #dce8ff; }

/* Payment Method Picker Modal */
.pm-overlay { position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:1100;display:flex;align-items:center;justify-content:center;padding:16px; }
.pm-box { background:#fff;border-radius:16px;width:100%;max-width:480px;max-height:90vh;box-shadow:0 20px 60px rgba(0,0,0,.25);display:flex;flex-direction:column;overflow:hidden; }
.pm-header { background:#1e2d4d;padding:18px 22px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0; }
.pm-header h3 { color:#fff;font-size:1rem;font-weight:700;margin:0; }
.pm-header-close { background:none;border:none;color:rgba(255,255,255,.7);font-size:1.1rem;cursor:pointer;padding:4px;line-height:1; }
.pm-summary { background:#f0f4ff;border-bottom:1px solid #e2e8f0;padding:14px 22px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0; }
.pm-summary-label { font-size:.82rem;color:#6c757d; }
.pm-summary-amount { font-size:1.35rem;font-weight:800;color:#1e2d4d; }
.pm-body { padding:20px 22px;overflow-y:auto;flex:1; }
.pm-body-title { font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#9ca3af;margin:0 0 14px; }
.pm-methods { display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:18px; }
.pm-method {
    border:2px solid #e5e7eb;border-radius:12px;padding:14px 12px;
    display:flex;flex-direction:column;align-items:center;gap:7px;
    cursor:pointer;transition:all .18s;background:#fff;font-family:inherit;
    text-align:center;
}
.pm-method:hover { border-color:#1e2d4d;background:#f8faff; }
.pm-method.selected { border-color:#1e2d4d;background:#eef2ff;box-shadow:0 0 0 3px rgba(30,45,77,.15); }
.pm-method-icon { width:48px;height:48px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.4rem; }
.pm-method-name { font-size:.82rem;font-weight:700;color:#1e2d4d; }
.pm-method-sub { font-size:.7rem;color:#9ca3af; }
.pm-footer { display:flex;gap:10px;padding:16px 22px;border-top:1px solid #f0f0f0;flex-shrink:0; }
.pm-btn-back { flex:1;padding:11px;border:1.5px solid #d1d5db;border-radius:8px;background:#fff;color:#4b5563;font-size:.9rem;font-weight:600;cursor:pointer;font-family:inherit;transition:background .15s; }
.pm-btn-back:hover { background:#f9fafb; }
.pm-btn-pay { flex:2;padding:11px;border:none;border-radius:8px;background:#1e2d4d;color:#fff;font-size:.9rem;font-weight:700;cursor:pointer;font-family:inherit;transition:background .15s;display:flex;align-items:center;justify-content:center;gap:8px; }
.pm-btn-pay:hover { background:#2b3f6b; }
.pm-btn-pay:disabled { background:#9ca3af;cursor:not-allowed; }

/* Schedule Modal – Calendar View */
.sched-modal-box { max-width: 580px !important; }
.sc-cal-nav { display: flex; align-items: center; justify-content: center; gap: 16px; margin-bottom: 12px; }
.sc-cal-nav-btn { background: none; border: 1px solid #d1d5db; border-radius: 8px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #1e2d4d; font-size: .85rem; transition: background .15s; font-family: inherit; }
.sc-cal-nav-btn:hover { background: #f0f2f5; }
.sc-cal-month { font-size: 1rem; font-weight: 700; color: #1e2d4d; min-width: 160px; text-align: center; }
.sc-cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 3px; margin-bottom: 6px; }
.sc-cal-head { text-align: center; font-size: .7rem; font-weight: 700; color: #6c757d; padding: 4px 0; text-transform: uppercase; }
.sc-cal-cell {
    text-align: center; padding: 8px 2px; border-radius: 8px; font-size: .82rem;
    font-weight: 500; color: #1e2d4d; border: 1.5px solid transparent; cursor: pointer; transition: all .15s;
}
.sc-cal-cell:hover:not(.past):not(.blocked):not(.empty):not(.other-month) { background: #eef2ff; border-color: #6366f1; }
.sc-cal-cell.past { color: #c0c5cd; cursor: default; }
.sc-cal-cell.blocked { background: #fce4e4; color: #dc3545; font-weight: 700; border-color: #f5c2c7; cursor: not-allowed; }
.sc-cal-cell.has-bookings { position: relative; }
.sc-cal-cell.has-bookings::after { content: ''; position: absolute; bottom: 3px; left: 50%; transform: translateX(-50%); width: 5px; height: 5px; border-radius: 50%; background: #f59e0b; }
.sc-cal-cell.today { border-color: #1e2d4d; font-weight: 800; }
.sc-cal-cell.selected { background: #1e2d4d; color: #fff; border-color: #1e2d4d; }
.sc-cal-cell.selected::after { background: #fff; }
.sc-cal-cell.empty { visibility: hidden; }
.sc-cal-cell.other-month { color: #d1d5db; cursor: default; }

/* Time slots panel */
.sc-slots-panel { border-top: 1px solid #f0f2f5; padding-top: 14px; margin-top: 8px; }
.sc-slots-title { font-size: .9rem; font-weight: 700; color: #1e2d4d; margin: 0 0 10px; display: flex; align-items: center; gap: 6px; }
.sc-slots-grid { display: flex; flex-wrap: wrap; gap: 6px; max-height: 200px; overflow-y: auto; }
.sc-slot {
    padding: 7px 14px; border-radius: 8px; border: 1.5px solid; font-size: .8rem;
    font-weight: 600; cursor: pointer; transition: all .15s; font-family: inherit;
}
.sc-slot.available { background: #f0fdf4; border-color: #86efac; color: #15803d; }
.sc-slot.available:hover { background: #dcfce7; border-color: #4ade80; }
.sc-slot.booked { background: #fff5f5; border-color: #fca5a5; color: #dc3545; cursor: not-allowed; }
.sc-slot.past { background: #f9f9f9; border-color: #e9ecef; color: #aaa; cursor: default; }
</style>
@endpush

@section('content')

{{-- Hidden data for JavaScript validation/errors --}}
<div id="booking-validation-data" 
     data-has-errors="{{ $errors->any() ? 'true' : 'false' }}" 
     data-old-lawyer-id="{{ old('lawyer_id') }}"
     style="display:none;">
</div>

{{-- Page title --}}
<div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
    <i class="fas fa-balance-scale" style="font-size:1.4rem;color:#1e2d4d;"></i>
    <h1 style="font-size:1.6rem;font-weight:800;color:#1e2d4d;margin:0;">Find a Lawyer</h1>
</div>
<p style="color:#6c757d;font-size:.9rem;margin:0 0 22px;">Browse and connect with certified legal professionals</p>

{{-- Full-width search bar --}}
<form method="GET" action="{{ route('find-lawyers') }}" id="searchForm">
    <div class="fl-search-bar">
        <i class="fas fa-search" style="color:#aaa;font-size:1rem;"></i>
        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="Search by name, specialty, or firm..."
            class="fl-search-input"
            autocomplete="off">
        {{-- preserve other active filters --}}
        @foreach(request()->except(['search','_token']) as $k => $v)
            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
        @endforeach
        <button type="submit" class="fl-search-btn">Search <i class="fas fa-arrow-right"></i></button>
    </div>
</form>

@if(session('success'))
<div style="background:#d4edda;border:1px solid #c3e6cb;color:#155724;border-radius:8px;padding:12px 18px;margin-bottom:20px;">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

<div class="fl-body">

    {{-- SIDEBAR FILTERS --}}
    <aside class="fl-sidebar">
        <form method="GET" action="{{ route('find-lawyers') }}" id="filterForm">
            @if(request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            <input type="hidden" name="sort"   value="{{ request('sort','rating') }}">

            <div class="fl-sidebar-header">
                <span><i class="fas fa-sliders-h"></i> Filters</span>
                <span class="fl-result-count">{{ $lawyers->total() }} results</span>
            </div>

            {{-- Practice Area --}}
            <div class="filter-section open" id="sec-area">
                <div class="filter-section-head" onclick="toggleSection('sec-area')">
                    <span>Practice Area</span>
                    <i class="fas fa-chevron-up fl-chevron"></i>
                </div>
                <div class="filter-section-body">
                    <div class="fl-radio-scroll">
                        <label class="fl-radio-opt">
                            <input type="radio" name="specialty" value="" {{ !request('specialty') ? 'checked' : '' }}> All Areas
                        </label>
                        @foreach($specialties as $s)
                        <label class="fl-radio-opt">
                            <input type="radio" name="specialty" value="{{ $s }}" {{ request('specialty')==$s ? 'checked' : '' }}> {{ $s }}
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Hourly Rate --}}
            <div class="filter-section open" id="sec-rate">
                <div class="filter-section-head" onclick="toggleSection('sec-rate')">
                    <span>Hourly Rate</span>
                    <i class="fas fa-chevron-up fl-chevron"></i>
                </div>
                <div class="filter-section-body">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:4px;">
                        <div>
                            <label style="font-size:.75rem;color:#888;display:block;margin-bottom:4px;">Min (₱)</label>
                            <input type="number" name="min_rate" value="{{ request('min_rate', 0) }}" min="0" max="2000" step="50"
                                class="fl-range-input" placeholder="0">
                        </div>
                        <div>
                            <label style="font-size:.75rem;color:#888;display:block;margin-bottom:4px;">Max (₱)</label>
                            <input type="number" name="max_rate" value="{{ request('max_rate', 1000) }}" min="0" max="2000" step="50"
                                class="fl-range-input" placeholder="1000">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Experience --}}
            <div class="filter-section open" id="sec-exp">
                <div class="filter-section-head" onclick="toggleSection('sec-exp')">
                    <span>Experience</span>
                    <i class="fas fa-chevron-up fl-chevron"></i>
                </div>
                <div class="filter-section-body">
                    <input type="range" name="min_experience" min="0" max="30"
                        value="{{ request('min_experience', 0) }}"
                        class="fl-slider" id="expSlider"
                        oninput="document.getElementById('expVal').textContent = this.value == 0 ? 'Any' : this.value + '+ yrs'">
                    <div style="display:flex;justify-content:space-between;font-size:.75rem;color:#888;margin-top:4px;">
                        <span>Any</span>
                        <span id="expVal">{{ request('min_experience', 0) == 0 ? 'Any' : request('min_experience').'+ yrs' }}</span>
                        <span>30+</span>
                    </div>
                </div>
            </div>

            {{-- Minimum Rating --}}
            <div class="filter-section open" id="sec-rating">
                <div class="filter-section-head" onclick="toggleSection('sec-rating')">
                    <span>Minimum Rating</span>
                    <i class="fas fa-chevron-up fl-chevron"></i>
                </div>
                <div class="filter-section-body">
                    <div class="fl-rating-pills">
                        @foreach(['' => 'Any', '3' => '3★', '3.5' => '3.5★', '4' => '4★', '4.5' => '4.5★'] as $val => $label)
                        <label class="fl-rating-pill {{ request('min_rating')==$val ? 'active' : '' }}">
                            <input type="radio" name="min_rating" value="{{ $val }}" {{ request('min_rating')==$val ? 'checked' : '' }} style="display:none;">
                            {{ $label }}
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Availability --}}
            <div class="filter-section" id="sec-avail">
                <div class="filter-section-head" onclick="toggleSection('sec-avail')">
                    <span>Availability</span>
                    <i class="fas fa-chevron-down fl-chevron"></i>
                </div>
                <div class="filter-section-body" style="display:none;">
                    @foreach(['' => 'Any', 'available' => 'Active Now', 'busy' => 'Busy'] as $val => $label)
                    <label class="fl-radio-opt">
                        <input type="radio" name="availability" value="{{ $val }}" {{ request('availability')==$val ? 'checked' : '' }}> {{ $label }}
                    </label>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="fl-apply-btn">Apply Filters</button>
            <a href="{{ route('find-lawyers') }}" class="fl-clear-link">Clear all</a>
        </form>
    </aside>

    {{-- RESULTS --}}
    <div class="fl-results">
        {{-- Results header --}}
        <div class="fl-results-header">
            <span class="fl-results-count">
                <strong>{{ $lawyers->total() }}</strong> lawyer{{ $lawyers->total() != 1 ? 's' : '' }} found
            </span>
            <div style="display:flex;align-items:center;gap:10px;">
                <select name="sort" class="fl-sort-select" onchange="applySort(this.value)">
                    <option value="rating"    {{ request('sort','rating')=='rating'    ? 'selected' : '' }}>Relevance</option>
                    <option value="rate_asc"  {{ request('sort')=='rate_asc'  ? 'selected' : '' }}>Lowest Rate</option>
                    <option value="rate_desc" {{ request('sort')=='rate_desc' ? 'selected' : '' }}>Highest Rate</option>
                    <option value="reviews"   {{ request('sort')=='reviews'   ? 'selected' : '' }}>Most Reviews</option>
                </select>
                <div class="fl-view-toggle">
                    <button type="button" class="fl-view-btn active" title="Grid"><i class="fas fa-th"></i></button>
                    <button type="button" class="fl-view-btn" title="List"><i class="fas fa-list"></i></button>
                </div>
            </div>
        </div>

        {{-- Lawyer cards grid --}}
        <div class="fl-cards-grid" id="lawyersGrid">
            @forelse($lawyers as $lp)
            @php
                $lawyerStatus = $lp->currentStatus();
            @endphp
            <div class="lc-card">
                {{-- Photo header --}}
                <div class="lc-image-wrap">
                    @if($lp->user->avatar_url)
                        <img src="{{ $lp->user->avatar_url }}" alt="{{ $lp->user->name }}" class="lc-photo">
                    @else
                        <div class="lc-photo" style="display:flex;align-items:center;justify-content:center;background:#1e2d4d;color:#fff;font-size:2.5rem;font-weight:700;">
                            {{ strtoupper(substr($lp->user->name,0,1)) }}
                        </div>
                    @endif
                    {{-- Availability badge --}}
                    <span class="lc-avail-badge {{ $lp->currentStatusClass() }}">
                        <span class="lc-avail-dot"></span>
                        {{ ucfirst($lawyerStatus) }}
                    </span>
                    {{-- Certified badge --}}
                    @if($lp->is_certified)
                    <span class="lc-cert-badge">
                        <i class="fas fa-shield-alt"></i> Certified
                    </span>
                    @endif
                </div>

                {{-- Card body --}}
                <div class="lc-body">
                    <div class="lc-name-rate">
                        <span class="lc-name">{{ $lp->user->name }}</span>
                        <span class="lc-rate">₱{{ number_format($lp->hourly_rate, 0) }}<span class="lc-rate-unit">/hr</span></span>
                    </div>
                    <div class="lc-firm">{{ $lp->firm }}</div>

                    {{-- Star rating --}}
                    <div class="lc-stars-row">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= floor($lp->rating))
                                <i class="fas fa-star lc-star filled"></i>
                            @elseif($i - $lp->rating < 1)
                                <i class="fas fa-star-half-alt lc-star filled"></i>
                            @else
                                <i class="far fa-star lc-star"></i>
                            @endif
                        @endfor
                        <span class="lc-rating-val">{{ number_format($lp->rating,1) }}</span>
                        <span class="lc-reviews">({{ $lp->reviews_count }} reviews)</span>
                    </div>

                    {{-- Specialty tag --}}
                    <div class="lc-tags">
                        <span class="lc-tag">{{ $lp->specialty }}</span>
                    </div>

                    {{-- Meta: exp + location --}}
                    <div class="lc-meta">
                        <span><i class="fas fa-briefcase"></i> {{ $lp->experience_years }} yrs</span>
                        <span><i class="fas fa-map-marker-alt"></i> {{ $lp->location }}</span>
                    </div>

                    {{-- Next slot --}}
                    @if($lp->nextConsultation)
                        <div class="lc-next">
                            <i class="fas fa-calendar-check"></i>
                            Next: {{ \Carbon\Carbon::parse($lp->nextConsultation->scheduled_at)->format('M j, g:i A') }}
                        </div>
                    @elseif($lawyerStatus === 'active')
                        <div class="lc-next">
                            <i class="fas fa-calendar-check"></i> Active Today
                        </div>
                    @else
                        <div class="lc-next" style="color:#dc3545;">
                            <i class="fas fa-clock"></i> Currently {{ ucfirst($lawyerStatus) }}
                        </div>
                    @endif

                    {{-- Quick Book slots (PHP-generated next 3 free slots) --}}
                    @php
                        $freeSlots = $lawyerSlots[$lp->user_id] ?? [];
                    @endphp
                    @if(count($freeSlots) > 0)
                    <div class="lc-quick-book">
                        <div class="lc-qb-label"><i class="fas fa-bolt"></i> Quick Book</div>
                        <div class="lc-qb-slots">
                            @foreach($freeSlots as $__fs)
                            <a class="lc-qb-chip"
                                href="{{ route('consultations.create', ['lawyer' => $lp->user_id, 'scheduled_at' => $__fs->format('Y-m-d\TH:i'), 'return_to' => url()->full()]) }}">
                                {{ $__fs->isToday() ? 'Today' : $__fs->format('M j') }}, {{ $__fs->format('g:i A') }}
                            </a>
                            @endforeach
                            <a class="lc-qb-more"
                                href="{{ route('consultations.create', ['lawyer' => $lp->user_id, 'return_to' => url()->full()]) }}">
                                All slots <i class="fas fa-angle-right"></i>
                            </a>
                        </div>
                    </div>
                    @endif

                    {{-- Actions --}}
                    <div class="lc-actions">
                        @if($lawyerStatus === 'active')
                        <a class="lc-book-btn"
                            href="{{ route('consultations.create', ['lawyer' => $lp->user_id, 'return_to' => url()->full()]) }}">
                            <i class="fas fa-video"></i> Book
                        </a>
                        @else
                        <button class="lc-book-btn disabled" disabled>
                            <i class="fas fa-clock"></i> Unavailable
                        </button>
                        @endif
                        <a href="{{ route('lawyer.public-profile', $lp->user_id) }}" class="lc-icon-btn" title="View Profile">
                            <i class="fas fa-user"></i>
                        </a>
                        <form method="POST" action="{{ route('messages.start') }}" style="display:inline;">
                            @csrf
                            <input type="hidden" name="lawyer_id" value="{{ $lp->user_id }}">
                            <button type="submit" class="lc-icon-btn" title="Send Message">
                                <i class="fas fa-comment"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="fl-empty">
                <i class="fas fa-search"></i>
                <h3>No lawyers found</h3>
                <p>Try adjusting your filters</p>
                <a href="{{ route('find-lawyers') }}" class="fl-empty-link">Clear all filters</a>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($lawyers->hasPages())
        <div class="fl-pagination-wrap">
            {{ $lawyers->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Schedule Modal (Calendar View) --}}
<div id="scheduleModal" class="fl-modal-overlay" style="display:none;">
    <div class="fl-modal-box sched-modal-box">
        <div class="fl-modal-header">
            <div>
                <h2 class="fl-modal-title"><i class="fas fa-calendar-alt" style="color:#1e2d4d;margin-right:8px;"></i>Availability Schedule</h2>
                <p id="schedLawyerName" class="fl-modal-sub"></p>
            </div>
            <button onclick="closeScheduleModal()" class="fl-modal-close"><i class="fas fa-times"></i></button>
        </div>

        {{-- Calendar --}}
        <div class="sc-cal-nav">
            <button type="button" class="sc-cal-nav-btn" onclick="_scChangeMonth(-1)"><i class="fas fa-chevron-left"></i></button>
            <span class="sc-cal-month" id="scCalMonth"></span>
            <button type="button" class="sc-cal-nav-btn" onclick="_scChangeMonth(1)"><i class="fas fa-chevron-right"></i></button>
        </div>
        <div class="sc-cal-grid">
            <div class="sc-cal-head">Sun</div><div class="sc-cal-head">Mon</div><div class="sc-cal-head">Tue</div>
            <div class="sc-cal-head">Wed</div><div class="sc-cal-head">Thu</div><div class="sc-cal-head">Fri</div><div class="sc-cal-head">Sat</div>
        </div>
        <div class="sc-cal-grid" id="scCalDays"></div>

        {{-- Time slots for selected day --}}
        <div id="scSlotsPanel" class="sc-slots-panel" style="display:none;">
            <div class="sc-slots-title">
                <i class="fas fa-clock"></i>
                <span id="scSlotsDate"></span>
            </div>
            <div class="sc-slots-grid" id="scSlotsGrid"></div>
        </div>

        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:16px;border-top:1px solid #f0f0f0;padding-top:14px;gap:16px;">
            <div style="font-size:.78rem;color:#888;display:flex;gap:16px;flex-wrap:wrap;align-items:center;">
                <span style="display:inline-flex;align-items:center;gap:5px;"><span style="width:10px;height:10px;border-radius:3px;background:#f0fdf4;border:1px solid #86efac;flex-shrink:0;"></span>Available</span>
                <span style="display:inline-flex;align-items:center;gap:5px;"><span style="width:10px;height:10px;border-radius:3px;background:#fce4e4;border:1px solid #f5c2c7;flex-shrink:0;"></span>Blocked</span>
                <span style="display:inline-flex;align-items:center;gap:5px;"><span style="width:10px;height:10px;border-radius:50%;background:#f59e0b;flex-shrink:0;"></span>Has bookings</span>
                <span style="display:inline-flex;align-items:center;gap:5px;"><span style="width:10px;height:10px;border-radius:3px;background:#e9ecef;flex-shrink:0;"></span>Past</span>
            </div>
            <button onclick="closeScheduleModal()" class="fl-btn-cancel" style="margin:0;flex-shrink:0;">Close</button>
        </div>
    </div>
</div>

{{-- Booking Modal --}}
<div id="bookingModal" class="fl-modal-overlay" style="display:none;">
    <div class="fl-modal-box">
        <div class="fl-modal-header">
            <div>
                <h2 class="fl-modal-title">Book Consultation</h2>
                <p id="bookLawyerName" class="fl-modal-sub"></p>
            </div>
            <button onclick="closeBooking()" class="fl-modal-close"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" action="{{ route('consultations.book') }}" enctype="multipart/form-data" id="bookingForm">
            @csrf
            <input type="hidden" name="lawyer_id" id="bookLawyerId">
            <input type="hidden" name="payment_method" id="bookPaymentMethod" value="all">
            <div class="fl-form-grid">
                <div class="fl-form-group fl-span2">
                    <label class="fl-form-label">Date &amp; Time</label>
                    <input type="datetime-local" name="scheduled_at" required class="fl-form-input">
                </div>
                <div class="fl-form-group">
                    <label class="fl-form-label">Duration</label>
                    <select name="duration" class="fl-form-input">
                        <option value="30">30 minutes</option>
                        <option value="60" selected>1 hour</option>
                        <option value="90">1.5 hours</option>
                        <option value="120">2 hours</option>
                    </select>
                </div>
                <div class="fl-form-group">
                    <label class="fl-form-label">Type</label>
                    <select name="type" class="fl-form-input">
                        <option value="video">📲 Video Call</option>
                        <option value="in-person">🤝 In-Person</option>
                    </select>
                </div>
                <div class="fl-form-group fl-span2">
                    <label class="fl-form-label">Notes <span style="font-weight:400;color:#999;">(optional)</span></label>
                    <textarea name="notes" rows="3" class="fl-form-input" placeholder="Brief description of your legal matter..."></textarea>
                </div>
                <div class="fl-form-group fl-span2">
                    <label class="fl-form-label">Supporting Documents <span style="font-weight:400;color:#999;">(optional)</span></label>
                    <p style="font-size:.78rem;color:#9ca3af;margin:0 0 8px;">Attach any relevant files — contracts, receipts, IDs, court papers, etc.</p>
                    <label id="flDocLabel" style="display:flex;align-items:center;gap:8px;padding:12px 14px;border:2px dashed #b5860d;border-radius:8px;cursor:pointer;font-size:.85rem;font-weight:600;color:#b5860d;background:#fdfaf3;">
                        <i class="fas fa-paperclip"></i>
                        <span id="flDocName">Click to attach a file</span>
                        <input type="file" name="case_document" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                               style="display:none;" onchange="handleFlDoc(this)">
                    </label>
                    <p style="font-size:.72rem;color:#ccc;margin:4px 0 0;">JPG, PNG, PDF, DOC, DOCX · max 10 MB</p>
                </div>
            </div>
            {{-- Cost summary --}}
            <div id="bookCostSummary" style="background:#f0f4ff;border:1px solid #c7d7f5;border-radius:8px;padding:12px 16px;margin-top:12px;font-size:0.9rem;display:none;">
                <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                    <span style="color:#555;">Estimated total</span>
                    <span id="bookTotal" style="font-weight:600;">₱0.00</span>
                </div>
                <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                    <span style="color:#555;">Downpayment due now <small style="color:#888;">(50%)</small></span>
                    <span id="bookDownpayment" style="font-weight:700;color:#1e2d4d;">₱0.00</span>
                </div>
                <div style="display:flex;justify-content:space-between;">
                    <span style="color:#555;">Balance due after session <small style="color:#888;">(50%)</small></span>
                    <span id="bookBalance" style="color:#666;">₱0.00</span>
                </div>
            </div>
            <div class="fl-modal-footer">
                <button type="button" onclick="closeBooking()" class="fl-btn-cancel">Cancel</button>
                <button type="button" onclick="openPaymentMethodModal()" class="fl-btn-confirm"><i class="fas fa-credit-card"></i> Confirm Booking</button>
            </div>
        </form>
    </div>
</div>

{{-- Payment Method Modal (PayMongo only) --}}
<div id="paymentMethodModal" class="pm-overlay" style="display:none;" onclick="handlePmOverlayClick(event)">
    <div class="pm-box">
        <div class="pm-header">
            <h3><i class="fas fa-shield-alt" style="margin-right:7px;color:#7c9ddc;"></i>Secure Payment</h3>
            <button class="pm-header-close" onclick="closePaymentMethodModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="pm-summary">
            <div>
                <div class="pm-summary-label">Downpayment due now (50%)</div>
                <div class="pm-summary-amount" id="pmDownpaymentAmt">₱0.00</div>
            </div>
        </div>
        <div class="pm-body" style="text-align:center;">
            <div class="pm-body-title">You will be redirected to a secure PayMongo checkout page to complete your payment.</div>
        </div>
        <div class="pm-footer">
            <button type="button" class="pm-btn-back" onclick="closePaymentMethodModal()"><i class="fas fa-arrow-left" style="margin-right:5px;"></i>Back</button>
            <button type="button" class="pm-btn-pay" id="pmPayBtn" onclick="submitBookingWithMethod()">
                <i class="fas fa-lock"></i> Proceed to Secure Payment
            </button>
        </div>
    </div>
</div>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fallback for _updateBookCost if not defined
    if (typeof window._updateBookCost !== 'function') {
        window._updateBookCost = function() {};
    }
    // Helper to inject lawyer payment info into modal

    let _bookRate = 0;
    window.openBooking = function(id, name, rate) {
        _bookRate = parseFloat(rate) || 0;
        document.getElementById('bookLawyerId').value = id;
        document.getElementById('bookLawyerName').textContent = name + ' — ₱' + Number(rate).toLocaleString() + '/hr';
        document.getElementById('bookingModal').style.display = 'flex';
        window._updateBookCost();
    };

    window.handleBookClick = function(btn) {
        const id = btn.dataset.lawyerId;
        const name = btn.dataset.lawyerName;
        const rate = btn.dataset.hourlyRate;
        window.openBooking(id, name, rate);
    };

    window.handleQuickBook = function(btn) {
        const id = btn.dataset.lawyerId;
        const name = btn.dataset.lawyerName;
        const rate = btn.dataset.hourlyRate;
        const time = btn.dataset.time;
        window.openBookingWithTime(id, name, rate, time);
    };

    window.openBookingWithTime = function(id, name, rate, isoTime) {
        window.openBooking(id, name, rate);
        // Ensure the time string is in the correct format for datetime-local (YYYY-MM-DDTHH:MM)
        let formatted = isoTime;
        if (isoTime.length > 16) formatted = isoTime.substring(0, 16);
        if (formatted.length === 13) formatted += ':00';
        // Fallback: force modal display and input update
        setTimeout(function() {
            const input = document.querySelector('#bookingModal input[type="datetime-local"]');
            if (input) {
                input.value = formatted;
                input.dispatchEvent(new Event('input'));
            }
            document.getElementById('bookingModal').style.display = 'flex';
        }, 100);
    };
    window.closeBooking = function() {
        document.getElementById('bookingModal').style.display = 'none';
        // reset doc upload label
        document.getElementById('flDocName').textContent = 'Click to attach a file';
        var label = document.getElementById('flDocLabel');
        label.style.borderColor = '#b5860d';
        label.style.color = '#b5860d';
        label.style.background = '#fdfaf3';
    };

    window._updateBookCost = function() {
        const dur = parseInt(document.querySelector('#bookingModal select[name="duration"]').value) || 60;
        const total = (dur / 60) * _bookRate;
        const down = total * 0.5;
        const bal = total * 0.5;

        document.getElementById('bookTotal').textContent = '₱' + total.toLocaleString(undefined, {minimumFractionDigits:2});
        document.getElementById('bookDownpayment').textContent = '₱' + down.toLocaleString(undefined, {minimumFractionDigits:2});
        document.getElementById('bookBalance').textContent = '₱' + bal.toLocaleString(undefined, {minimumFractionDigits:2});
        document.getElementById('bookCostSummary').style.display = 'block';
    };
    window.handleFlDoc = function(input) {
        if (input.files && input.files[0]) {
            document.getElementById('flDocName').textContent = '✓ ' + input.files[0].name;
            var label = document.getElementById('flDocLabel');
            label.style.borderColor = '#28a745';
            label.style.color = '#28a745';
            label.style.background = '#f0fff4';
        }
    };
    document.getElementById('bookingModal').addEventListener('click', function(e) {
        if (e.target === this) closeBooking();
    });

    /* ── Payment Method Picker ───────────────────────────────── */
    // No longer needed: _selectedPaymentMethod, _lawyerPaymentData

    window.openPaymentMethodModal = function() {
        // Basic client-side validation before opening picker
        const form = document.getElementById('bookingForm');
        const scheduledAt = form.querySelector('input[name="scheduled_at"]').value;
        if (!scheduledAt) {
            alert('Please select a date & time for your consultation.');
            return;
        }
        const downText = document.getElementById('bookDownpayment').textContent;
        document.getElementById('pmDownpaymentAmt').textContent = downText;
        document.getElementById('pmPayBtn').disabled = false; // Always enabled for PayMongo only
        document.getElementById('paymentMethodModal').style.display = 'flex';
    };

    window.closePaymentMethodModal = function() {
        document.getElementById('paymentMethodModal').style.display = 'none';
    };

    window.handlePmOverlayClick = function(e) {
        if (e.target === document.getElementById('paymentMethodModal')) {
            closePaymentMethodModal();
        }
    };

    window.selectPaymentMethod = function(btn) {
        document.querySelectorAll('.pm-method').forEach(b => b.classList.remove('selected'));
        btn.classList.add('selected');
        _selectedPaymentMethod = btn.dataset.method;
        validatePmSubmission(); // Use centralized validation instead of just enabling
        let instructions = '';
        if (_selectedPaymentMethod === 'gcash') {
            if (Object.keys(_lawyerPaymentData).length === 0) {
                instructions = '<div style="color:#1e2d4d;font-weight:600;"><i class="fas fa-spinner fa-spin"></i> Fetching lawyer payment info...</div>';
            } else if (_lawyerPaymentData.gcash_number) {
                instructions = 'Send your payment to <b>' + _lawyerPaymentData.gcash_number + '</b> via GCash. After payment, upload your screenshot or enter the reference number.';
                if (_lawyerPaymentData.gcash_qr_url) {
                    document.getElementById('pmQRSection').style.display = 'block';
                } else {
                    document.getElementById('pmQRSection').style.display = 'none';
                }
            } else {
                instructions = '<div style="color:#dc3545;font-weight:600;"><i class="fas fa-exclamation-triangle"></i> This lawyer has not set up their GCash details yet. Please contact them via messages or choose another method.</div>';
                document.getElementById('pmQRSection').style.display = 'none';
            }
        } else if (_selectedPaymentMethod === 'paypal') {
            instructions = 'Send your payment via <a href="https://paypal.me/yourusername" target="_blank">PayPal.Me/yourusername</a>. After payment, upload your screenshot or enter the reference number.';
            document.getElementById('pmQRSection').style.display = 'none';
        }
        document.getElementById('pmInstructions').innerHTML = instructions;
        document.getElementById('pmInstructions').style.display = 'block';
        document.getElementById('pmProofSection').style.display = (_selectedPaymentMethod === 'gcash' && !_lawyerPaymentData.gcash_number) ? 'none' : 'block';
    };

    window.validatePmSubmission = function() {
        const btn = document.getElementById('pmPayBtn');
        if (!_selectedPaymentMethod) {
            btn.disabled = true;
            return;
        }

        // If GCash and lawyer hasn't set up info, always disable
        if (_selectedPaymentMethod === 'gcash' && (!_lawyerPaymentData || !_lawyerPaymentData.gcash_number)) {
            btn.disabled = true;
            return;
        }

        // Must have either a file OR a reference number
        const hasFile = document.getElementById('pmFile').files.length > 0;
        const hasRef = document.getElementById('pmRef').value.trim().length > 0;

        btn.disabled = !(hasFile || hasRef);
    };

    window.submitBookingWithMethod = function() {
        document.getElementById('bookPaymentMethod').value = 'all'; // Always use PayMongo
        document.getElementById('bookingForm').submit();
    };
    document.querySelector('#bookingModal select[name="duration"]').addEventListener('change', _updateBookCost);

    /* ── Schedule Modal ──────────────────────────────────────── */
    let _sched = {};

    window.openScheduleModalFromBtn = function(btn) {
        const id   = btn.dataset.lawyerId;
        const name = btn.dataset.lawyerName;
        const rate = btn.dataset.hourlyRate;
        const booked = JSON.parse(btn.dataset.consultations);
        const blocked = btn.dataset.blocked ? JSON.parse(btn.dataset.blocked) : [];
        window.openScheduleModal(id, name, rate, booked, blocked);
    };

    let _scYear, _scMonth, _scSelectedDate = null;

    window.openScheduleModal = function(id, name, rate, booked, blocked) {
        _sched = { id, name, rate, booked, blocked: blocked || [] };
        document.getElementById('schedLawyerName').textContent = name + ' · ₱' + Number(rate).toLocaleString() + '/hr';
        const now = new Date();
        _scYear = now.getFullYear();
        _scMonth = now.getMonth();
        _scSelectedDate = null;
        document.getElementById('scSlotsPanel').style.display = 'none';
        _scRenderCal();
        document.getElementById('scheduleModal').style.display = 'flex';
    };
    window.closeScheduleModal = function() {
        document.getElementById('scheduleModal').style.display = 'none';
    };
    document.getElementById('scheduleModal').addEventListener('click', function(e) {
        if (e.target === this) closeScheduleModal();
    });

    window._scChangeMonth = function(dir) {
        _scMonth += dir;
        if (_scMonth > 11) { _scMonth = 0; _scYear++; }
        if (_scMonth < 0)  { _scMonth = 11; _scYear--; }
        _scSelectedDate = null;
        document.getElementById('scSlotsPanel').style.display = 'none';
        _scRenderCal();
    };

    function _scRenderCal() {
        const MONTHS = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        document.getElementById('scCalMonth').textContent = MONTHS[_scMonth] + ' ' + _scYear;

        const grid = document.getElementById('scCalDays');
        grid.innerHTML = '';

        const first = new Date(_scYear, _scMonth, 1);
        const last  = new Date(_scYear, _scMonth + 1, 0);
        const today = new Date(); today.setHours(0,0,0,0);
        const pad   = n => String(n).padStart(2, '0');

        const blocked = (_sched.blocked || []).map(function(blockedDate) {
            const range = {
                date: blockedDate.date,
                isAllDay: !!blockedDate.is_all_day,
                label: blockedDate.label,
                reason: blockedDate.reason || '',
            };

            if (!range.isAllDay) {
                range.s = new Date(blockedDate.date + 'T' + blockedDate.start_time + ':00');
                range.e = new Date(blockedDate.date + 'T' + blockedDate.end_time + ':00');
            }

            return range;
        });
        const booked  = (_sched.booked || []).map(b => ({ s: new Date(b.start), e: new Date(b.end) }));

        // Leading empty cells
        for (let i = 0; i < first.getDay(); i++) {
            const e = document.createElement('div');
            e.className = 'sc-cal-cell empty';
            grid.appendChild(e);
        }

        for (let d = 1; d <= last.getDate(); d++) {
            const cell = document.createElement('div');
            const dateObj = new Date(_scYear, _scMonth, d); dateObj.setHours(0,0,0,0);
            const dateStr = _scYear + '-' + pad(_scMonth+1) + '-' + pad(d);
            const isPast    = dateObj < today;
            const dayBlocked = blocked.filter(b => b.date === dateStr);
            const isBlocked = dayBlocked.some(b => b.isAllDay);
            const hasPartialBlock = dayBlocked.length > 0 && !isBlocked;
            const isToday   = dateObj.getTime() === today.getTime();
            const isSelected = _scSelectedDate === dateStr;

            // Check if any bookings on this day
            const dayStart = new Date(dateObj);
            const dayEnd   = new Date(dateObj); dayEnd.setDate(dayEnd.getDate() + 1);
            const hasBookings = booked.some(b => b.s < dayEnd && b.e > dayStart);

            cell.className = 'sc-cal-cell';
            if (isPast) cell.classList.add('past');
            if (isBlocked) cell.classList.add('blocked');
            if (hasPartialBlock) cell.classList.add('has-bookings');
            if (isToday) cell.classList.add('today');
            if (isSelected) cell.classList.add('selected');
            if (hasBookings && !isPast && !isBlocked) cell.classList.add('has-bookings');
            cell.textContent = d;

            if (isBlocked) {
                cell.title = 'Lawyer unavailable';
            } else if (hasPartialBlock) {
                cell.title = dayBlocked.map(function(item) {
                    return item.label + (item.reason ? ' - ' + item.reason : '');
                }).join('\n');
            } else if (!isPast) {
                cell.onclick = (function(ds, dObj) {
                    return function() {
                        _scSelectedDate = ds;
                        _scRenderCal();
                        _scShowSlots(ds, dObj);
                    };
                })(dateStr, new Date(dateObj));
            }

            grid.appendChild(cell);
        }
    }

    function _scShowSlots(dateStr, dateObj) {
        const panel = document.getElementById('scSlotsPanel');
        const grid  = document.getElementById('scSlotsGrid');
        const title = document.getElementById('scSlotsDate');

        const DAYS = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        const MONTHS = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        title.textContent = DAYS[dateObj.getDay()] + ', ' + MONTHS[dateObj.getMonth()] + ' ' + dateObj.getDate();

        const blocked = (_sched.blocked || []).map(function(blockedDate) {
            const range = {
                date: blockedDate.date,
                isAllDay: !!blockedDate.is_all_day,
            };

            if (!range.isAllDay) {
                range.s = new Date(blockedDate.date + 'T' + blockedDate.start_time + ':00');
                range.e = new Date(blockedDate.date + 'T' + blockedDate.end_time + ':00');
            }

            return range;
        }).filter(b => b.date === dateStr);

        if (blocked.some(b => b.isAllDay)) {
            grid.innerHTML = '<div style="width:100%;text-align:center;padding:18px;color:#dc3545;font-size:.88rem;"><i class="fas fa-ban"></i> Lawyer unavailable on this date</div>';
            panel.style.display = 'block';
            return;
        }

        const HOURS  = [9,10,11,12,13,14,15,16,17];
        const now    = new Date();
        const booked = (_sched.booked || []).map(b => ({ s: new Date(b.start), e: new Date(b.end) }));
        let html = '';

        for (const hr of HOURS) {
            const slotS = new Date(dateObj); slotS.setHours(hr, 0, 0, 0);
            const slotE = new Date(dateObj); slotE.setHours(hr + 1, 0, 0, 0);
            const h12 = hr % 12 || 12;
            const ap  = hr < 12 ? 'AM' : 'PM';
            const label = h12 + ':00 ' + ap;
            const iso = _toISO(slotS);

            if (booked.some(b => slotS < b.e && slotE > b.s) || blocked.some(b => !b.isAllDay && slotS < b.e && slotE > b.s)) {
                html += `<div class="sc-slot booked" title="Unavailable">${label}</div>`;
            } else {
                html += `<div class="sc-slot available" onclick="_scBookSlot('${iso}')">${label}</div>`;
            }
        }

        grid.innerHTML = html;
        panel.style.display = 'block';
        panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    window._scBookSlot = function(iso) {
        closeScheduleModal();
        openBookingWithTime(_sched.id, _sched.name, _sched.rate, iso);
    };
    window._fmtHr = function(hr) {
        const h = hr % 12 || 12, ap = hr < 12 ? 'AM' : 'PM';
        return h + ':00 ' + ap;
    };
    window._toISO = function(d) {
        const p = n => String(n).padStart(2,'0');
        return `${d.getFullYear()}-${p(d.getMonth()+1)}-${p(d.getDate())}T${p(d.getHours())}:${p(d.getMinutes())}`;
    };

    /* ── Misc ────────────────────────────────────────────────── */
    const validationData = document.getElementById('booking-validation-data');
    if (validationData) {
        const hasErrors = validationData.dataset.hasErrors === 'true';
        const oldLawyerId = validationData.dataset.oldLawyerId;
        
        if (hasErrors && oldLawyerId) {
            // If we have errors, the server usually provides the lawyer_id in old input
            // We can use this to find the lawyer card and trigger re-opening if needed
        }
    }

    document.querySelectorAll('.fl-rating-pill').forEach(pill => {
        pill.addEventListener('click', () => {
            document.querySelectorAll('.fl-rating-pill').forEach(p => p.classList.remove('active'));
            pill.classList.add('active');
        });
    });
    document.querySelectorAll('.fl-view-btn').forEach((btn, idx) => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.fl-view-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const grid = document.getElementById('lawyersGrid');
            grid.className = idx === 0 ? 'fl-cards-grid' : 'fl-cards-list';
        });
    });
});
</script>
@endpush
@endsection
