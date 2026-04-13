@extends('layouts.app')
@section('title', $profile->user->name . ' – Lawyer Profile')

@push('styles')
<style>
/* ── Profile page ──────────────────────────────── */
.lpp-back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #1e2d4d;
    font-size: .9rem;
    font-weight: 600;
    text-decoration: none;
    margin-bottom: 20px;
}
.lpp-back:hover { color: #2563eb; }

.lpp-card {
    background: #fff;
    border: 1px solid #e8ecf0;
    border-radius: 16px;
    overflow: hidden;
    max-width: 820px;
    margin: 0 auto;
    box-shadow: 0 4px 24px rgba(30,45,77,.08);
}

/* ── Top hero banner ── */
.lpp-hero {
    background: linear-gradient(135deg, #1e2d4d 0%, #2a3f6f 100%);
    padding: 36px 40px 28px;
    display: flex;
    align-items: center;
    gap: 28px;
}
.lpp-avatar {
    width: 96px;
    height: 96px;
    border-radius: 50%;
    border: 3px solid rgba(255,255,255,.35);
    background: #3d5a99;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.4rem;
    color: #fff;
    flex-shrink: 0;
    overflow: hidden;
}
.lpp-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.lpp-hero-info { flex: 1; }
.lpp-name {
    font-size: 1.55rem;
    font-weight: 800;
    color: #fff;
    margin: 0 0 4px;
}
.lpp-specialty {
    font-size: .95rem;
    color: rgba(255,255,255,.75);
    margin: 0 0 10px;
}
.lpp-badges { display: flex; flex-wrap: wrap; gap: 8px; }
.lpp-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: .78rem;
    font-weight: 600;
}
.lpp-badge-cert  { background: rgba(34,197,94,.25);  color: #bbf7d0; border: 1px solid rgba(34,197,94,.3); }
.lpp-badge-avail { background: rgba(59,130,246,.25); color: #bfdbfe; border: 1px solid rgba(59,130,246,.3); }
.lpp-badge-firm  { background: rgba(255,255,255,.15); color: #fff; border: 1px solid rgba(255,255,255,.2); }

/* ── Stat row ── */
.lpp-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    background: #fafafa;
    border-bottom: 1px solid #e8ecf0;
}
.lpp-stat {
    padding: 24px 15px;
    text-align: center;
    border-right: 1px solid #e8ecf0;
    transition: background .2s;
}
.lpp-stat:hover { background: #f3f4f6; }
.lpp-stat:last-child { border-right: none; }
.lpp-stat-value {
    font-size: 1.5rem;
    font-weight: 800;
    color: #1e2d4d;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}
.lpp-stat-label {
    font-size: .7rem;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: .08em;
    margin-top: 4px;
}

/* ── Body content ── */
.lpp-body { padding: 30px 40px; }

.lpp-section { margin-bottom: 26px; }
.lpp-section-title {
    font-size: .85rem;
    font-weight: 700;
    color: #1e2d4d;
    text-transform: uppercase;
    letter-spacing: .06em;
    margin-bottom: 10px;
    padding-bottom: 6px;
    border-bottom: 2px solid #f0f4f8;
}
.lpp-bio {
    font-size: .93rem;
    color: #444;
    line-height: 1.65;
    margin: 0;
}

.lpp-meta-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 16px;
    margin-top: 15px;
}
.lpp-meta-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 12px 16px;
    background: #f8fafc;
    border-radius: 12px;
    border: 1px solid #f1f5f9;
    transition: transform .2s, border-color .2s;
}
.lpp-meta-item:hover {
    border-color: #cbd5e1;
    transform: translateY(-2px);
}
.lpp-meta-item > i:first-child {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: #eef2ff;
    color: #2563eb;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .95rem;
    flex-shrink: 0;
}
.lpp-meta-content {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.lpp-meta-label {
    font-size: .72rem;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: .05em;
}
.lpp-meta-value {
    font-size: .92rem;
    font-weight: 600;
    color: #334155;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* ── Stars ── */
.lpp-stars {
    color: #f59e0b;
    font-size: .85rem;
    display: inline-flex;
    gap: 2px;
}
.lpp-stars i {
    width: auto !important;
    height: auto !important;
    background: none !important;
    color: inherit !important;
    display: inline !important;
    font-size: inherit !important;
}

/* ── Action buttons ── */
.lpp-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-top: 26px;
    padding-top: 22px;
    border-top: 1px solid #e8ecf0;
}
.lpp-btn-primary {
    flex: 1;
    min-width: 160px;
    padding: 13px 20px;
    background: #1e2d4d;
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: .93rem;
    font-weight: 700;
    cursor: pointer;
    text-align: center;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: background .2s;
}
.lpp-btn-primary:hover { background: #2563eb; color: #fff; }

.lpp-btn-outline {
    flex: 1;
    min-width: 160px;
    padding: 13px 20px;
    background: transparent;
    color: #1e2d4d;
    border: 2px solid #1e2d4d;
    border-radius: 10px;
    font-size: .93rem;
    font-weight: 700;
    cursor: pointer;
    text-align: center;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all .2s;
}
.lpp-btn-outline:hover { background: #1e2d4d; color: #fff; }

/* ── Reviews section ── */
.lpp-review-list { display: flex; flex-direction: column; gap: 16px; }
.lpp-review-item {
    background: #f8fafc;
    border: 1px solid #e8ecf0;
    border-radius: 12px;
    padding: 16px 18px;
}
.lpp-review-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; }
.lpp-reviewer { display: flex; align-items: center; gap: 10px; }
.lpp-reviewer-avatar {
    width: 36px; height: 36px; border-radius: 50%;
    background: #1e2d4d; color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: .85rem; font-weight: 700; flex-shrink: 0;
}
.lpp-reviewer-name { font-size: .9rem; font-weight: 700; color: #1e2d4d; }
.lpp-review-date { font-size: .75rem; color: #999; margin-top: 2px; }
.lpp-review-stars { color: #f59e0b; font-size: .83rem; letter-spacing: 1px; }
.lpp-review-comment { font-size: .88rem; color: #444; line-height: 1.6; margin: 0; }
.lpp-no-reviews { text-align: center; padding: 32px; color: #999; font-size: .9rem; }
.lpp-no-reviews i { font-size: 1.8rem; display: block; margin-bottom: 8px; color: #ccc; }

@media (max-width: 600px) {
    .lpp-hero { flex-direction: column; align-items: flex-start; padding: 24px 20px 20px; }
    .lpp-stats { grid-template-columns: repeat(2, 1fr); }
    .lpp-body { padding: 20px; }
    .lpp-meta-grid { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')

<div id="booking-validation-data" 
     data-has-errors="{{ $errors->any() ? 'true' : 'false' }}" 
     data-old-lawyer-id="{{ old('lawyer_id') }}"
     data-current-lawyer-id="{{ $profile->user_id }}"
     data-hourly-rate="{{ $profile->hourly_rate ?? 0 }}"
     style="display:none;">
</div>

<a href="{{ route('find-lawyers') }}" class="lpp-back">
    <i class="fas fa-arrow-left"></i> Back to Find Lawyers
</a>

@php
    $profileStatus = $profile->currentStatus();
@endphp

<div class="lpp-card">

    {{-- Hero banner --}}
    <div class="lpp-hero">
        <div class="lpp-avatar">
            @if($profile->user->avatar_url)
                <img src="{{ $profile->user->avatar_url }}" alt="{{ $profile->user->name }}">
            @else
                <i class="fas fa-user-tie"></i>
            @endif
        </div>
        <div class="lpp-hero-info">
            <h1 class="lpp-name">{{ $profile->user->name }}</h1>
            <p class="lpp-specialty">{{ $profile->specialty ?? 'General Practice' }}</p>
            <div class="lpp-badges">
                @if($profile->is_certified)
                <span class="lpp-badge lpp-badge-cert">
                    <i class="fas fa-shield-check"></i> IBP Certified
                </span>
                @endif
                <span class="lpp-badge lpp-badge-avail">
                    <i class="fas fa-circle" style="font-size:.5rem;"></i>
                    {{ ucfirst($profileStatus) }}
                </span>
                @if($profile->firm)
                <span class="lpp-badge lpp-badge-firm">
                    <i class="fas fa-building"></i> {{ $profile->firm }}
                </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Stats row --}}
    <div class="lpp-stats">
        <div class="lpp-stat">
            <div class="lpp-stat-value">₱{{ number_format($profile->hourly_rate ?? 0) }}</div>
            <div class="lpp-stat-label">Rate / Hour</div>
        </div>
        <div class="lpp-stat">
            <div class="lpp-stat-value">{{ $profile->experience_years ?? 0 }}</div>
            <div class="lpp-stat-label">Years Exp.</div>
        </div>
        <div class="lpp-stat">
            <div class="lpp-stat-value">
                {{ number_format($profile->rating ?? 0, 1) }}
                <i class="fas fa-star" style="color:#f59e0b;font-size:1.1rem;"></i>
            </div>
            <div class="lpp-stat-label">Avg Rating</div>
        </div>
        <div class="lpp-stat">
            <div class="lpp-stat-value">{{ $profile->reviews_count ?? 0 }}</div>
            <div class="lpp-stat-label">Total Reviews</div>
        </div>
    </div>

    {{-- Body --}}
    <div class="lpp-body">

        {{-- About --}}
        @if($profile->bio)
        <div class="lpp-section">
            <div class="lpp-section-title">About</div>
            <p class="lpp-bio">{{ $profile->bio }}</p>
        </div>
        @endif

        {{-- Details --}}
        <div class="lpp-section">
            <div class="lpp-section-title">Professional Details</div>
            <div class="lpp-meta-grid">
                <div class="lpp-meta-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div class="lpp-meta-content">
                        <span class="lpp-meta-label">Location</span>
                        <span class="lpp-meta-value">{{ $profile->location ?? 'Location not specified' }}</span>
                    </div>
                </div>
                <div class="lpp-meta-item">
                    <i class="fas fa-briefcase"></i>
                    <div class="lpp-meta-content">
                        <span class="lpp-meta-label">Specialty</span>
                        <span class="lpp-meta-value">{{ $profile->specialty ?? 'General Practice' }}</span>
                    </div>
                </div>
                <div class="lpp-meta-item">
                    <i class="fas fa-history"></i>
                    <div class="lpp-meta-content">
                        <span class="lpp-meta-label">Years of Experience</span>
                        <span class="lpp-meta-value">{{ $profile->experience_years ?? 0 }} Years</span>
                    </div>
                </div>
                <div class="lpp-meta-item">
                    <i class="fas fa-star"></i>
                    <div class="lpp-meta-content">
                        <span class="lpp-meta-label">Client Satisfaction</span>
                        <div class="lpp-meta-value">
                            @php $r = round($profile->rating ?? 0); @endphp
                            <span class="lpp-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa{{ $i <= $r ? 's' : 'r' }} fa-star"></i>
                                @endfor
                            </span>
                            <span style="font-size: .85rem; color: #64748b; font-weight: 400;">({{ $profile->reviews_count ?? 0 }} reviews)</span>
                        </div>
                    </div>
                </div>
                @if($profile->firm)
                <div class="lpp-meta-item">
                    <i class="fas fa-building"></i>
                    <div class="lpp-meta-content">
                        <span class="lpp-meta-label">Law Firm</span>
                        <span class="lpp-meta-value">{{ $profile->firm }}</span>
                    </div>
                </div>
                @endif
                <div class="lpp-meta-item">
                    <i class="fas fa-tag"></i>
                    <div class="lpp-meta-content">
                        <span class="lpp-meta-label">Hourly Consultation</span>
                        <span class="lpp-meta-value">₱{{ number_format($profile->hourly_rate ?? 0) }} / hour</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Reviews --}}
        <div class="lpp-section">
            <div class="lpp-section-title">
                Client Reviews
                @if($reviews->count() > 0)
                    <span style="font-weight:400;font-size:.8rem;color:#6c757d;text-transform:none;letter-spacing:0;">
                        — {{ $reviews->count() }} {{ Str::plural('review', $reviews->count()) }}
                    </span>
                @endif
            </div>

            @if($reviews->isEmpty())
                <div class="lpp-no-reviews">
                    <i class="fas fa-comment-slash"></i>
                    No reviews yet. Be the first to leave one after your consultation!
                </div>
            @else
                <div class="lpp-review-list">
                    @foreach($reviews as $review)
                    <div class="lpp-review-item">
                        <div class="lpp-review-header">
                            <div class="lpp-reviewer">
                                <div class="lpp-reviewer-avatar">{{ strtoupper(substr($review->client->name, 0, 1)) }}</div>
                                <div>
                                    <div class="lpp-reviewer-name">{{ $review->client->name }}</div>
                                    <div class="lpp-review-date">{{ $review->created_at->format('M d, Y') }}</div>
                                </div>
                            </div>
                            <div class="lpp-review-stars">
                                <span style="font-weight: 700; margin-right: 5px;">{{ number_format($review->rating, 1) }}</span>
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa{{ $i <= $review->rating ? 's' : 'r' }} fa-star"></i>
                                @endfor
                            </div>
                        </div>
                        @if($review->comment)
                            <p class="lpp-review-comment">{{ $review->comment }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Actions --}}
        <div class="lpp-actions">
            @if($profileStatus === 'active')
            <a class="lpp-btn-primary" href="{{ route('consultations.create', ['lawyer' => $profile->user_id, 'return_to' => url()->full()]) }}">
                <i class="fas fa-calendar-check"></i> Book Consultation
            </a>
            @else
            <span class="lpp-btn-primary" style="opacity:.5;cursor:not-allowed;">
                <i class="fas fa-clock"></i> Currently Unavailable
            </span>
            @endif

            <form method="POST" action="{{ route('messages.start') }}" style="flex:1;min-width:160px;display:flex;">
                @csrf
                <input type="hidden" name="lawyer_id" value="{{ $profile->user_id }}">
                <button type="submit" class="lpp-btn-outline" style="width:100%;">
                    <i class="fas fa-comment"></i> Send Message
                </button>
            </form>
        </div>

    </div>{{-- /lpp-body --}}
</div>{{-- /lpp-card --}}

{{-- ── Booking Modal ─────────────────────────────────────────────────────── --}}
<div id="bookingModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9000;align-items:center;justify-content:center;padding:20px;"
     onclick="if(event.target===this)this.style.display='none'">
    <div style="background:#fff;border-radius:16px;max-width:540px;width:100%;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.25);">

        {{-- Modal Header --}}
        <div style="background:linear-gradient(135deg,#1e2d4d,#2a3f6f);border-radius:16px 16px 0 0;padding:22px 28px;display:flex;align-items:center;justify-content:space-between;">
            <div>
                <div style="color:#fff;font-size:1.1rem;font-weight:700;"><i class="fas fa-calendar-check" style="color:#b5860d;margin-right:8px;"></i> Book a Consultation</div>
                <div style="color:rgba(255,255,255,.65);font-size:.83rem;margin-top:3px;">with {{ $profile->user->name }}</div>
            </div>
            <button onclick="document.getElementById('bookingModal').style.display='none'"
                    style="background:none;border:none;color:rgba(255,255,255,.7);font-size:1.5rem;cursor:pointer;line-height:1;">&times;</button>
        </div>

        {{-- Modal Body --}}
        <form method="POST" action="{{ route('consultations.book') }}" enctype="multipart/form-data" style="padding:26px 28px;">
            @csrf
            <input type="hidden" name="lawyer_id" value="{{ $profile->user_id }}">

            {{-- Rate info bar --}}
            <div style="background:#f8f5e8;border:1px solid #e9d98a;border-radius:10px;padding:12px 16px;margin-bottom:22px;display:flex;align-items:center;gap:10px;font-size:.88rem;color:#7a5c00;">
                <i class="fas fa-info-circle"></i>
                <span>Rate: <strong>₱{{ number_format($profile->hourly_rate) }}/hr</strong> · 50% downpayment required at booking</span>
            </div>

            {{-- Date & Time --}}
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:.82rem;font-weight:700;color:#1e2d4d;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">
                    <i class="fas fa-calendar-alt" style="color:#b5860d;"></i> Preferred Date & Time *
                </label>
                <input type="datetime-local" name="scheduled_at" required
                    value="{{ old('scheduled_at') }}"
                    style="width:100%;padding:11px 14px;border:1.5px solid #dee2e6;border-radius:8px;font-size:.93rem;font-family:inherit;box-sizing:border-box;">
            </div>

            {{-- Session Type + Duration --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                <div>
                    <label style="display:block;font-size:.82rem;font-weight:700;color:#1e2d4d;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">
                        <i class="fas fa-video" style="color:#b5860d;"></i> Session Type *
                    </label>
                    <select name="type" required
                        style="width:100%;padding:11px 14px;border:1.5px solid #dee2e6;border-radius:8px;font-size:.93rem;font-family:inherit;box-sizing:border-box;background:#fff;">
                        <option value="video" {{ old('type')=='video'?'selected':'' }}>📲 Video Call</option>
                        <option value="in-person" {{ old('type')=='in-person'?'selected':'' }}>🤝 In-Person</option>
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:.82rem;font-weight:700;color:#1e2d4d;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">
                        <i class="fas fa-hourglass-half" style="color:#b5860d;"></i> Duration *
                    </label>
                    <select name="duration" id="bkDuration" onchange="updateEstimate()" required
                        style="width:100%;padding:11px 14px;border:1.5px solid #dee2e6;border-radius:8px;font-size:.93rem;font-family:inherit;box-sizing:border-box;background:#fff;">
                        <option value="30">30 minutes</option>
                        <option value="60" selected>1 hour</option>
                        <option value="90">1.5 hours</option>
                        <option value="120">2 hours</option>
                    </select>
                </div>
            </div>

            {{-- Price estimate --}}
            <div style="background:#f0f8ff;border:1px solid #b8d9f5;border-radius:8px;padding:10px 16px;margin-bottom:16px;font-size:.88rem;color:#1e2d4d;display:flex;align-items:center;justify-content:space-between;">
                <span><i class="fas fa-calculator" style="color:#2563eb;"></i> Estimated Total</span>
                <strong id="bkEstimate">₱{{ number_format($profile->hourly_rate) }}</strong>
            </div>

            {{-- Notes --}}
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:.82rem;font-weight:700;color:#1e2d4d;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">
                    <i class="fas fa-comment-alt" style="color:#b5860d;"></i> Brief Description of Your Case
                </label>
                <textarea name="notes" rows="3" placeholder="Briefly describe your legal concern so the lawyer can prepare…"
                    style="width:100%;padding:11px 14px;border:1.5px solid #dee2e6;border-radius:8px;font-size:.9rem;font-family:inherit;box-sizing:border-box;resize:vertical;">{{ old('notes') }}</textarea>
            </div>

            {{-- Document Upload --}}
            <div style="margin-bottom:22px;">
                <label style="display:block;font-size:.82rem;font-weight:700;color:#1e2d4d;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">
                    <i class="fas fa-paperclip" style="color:#b5860d;"></i> Supporting Documents <span style="font-weight:400;color:#888;text-transform:none;letter-spacing:0;">(optional)</span>
                </label>
                <p style="font-size:.8rem;color:#6c757d;margin:0 0 10px;">
                    Attach any relevant documents to help the lawyer review your case — contracts, receipts, IDs, court papers, etc.
                </p>
                <label id="docUploadLabel" style="display:flex;align-items:center;justify-content:center;gap:10px;padding:14px;border:2px dashed #b5860d;border-radius:8px;cursor:pointer;font-size:.88rem;font-weight:600;color:#b5860d;background:#fdfaf3;transition:all .2s;">
                    <i class="fas fa-cloud-upload-alt" style="font-size:1.2rem;"></i>
                    <span id="docUploadText">Click to upload (JPG, PNG, PDF, DOC — max 10MB)</span>
                    <input type="file" name="case_document" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" style="display:none;"
                           onchange="handleDocUpload(this)">
                </label>
            </div>

            {{-- Submit --}}
            <button type="submit"
                style="width:100%;padding:14px;background:#1e2d4d;color:#fff;border:none;border-radius:10px;font-size:1rem;font-weight:700;cursor:pointer;font-family:inherit;transition:background .2s;display:flex;align-items:center;justify-content:center;gap:8px;">
                <i class="fas fa-calendar-check"></i> Confirm & Proceed to Payment
            </button>
            <p style="text-align:center;font-size:.78rem;color:#aaa;margin-top:10px;">
                You will be redirected to the secure payment page to complete the 50% downpayment.
            </p>
        </form>
    </div>
</div>

@push('scripts')
<script>
var hourlyRate = 0;
const validationData = document.getElementById('booking-validation-data');
if (validationData) {
    hourlyRate = parseFloat(validationData.dataset.hourlyRate) || 0;
}

function updateEstimate() {
    var mins = parseInt(document.getElementById('bkDuration').value) || 60;
    var total = (hourlyRate / 60) * mins;
    document.getElementById('bkEstimate').textContent = '₱' + total.toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2});
}
updateEstimate();

function handleDocUpload(input) {
    var label = document.getElementById('docUploadLabel');
    var text  = document.getElementById('docUploadText');
    if (input.files && input.files[0]) {
        text.textContent = '✓ ' + input.files[0].name;
        label.style.borderColor = '#28a745';
        label.style.color = '#28a745';
        label.style.background = '#f0fff4';
    }
}

// Open modal if there were validation errors after submission
if (validationData) {
    const hasErrors = validationData.dataset.hasErrors === 'true';
    const oldLawyerId = validationData.dataset.oldLawyerId;
    const currentLawyerId = validationData.dataset.currentLawyerId;

    if (hasErrors && oldLawyerId == currentLawyerId) {
        document.getElementById('bookingModal').style.display = 'flex';
    }
}
</script>
@endpush

@endsection
