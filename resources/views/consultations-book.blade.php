@extends('layouts.app')
@section('title', 'Book Consultation')

@push('styles')
<style>
.cb-shell { display:grid; grid-template-columns: 340px minmax(0, 1fr); gap:24px; align-items:start; }
.cb-back { display:inline-flex; align-items:center; gap:8px; margin-bottom:18px; color:#1e2d4d; text-decoration:none; font-weight:700; }
.cb-back:hover { color:#2563eb; }
.cb-panel { background:#fff; border:1px solid #e7edf3; border-radius:18px; box-shadow:0 10px 30px rgba(30,45,77,.06); }
.cb-side { padding:22px; position:sticky; top:88px; }
.cb-main { padding:24px; }
.cb-lawyer-top { display:flex; gap:14px; align-items:center; }
.cb-avatar { width:68px; height:68px; border-radius:18px; overflow:hidden; background:#1e2d4d; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.5rem; font-weight:800; flex-shrink:0; }
.cb-avatar img { width:100%; height:100%; object-fit:cover; }
.cb-name { margin:0; font-size:1.3rem; font-weight:800; color:#1e2d4d; }
.cb-meta { margin:4px 0 0; color:#6b7280; font-size:.9rem; }
.cb-badges { display:flex; flex-wrap:wrap; gap:8px; margin-top:14px; }
.cb-badge { display:inline-flex; align-items:center; gap:6px; padding:6px 11px; border-radius:999px; font-size:.78rem; font-weight:700; }
.cb-badge.status-available { background:#ecfdf3; color:#15803d; }
.cb-badge.status-busy { background:#fff7ed; color:#c2410c; }
.cb-badge.status-offline { background:#f3f4f6; color:#6b7280; }
.cb-badge.cert { background:#eef2ff; color:#1e3a8a; }
.cb-stats { display:grid; grid-template-columns:repeat(2,1fr); gap:12px; margin-top:18px; }
.cb-stat { padding:14px; border-radius:14px; background:#f8fafc; border:1px solid #edf2f7; }
.cb-stat-label { font-size:.72rem; text-transform:uppercase; letter-spacing:.06em; color:#94a3b8; font-weight:700; }
.cb-stat-value { margin-top:6px; color:#1e2d4d; font-size:1.1rem; font-weight:800; }
.cb-help { margin-top:18px; padding:14px; border-radius:14px; background:#fffaf0; border:1px solid #f6d38b; color:#8a6116; font-size:.86rem; line-height:1.55; }
.cb-header { display:flex; justify-content:space-between; gap:16px; align-items:flex-start; margin-bottom:20px; }
.cb-title { margin:0; font-size:1.6rem; font-weight:800; color:#1e2d4d; }
.cb-sub { margin:6px 0 0; color:#6b7280; font-size:.94rem; }
.cb-alert { border-radius:12px; padding:12px 14px; margin-bottom:16px; font-size:.9rem; }
.cb-alert.error { background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; }
.cb-alert.success { background:#ecfdf3; border:1px solid #bbf7d0; color:#166534; }
.cb-section { margin-top:24px; }
.cb-section:first-child { margin-top:0; }
.cb-section-title { margin:0 0 12px; font-size:1rem; font-weight:800; color:#1e2d4d; }
.cb-quick-grid { display:flex; flex-wrap:wrap; gap:8px; }
.cb-quick-btn { border:1.5px solid #bfdbfe; background:#eff6ff; color:#1d4ed8; border-radius:10px; padding:8px 12px; font:inherit; font-size:.83rem; font-weight:700; cursor:pointer; transition:.15s; }
.cb-quick-btn:hover, .cb-quick-btn.active { background:#1e3a8a; border-color:#1e3a8a; color:#fff; }
.cb-cal-nav { display:flex; align-items:center; justify-content:center; gap:16px; margin-bottom:14px; }
.cb-cal-nav-btn { width:34px; height:34px; border-radius:10px; border:1px solid #d1d5db; background:#fff; color:#1e2d4d; cursor:pointer; }
.cb-cal-month { min-width:170px; text-align:center; font-size:1rem; font-weight:800; color:#1e2d4d; }
.cb-cal-grid { display:grid; grid-template-columns:repeat(7,1fr); gap:5px; }
.cb-cal-head { text-align:center; padding:4px 0; font-size:.72rem; font-weight:700; color:#94a3b8; text-transform:uppercase; }
.cb-cal-cell { min-height:48px; border-radius:12px; border:1.5px solid transparent; background:#fff; color:#1e2d4d; display:flex; align-items:center; justify-content:center; font-size:.88rem; font-weight:700; cursor:pointer; position:relative; transition:.15s; }
.cb-cal-cell:hover:not(.past):not(.blocked):not(.empty) { background:#eef2ff; border-color:#a5b4fc; }
.cb-cal-cell.today { border-color:#1e2d4d; }
.cb-cal-cell.selected { background:#1e2d4d; color:#fff; border-color:#1e2d4d; }
.cb-cal-cell.blocked { background:#fef2f2; border-color:#fecaca; color:#dc2626; cursor:not-allowed; }
.cb-cal-cell.past { background:#f8fafc; color:#cbd5e1; cursor:default; }
.cb-cal-cell.has-bookings::after { content:''; position:absolute; bottom:6px; left:50%; transform:translateX(-50%); width:6px; height:6px; border-radius:999px; background:#f59e0b; }
.cb-cal-cell.selected::after { background:#fff; }
.cb-cal-cell.empty { visibility:hidden; }
.cb-slots { margin-top:16px; padding-top:16px; border-top:1px solid #edf2f7; }
.cb-slots-grid { display:flex; flex-wrap:wrap; gap:8px; }
.cb-slot { border-radius:10px; padding:8px 12px; font-size:.82rem; font-weight:700; border:1.5px solid; }
.cb-slot.available { background:#ecfdf3; border-color:#86efac; color:#15803d; cursor:pointer; }
.cb-slot.available:hover, .cb-slot.available.active { background:#166534; border-color:#166534; color:#fff; }
.cb-slot.booked { background:#fff7ed; border-color:#fdba74; color:#c2410c; }
.cb-slot.blocked { background:#fff5f5; border-color:#fca5a5; color:#dc2626; }
.cb-slot.past { background:#f8fafc; border-color:#e5e7eb; color:#9ca3af; }
.cb-legend { display:flex; flex-wrap:wrap; gap:14px; margin-top:14px; color:#6b7280; font-size:.78rem; }
.cb-legend span { display:inline-flex; align-items:center; gap:6px; }
.cb-legend i { width:10px; height:10px; border-radius:3px; display:inline-block; }
.cb-legend .free { background:#ecfdf3; border:1px solid #86efac; }
.cb-legend .busy { background:#fef2f2; border:1px solid #fecaca; }
.cb-legend .taken { background:#fff7ed; border:1px solid #fdba74; }
.cb-legend .booked { background:#f59e0b; border-radius:999px; }
.cb-form-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:16px; }
.cb-field.full { grid-column:1 / -1; }
.cb-label { display:block; margin-bottom:7px; font-size:.84rem; font-weight:800; color:#1e2d4d; }
.cb-label span { color:#94a3b8; font-weight:500; }
.cb-input, .cb-textarea, .cb-select { width:100%; border:1.5px solid #d7dee7; border-radius:12px; padding:12px 14px; font:inherit; font-size:.95rem; box-sizing:border-box; background:#fff; }
.cb-input:focus, .cb-textarea:focus, .cb-select:focus { outline:none; border-color:#1e3a8a; box-shadow:0 0 0 4px rgba(30,58,138,.08); }
.cb-textarea { min-height:110px; resize:vertical; }
.cb-upload { display:flex; align-items:center; gap:10px; border:2px dashed #d4a018; color:#b7791f; background:#fffaf0; border-radius:14px; padding:14px; cursor:pointer; font-weight:700; }
.cb-upload input { display:none; }
.cb-cost { margin-top:18px; border:1px solid #c7d7f5; background:#f0f4ff; border-radius:16px; padding:16px; }
.cb-cost-row { display:flex; justify-content:space-between; gap:10px; font-size:.92rem; color:#475569; margin-top:6px; }
.cb-cost-row strong { color:#1e2d4d; }
.cb-actions { margin-top:18px; display:flex; gap:12px; }
.cb-btn-secondary, .cb-btn-primary { border-radius:12px; padding:13px 16px; font:inherit; font-weight:800; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; justify-content:center; }
.cb-btn-secondary { border:1.5px solid #d1d5db; color:#475569; background:#fff; flex:1; }
.cb-btn-primary { border:none; background:#1e2d4d; color:#fff; flex:2; }
.cb-errors { margin:0 0 16px; padding-left:18px; color:#b91c1c; font-size:.88rem; }
.cb-modal-backdrop { position:fixed; inset:0; z-index:10000; display:none; align-items:center; justify-content:center; padding:18px; background:rgba(15,23,42,.48); backdrop-filter:blur(3px); }
.cb-modal-backdrop.visible { display:flex; }
.cb-modal { width:min(420px, 100%); border-radius:16px; background:#fff; border:1px solid #e5e7eb; box-shadow:0 24px 70px rgba(15,23,42,.28); overflow:hidden; }
.cb-modal-body { padding:22px 22px 18px; }
.cb-modal-icon { width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; background:#fff7ed; color:#c2410c; margin-bottom:14px; }
.cb-modal-title { margin:0; color:#1e2d4d; font-size:1.05rem; font-weight:800; }
.cb-modal-message { margin:8px 0 0; color:#475569; line-height:1.55; font-size:.92rem; }
.cb-modal-actions { display:flex; justify-content:flex-end; padding:14px 18px 18px; }
.cb-modal-btn { border:none; border-radius:10px; padding:10px 16px; background:#1e2d4d; color:#fff; font:inherit; font-weight:800; cursor:pointer; }
@media (max-width: 980px) {
    .cb-shell { grid-template-columns:1fr; }
    .cb-side { position:static; }
}
@media (max-width: 640px) {
    .cb-main, .cb-side { padding:18px; }
    .cb-form-grid, .cb-stats { grid-template-columns:1fr; }
    .cb-actions { flex-direction:column; }
}
</style>
@endpush

@section('content')
@php
    $profileStatus = $profile->currentStatus();
    $minScheduledAt = now()->copy()->addMinute()->format('Y-m-d\TH:i');
@endphp
<a href="{{ $returnTo }}" class="cb-back"><i class="fas fa-arrow-left"></i> Back</a>

<div class="cb-shell">
    <aside class="cb-panel cb-side">
        <div class="cb-lawyer-top">
            <div class="cb-avatar">
                @if($lawyer->avatar_url)
                    <img src="{{ $lawyer->avatar_url }}" alt="{{ $lawyer->name }}">
                @else
                    {{ strtoupper(substr($lawyer->name, 0, 1)) }}
                @endif
            </div>
            <div>
                <h1 class="cb-name">{{ $lawyer->name }}</h1>
                <p class="cb-meta">{{ $profile->specialty }}{{ $profile->firm ? ' • '.$profile->firm : '' }}</p>
                <p class="cb-meta">{{ $profile->location ?: 'Location not specified' }}</p>
            </div>
        </div>

        <div class="cb-badges">
            <span class="cb-badge status-{{ $profile->currentStatusClass() }}">
                <i class="fas fa-circle" style="font-size:.45rem;"></i> {{ ucfirst($profileStatus) }}
            </span>
            @if($profile->is_certified)
                <span class="cb-badge cert"><i class="fas fa-shield-alt"></i> Certified</span>
            @endif
        </div>

        <div class="cb-stats">
            <div class="cb-stat">
                <div class="cb-stat-label">Rate</div>
                <div class="cb-stat-value">PHP {{ number_format($profile->hourly_rate, 0) }}/hr</div>
            </div>
            <div class="cb-stat">
                <div class="cb-stat-label">Experience</div>
                <div class="cb-stat-value">{{ $profile->experience_years }} yrs</div>
            </div>
            <div class="cb-stat">
                <div class="cb-stat-label">Rating</div>
                <div class="cb-stat-value">{{ number_format($profile->rating, 1) }}</div>
            </div>
            <div class="cb-stat">
                <div class="cb-stat-label">Reviews</div>
                <div class="cb-stat-value">{{ $profile->reviews_count }}</div>
            </div>
        </div>

        <div class="cb-help">
            This page books directly against the selected lawyer’s schedule. Blocked dates and already-paid consultations are checked again on submit before payment starts.
        </div>
    </aside>

    <section class="cb-panel cb-main">
        <div class="cb-header">
            <div>
                <h2 class="cb-title">Book Consultation</h2>
                <p class="cb-sub">Choose a safe available slot, review the lawyer’s calendar, and continue to secure payment.</p>
            </div>
        </div>

        @if(session('error'))
            <div class="cb-alert error">{{ session('error') }}</div>
        @endif

        @if(session('success'))
            <div class="cb-alert success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <ul class="cb-errors">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <div class="cb-section">
            <h3 class="cb-section-title">Quick Slots</h3>
            <div class="cb-quick-grid">
                @forelse($quickSlots as $slot)
                    <button
                        type="button"
                        class="cb-quick-btn"
                        data-slot="{{ $slot->format('Y-m-d\TH:i') }}"
                    >
                        {{ $slot->isToday() ? 'Today' : $slot->format('M j') }}, {{ $slot->format('g:i A') }}
                    </button>
                @empty
                    <span class="cb-sub">No quick slots found in the next two weeks.</span>
                @endforelse
            </div>
        </div>

        <div class="cb-section">
            <h3 class="cb-section-title">Availability Calendar</h3>
            <div class="cb-cal-nav">
                <button type="button" class="cb-cal-nav-btn" id="cbPrevMonth"><i class="fas fa-chevron-left"></i></button>
                <div class="cb-cal-month" id="cbCalMonth"></div>
                <button type="button" class="cb-cal-nav-btn" id="cbNextMonth"><i class="fas fa-chevron-right"></i></button>
            </div>
            <div class="cb-cal-grid">
                <div class="cb-cal-head">Sun</div>
                <div class="cb-cal-head">Mon</div>
                <div class="cb-cal-head">Tue</div>
                <div class="cb-cal-head">Wed</div>
                <div class="cb-cal-head">Thu</div>
                <div class="cb-cal-head">Fri</div>
                <div class="cb-cal-head">Sat</div>
            </div>
            <div class="cb-cal-grid" id="cbCalDays"></div>

            <div class="cb-slots" id="cbSlotsPanel" style="display:none;">
                <h4 class="cb-section-title" id="cbSlotsTitle" style="margin-bottom:10px;"></h4>
                <div class="cb-slots-grid" id="cbSlotsGrid"></div>
            </div>

            <div class="cb-legend">
                <span><i class="free"></i> Available</span>
                <span><i class="busy"></i> Blocked date</span>
                <span><i class="taken"></i> Already booked</span>
                <span><i class="booked"></i> Date has bookings</span>
            </div>
        </div>

        <div class="cb-section">
            <h3 class="cb-section-title">Consultation Details</h3>
            <form method="POST" action="{{ route('consultations.book') }}" enctype="multipart/form-data" id="cbBookingForm">
                @csrf
                <input type="hidden" name="lawyer_id" value="{{ $lawyer->id }}">

                <div class="cb-form-grid">
                    <div class="cb-field full">
                        <label class="cb-label" for="cbScheduledAt">Date & Time</label>
                        <input
                            id="cbScheduledAt"
                            class="cb-input"
                            type="datetime-local"
                            name="scheduled_at"
                            value="{{ $selectedAt }}"
                            min="{{ $minScheduledAt }}"
                            required
                        >
                    </div>

                    <div class="cb-field">
                        <label class="cb-label" for="cbDuration">Duration</label>
                        <select id="cbDuration" class="cb-select" name="duration">
                            <option value="30" {{ old('duration', '60') == '30' ? 'selected' : '' }}>30 minutes</option>
                            <option value="60" {{ old('duration', '60') == '60' ? 'selected' : '' }}>1 hour</option>
                            <option value="90" {{ old('duration', '60') == '90' ? 'selected' : '' }}>1.5 hours</option>
                            <option value="120" {{ old('duration', '60') == '120' ? 'selected' : '' }}>2 hours</option>
                        </select>
                    </div>

                    <div class="cb-field">
                        <label class="cb-label" for="cbType">Type</label>
                        <select id="cbType" class="cb-select" name="type">
                            <option value="video" {{ old('type', 'video') == 'video' ? 'selected' : '' }}>Video Call</option>
                            <option value="phone" {{ old('type') == 'phone' ? 'selected' : '' }}>Phone Call</option>
                            <option value="in-person" {{ old('type') == 'in-person' ? 'selected' : '' }}>In-Person</option>
                        </select>
                    </div>

                    <div class="cb-field full">
                        <label class="cb-label" for="cbNotes">Notes <span>optional</span></label>
                        <textarea id="cbNotes" class="cb-textarea" name="notes" placeholder="Briefly describe your legal concern so the lawyer can prepare.">{{ old('notes') }}</textarea>
                    </div>

                    <div class="cb-field full">
                        <label class="cb-label">Supporting Documents <span>optional</span></label>
                        <label class="cb-upload" id="cbUploadLabel">
                            <i class="fas fa-paperclip"></i>
                            <span id="cbUploadText">Click to attach JPG, PNG, PDF, DOC, or DOCX files</span>
                            <input type="file" name="case_document" id="cbDocumentInput" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                        </label>
                    </div>
                </div>

                <div class="cb-cost">
                    <div class="cb-cost-row">
                        <span>Estimated total</span>
                        <strong id="cbTotal">PHP 0.00</strong>
                    </div>
                    <div class="cb-cost-row">
                        <span>Downpayment due now (50%)</span>
                        <strong id="cbDownpayment">PHP 0.00</strong>
                    </div>
                    <div class="cb-cost-row">
                        <span>Balance due after session (50%)</span>
                        <span id="cbBalance">PHP 0.00</span>
                    </div>
                </div>

                <div class="cb-actions">
                    <button type="button" class="cb-btn-secondary" style="width:100%;" onclick="document.getElementById('askMsgForm').submit();">Ask Via Messages</button>
                    <button type="submit" class="cb-btn-primary"><i class="fas fa-lock" style="margin-right:8px;"></i>Continue to Secure Payment</button>
                </div>
            </form>

            <form method="POST" action="{{ route('messages.start') }}" id="askMsgForm" style="display:none;">
                @csrf
                <input type="hidden" name="lawyer_id" value="{{ $lawyer->id }}">
            </form>
        </div>
    </section>
</div>

<div class="cb-modal-backdrop" id="cbBookingModal" role="dialog" aria-modal="true" aria-labelledby="cbBookingModalTitle" aria-hidden="true">
    <div class="cb-modal">
        <div class="cb-modal-body">
            <div class="cb-modal-icon"><i class="fas fa-calendar-xmark"></i></div>
            <h3 class="cb-modal-title" id="cbBookingModalTitle">Schedule unavailable</h3>
            <p class="cb-modal-message" id="cbBookingModalMessage">Please choose an upcoming consultation time.</p>
        </div>
        <div class="cb-modal-actions">
            <button type="button" class="cb-modal-btn" id="cbBookingModalOk">OK</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const hourlyRate = {{ (float) $profile->hourly_rate }};
    const blocked = @json($blockedSchedule ?? []);
    const booked = @json($bookedSlots);
    const selectedInput = document.getElementById('cbScheduledAt');
    const durationSelect = document.getElementById('cbDuration');
    const uploadInput = document.getElementById('cbDocumentInput');
    const uploadText = document.getElementById('cbUploadText');
    const quickButtons = Array.from(document.querySelectorAll('.cb-quick-btn'));
    const totalEl = document.getElementById('cbTotal');
    const downpaymentEl = document.getElementById('cbDownpayment');
    const balanceEl = document.getElementById('cbBalance');
    const slotsPanel = document.getElementById('cbSlotsPanel');
    const slotsGrid = document.getElementById('cbSlotsGrid');
    const slotsTitle = document.getElementById('cbSlotsTitle');
    const daysGrid = document.getElementById('cbCalDays');
    const monthLabel = document.getElementById('cbCalMonth');
    const prevMonthBtn = document.getElementById('cbPrevMonth');
    const nextMonthBtn = document.getElementById('cbNextMonth');
    const bookingModal = document.getElementById('cbBookingModal');
    const bookingModalMessage = document.getElementById('cbBookingModalMessage');
    const bookingModalOk = document.getElementById('cbBookingModalOk');

    let current = selectedInput.value ? new Date(selectedInput.value) : new Date();
    let selectedDate = selectedInput.value ? selectedInput.value.slice(0, 10) : null;
    let selectedSlot = selectedInput.value || null;
    const minBookable = new Date(@json($minScheduledAt));

    function formatCurrency(amount) {
        return 'PHP ' + amount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function updateCost() {
        const duration = parseInt(durationSelect.value || '60', 10);
        const total = (hourlyRate / 60) * duration;
        const downpayment = total * 0.5;
        totalEl.textContent = formatCurrency(total);
        downpaymentEl.textContent = formatCurrency(downpayment);
        balanceEl.textContent = formatCurrency(total - downpayment);
    }

    function showBookingModal(message) {
        bookingModalMessage.textContent = message;
        bookingModal.classList.add('visible');
        bookingModal.setAttribute('aria-hidden', 'false');
        bookingModalOk.focus();
    }

    function hideBookingModal() {
        bookingModal.classList.remove('visible');
        bookingModal.setAttribute('aria-hidden', 'true');
    }

    function toLocalIso(date) {
        const pad = (value) => String(value).padStart(2, '0');
        return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
    }

    function normalizeBooked() {
        return booked.map(function (item) {
            return { start: new Date(item.start), end: new Date(item.end) };
        });
    }

    function normalizeBlocked() {
        return blocked.map(function (item) {
            const range = {
                date: item.date,
                isAllDay: !!item.is_all_day,
                label: item.label,
            };

            if (!range.isAllDay) {
                range.start = new Date(item.date + 'T' + item.start_time + ':00');
                range.end = new Date(item.date + 'T' + item.end_time + ':00');
            }

            return range;
        });
    }

    function selectSlot(isoValue) {
        const chosen = new Date(isoValue);
        if (chosen < minBookable) {
            showBookingModal('Please choose an upcoming consultation time.');
            selectedInput.value = '';
            selectedDate = null;
            selectedSlot = null;
            renderCalendar();
            return;
        }

        selectedSlot = isoValue;
        selectedInput.value = isoValue;
        selectedDate = isoValue.slice(0, 10);

        quickButtons.forEach(function (button) {
            button.classList.toggle('active', button.dataset.slot === isoValue);
        });

        renderCalendar();

        if (selectedDate) {
            showSlots(selectedDate, new Date(selectedDate + 'T00:00'));
        }
    }

    function renderCalendar() {
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        const first = new Date(current.getFullYear(), current.getMonth(), 1);
        const last = new Date(current.getFullYear(), current.getMonth() + 1, 0);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        monthLabel.textContent = monthNames[current.getMonth()] + ' ' + current.getFullYear();
        daysGrid.innerHTML = '';

        for (let i = 0; i < first.getDay(); i += 1) {
            const empty = document.createElement('div');
            empty.className = 'cb-cal-cell empty';
            daysGrid.appendChild(empty);
        }

        const bookedRanges = normalizeBooked();
        const blockedRanges = normalizeBlocked();

        for (let day = 1; day <= last.getDate(); day += 1) {
            const date = new Date(current.getFullYear(), current.getMonth(), day);
            date.setHours(0, 0, 0, 0);

            const dateStr = toLocalIso(date).slice(0, 10);
            const cell = document.createElement('button');
            cell.type = 'button';
            cell.className = 'cb-cal-cell';
            cell.textContent = day;

            const isPast = date < today;
            const dayBlocked = blockedRanges.filter(function (range) { return range.date === dateStr; });
            const isBlocked = dayBlocked.some(function (range) { return range.isAllDay; });
            const hasPartialBlock = dayBlocked.length > 0 && !isBlocked;
            const isToday = date.getTime() === today.getTime();
            const isSelected = selectedDate === dateStr;
            const dayStart = new Date(date);
            const dayEnd = new Date(date);
            dayEnd.setDate(dayEnd.getDate() + 1);
            const hasBookings = bookedRanges.some(function (range) {
                return range.start < dayEnd && range.end > dayStart;
            });

            if (isPast) cell.classList.add('past');
            if (isBlocked) cell.classList.add('blocked');
            if (hasPartialBlock) cell.classList.add('has-bookings');
            if (isToday) cell.classList.add('today');
            if (isSelected) cell.classList.add('selected');
            if (hasBookings && !isPast && !isBlocked) cell.classList.add('has-bookings');

            if (isPast || isBlocked) {
                cell.disabled = true;
            } else {
                cell.addEventListener('click', function () {
                    selectedDate = dateStr;
                    renderCalendar();
                    showSlots(dateStr, date);
                });
            }

            daysGrid.appendChild(cell);
        }
    }

    function showSlots(dateStr, dateObj) {
        const bookedRanges = normalizeBooked();
        const blockedRanges = normalizeBlocked().filter(function (range) { return range.date === dateStr; });
        const now = new Date();
        const hours = [9, 10, 11, 12, 13, 14, 15, 16, 17];
        const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

        slotsTitle.textContent = dayNames[dateObj.getDay()] + ', ' + monthNames[dateObj.getMonth()] + ' ' + dateObj.getDate();
        slotsGrid.innerHTML = '';
        slotsPanel.style.display = 'block';

        if (blockedRanges.some(function (range) { return range.isAllDay; })) {
            slotsGrid.innerHTML = '<div class="cb-slot blocked">This lawyer is unavailable on this date.</div>';
            return;
        }

        hours.forEach(function (hour) {
            const slotStart = new Date(dateObj);
            slotStart.setHours(hour, 0, 0, 0);
            const slotEnd = new Date(dateObj);
            slotEnd.setHours(hour + 1, 0, 0, 0);
            const label = ((hour % 12) || 12) + ':00 ' + (hour < 12 ? 'AM' : 'PM');
            const isoValue = toLocalIso(slotStart);
            const button = document.createElement('button');
            button.type = 'button';
            button.textContent = label;
            button.className = 'cb-slot';

            if (slotStart < minBookable) {
                button.classList.add('past');
                button.disabled = true;
                button.title = 'Please choose an upcoming time';
            } else if (bookedRanges.some(function (range) { return slotStart < range.end && slotEnd > range.start; })) {
                button.classList.add('booked');
                button.disabled = true;
                button.title = 'Already booked';
            } else if (blockedRanges.some(function (range) { return !range.isAllDay && slotStart < range.end && slotEnd > range.start; })) {
                button.classList.add('blocked');
                button.disabled = true;
                button.title = 'Blocked by lawyer availability';
            } else {
                button.classList.add('available');
                if (selectedSlot === isoValue) {
                    button.classList.add('active');
                }
                button.addEventListener('click', function () {
                    selectSlot(isoValue);
                });
            }

            slotsGrid.appendChild(button);
        });
    }

    prevMonthBtn.addEventListener('click', function () {
        current = new Date(current.getFullYear(), current.getMonth() - 1, 1);
        renderCalendar();
    });

    nextMonthBtn.addEventListener('click', function () {
        current = new Date(current.getFullYear(), current.getMonth() + 1, 1);
        renderCalendar();
    });

    bookingModalOk.addEventListener('click', hideBookingModal);

    bookingModal.addEventListener('click', function (event) {
        if (event.target === bookingModal) {
            hideBookingModal();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && bookingModal.classList.contains('visible')) {
            hideBookingModal();
        }
    });

    selectedInput.addEventListener('change', function () {
        if (!selectedInput.value) return;
        const chosen = new Date(selectedInput.value);
        if (chosen < minBookable) {
            showBookingModal('Please choose an upcoming consultation time.');
            selectedInput.value = '';
            selectedDate = null;
            selectedSlot = null;
            renderCalendar();
            slotsPanel.style.display = 'none';
            return;
        }
        current = new Date(chosen.getFullYear(), chosen.getMonth(), 1);
        selectSlot(selectedInput.value.slice(0, 16));
    });

    durationSelect.addEventListener('change', updateCost);

    uploadInput.addEventListener('change', function () {
        if (uploadInput.files && uploadInput.files[0]) {
            uploadText.textContent = uploadInput.files[0].name;
        }
    });

    quickButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const chosen = new Date(button.dataset.slot);
            current = new Date(chosen.getFullYear(), chosen.getMonth(), 1);
            selectSlot(button.dataset.slot);
        });
    });

    updateCost();
    renderCalendar();

    if (selectedDate) {
        showSlots(selectedDate, new Date(selectedDate + 'T00:00'));
    }
});
</script>
@endpush
