@extends('layouts.lawfirm')
@section('title', 'Team & Applications')
@section('content')

<div class="lp-page-header">
    <div>
        <h1 class="lp-page-title">Team & Applications</h1>
        <p class="lp-page-sub">Manage your lawyers and review applications</p>
    </div>
</div>

@if(session('success'))
    <div class="lp-alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

{{-- TABS --}}
<div class="lp-tabs" style="margin-bottom:24px;">
    <button class="lp-tab active" onclick="showTab('team', this)">
        <i class="fas fa-users"></i> Team Members <span class="lp-count-badge" style="margin-left:6px;">{{ $teamMembers->count() }}</span>
    </button>
    <button class="lp-tab" onclick="showTab('applications', this)">
        <i class="fas fa-inbox"></i> Applications
        @php $pendCount = $applications->where('status','pending')->count(); @endphp
        @if($pendCount > 0)<span class="lf-badge" style="margin-left:6px;">{{ $pendCount }}</span>@endif
    </button>
</div>

{{-- TEAM MEMBERS TAB --}}
<div id="tab-team" class="lf-tab-content">
    @forelse($teamMembers as $member)
    <div class="lp-consult-card" style="border-left-color:#1a3d2b;">
        <div style="display:grid;grid-template-columns:minmax(0,1fr) auto;align-items:center;column-gap:24px;row-gap:16px;width:100%;">
            <div style="display:flex;align-items:center;gap:16px;min-width:0;">
                <div class="lp-req-avatar" style="background:#1a3d2b;width:52px;height:52px;line-height:52px;font-size:1.2rem;flex-shrink:0;">
                    {{ strtoupper(substr($member->user->name, 0, 1)) }}
                </div>
                <div style="flex:1;min-width:260px;">
                    <div style="font-weight:700;font-size:1rem;color:#1e2d4d;">{{ $member->user->name }}</div>
                    <div style="font-size:.85rem;color:#6c757d;margin-top:3px;">
                        <span><i class="fas fa-gavel" style="color:#b5860d;"></i> {{ $member->specialty }}</span>
                        &nbsp;&bull;&nbsp;
                        <span><i class="fas fa-briefcase"></i> {{ $member->experience_years }} yrs experience</span>
                        @if($member->location)
                        &nbsp;&bull;&nbsp;
                        <span><i class="fas fa-map-marker-alt"></i> {{ $member->location }}</span>
                        @endif
                    </div>
                    <div style="margin-top:6px;display:flex;gap:8px;flex-wrap:wrap;">
                        <span class="lp-status-badge {{ $member->currentStatusClass() }}">{{ $member->currentStatusLabel() }}</span>
                        @if($member->is_certified)<span class="lp-pay-badge paid"><i class="fas fa-certificate"></i> Certified</span>@endif
                        <span style="font-size:.82rem;color:#6c757d;"><i class="fas fa-peso-sign"></i> ₱{{ number_format($member->hourly_rate, 0) }}/hr</span>
                    </div>
                </div>
            </div>
            <div style="display:flex;flex-direction:column;gap:8px;align-items:flex-end;justify-content:center;min-width:240px;text-align:right;justify-self:end;">
                <div style="font-size:.82rem;color:#6c757d;text-align:right;">
                    <i class="fas fa-star" style="color:#b5860d;"></i> {{ number_format($member->rating, 1) }}
                    ({{ $member->reviews_count }} reviews)
                </div>
                <div style="display:flex;gap:8px;justify-content:flex-end;flex-wrap:wrap;width:100%;">
                    <form method="POST" action="{{ route('lawfirm.messages.start') }}" style="margin:0;">
                        @csrf
                        <input type="hidden" name="lawyer_id" value="{{ $member->user_id }}">
                        <button type="submit" class="lp-btn-review" style="font-size:.8rem;padding:5px 12px;">
                            <i class="fas fa-comment"></i> Message
                        </button>
                    </form>
                    <form method="POST" action="{{ route('lawfirm.lawyers.remove', $member->user_id) }}"
                        onsubmit="return confirm('Remove {{ addslashes($member->user->name) }} from your firm?')" style="margin:0;">
                        @csrf
                        <button type="submit" class="lp-btn-decline" style="font-size:.8rem;padding:5px 12px;">
                            <i class="fas fa-user-minus"></i> Remove
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="lp-empty-sm" style="padding:40px;text-align:center;">
        <i class="fas fa-users" style="font-size:2rem;color:#dee2e6;display:block;margin-bottom:12px;"></i>
        No team members yet. Accept lawyer applications to build your team.
    </div>
    @endforelse
</div>

{{-- APPLICATIONS TAB --}}
<div id="tab-applications" class="lf-tab-content" style="display:none;">
    @forelse($applications as $app)
    @php $lp = $app->lawyer->lawyerProfile; @endphp
    <div class="lp-consult-card app-card" style="border-left-color:{{ $app->status === 'pending' ? '#ffc107' : ($app->status === 'accepted' ? '#28a745' : '#dc3545') }};">
        {{-- Top row: avatar + info + status badge --}}
        <div style="display:flex;align-items:flex-start;gap:14px;">
            <div class="lp-req-avatar" style="width:48px;height:48px;line-height:48px;font-size:1.1rem;flex-shrink:0;">
                {{ strtoupper(substr($app->lawyer->name, 0, 1)) }}
            </div>
            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                    <span style="font-weight:700;font-size:1rem;color:#1e2d4d;">{{ $app->lawyer->name }}</span>
                    <span class="lp-status-badge {{ $app->status }}" style="font-size:.75rem;">{{ ucfirst($app->status) }}</span>
                </div>
                <div style="font-size:.83rem;color:#6c757d;margin-top:4px;display:flex;flex-wrap:wrap;gap:4px 14px;">
                    @if($lp)
                    <span><i class="fas fa-gavel" style="color:#b5860d;"></i> {{ $lp->specialty }}</span>
                    <span><i class="fas fa-briefcase"></i> {{ $lp->experience_years }} yrs exp</span>
                    <span><i class="fas fa-peso-sign"></i> ₱{{ number_format($lp->hourly_rate,0) }}/hr</span>
                    @endif
                    <span><i class="fas fa-clock"></i> Applied {{ $app->created_at->diffForHumans() }}</span>
                    @if($app->responded_at && $app->status !== 'pending')
                    <span><i class="fas fa-reply"></i> Responded {{ $app->responded_at->diffForHumans() }}</span>
                    @endif
                </div>
                @if($app->message && $app->message !== 'Applied during registration.')
                <div style="margin-top:8px;background:#f8f9fa;border-radius:6px;padding:7px 12px;font-size:.83rem;color:#555;font-style:italic;border-left:3px solid #dee2e6;">
                    "{{ $app->message }}"
                </div>
                @endif
            </div>
        </div>
        {{-- Bottom row: action buttons flush right --}}
        <div style="display:flex;align-items:center;justify-content:flex-end;gap:8px;margin-top:14px;padding-top:12px;border-top:1px solid #f0f2f5;flex-wrap:wrap;">
            <button class="app-action-btn app-btn-outline" onclick="openReviewModal({{ $app->id }})">
                <i class="fas fa-file-user"></i> Review Docs
            </button>
            <form method="POST" action="{{ route('lawfirm.messages.start') }}" style="margin:0;">
                @csrf
                <input type="hidden" name="lawyer_id" value="{{ $app->lawyer_id }}">
                <button type="submit" class="app-action-btn app-btn-outline">
                    <i class="fas fa-comment-dots"></i> Message
                </button>
            </form>
            @if($app->status === 'pending')
            <form method="POST" action="{{ route('lawfirm.lawyers.accept', $app->id) }}" style="margin:0;">
                @csrf
                <button type="submit" class="app-action-btn app-btn-accept">
                    <i class="fas fa-check"></i> Accept
                </button>
            </form>
            <form method="POST" action="{{ route('lawfirm.lawyers.reject', $app->id) }}" style="margin:0;">
                @csrf
                <button type="submit" class="app-action-btn app-btn-reject" onclick="return confirm('Reject this application?')">
                    <i class="fas fa-times"></i> Reject
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- Review Modal for this application --}}
    <div id="reviewModal-{{ $app->id }}" class="lf-review-overlay" style="display:none;" onclick="if(event.target===this)closeReviewModal({{ $app->id }})">
        <div class="lf-review-modal">
            {{-- Header --}}
            <div class="lf-rm-header">
                <div class="lf-rm-head-main">
                    <div class="lp-req-avatar lf-rm-avatar">
                        {{ strtoupper(substr($app->lawyer->name, 0, 1)) }}
                    </div>
                    <div class="lf-rm-head-copy">
                        <div class="lf-rm-name">{{ $app->lawyer->name }}</div>
                        <div class="lf-rm-sub">{{ $app->lawyer->email }}</div>
                        @if($app->lawyer->phone)
                        <div class="lf-rm-sub"><i class="fas fa-phone fa-xs"></i> {{ $app->lawyer->phone }}</div>
                        @endif
                    </div>
                </div>
                <button class="lf-rm-close" onclick="closeReviewModal({{ $app->id }})">&times;</button>
            </div>

            <div class="lf-rm-body">
                {{-- Status banner --}}
                <div class="lf-rm-status-bar lf-rm-status-{{ $app->status }}">
                    <i class="fas fa-{{ $app->status === 'pending' ? 'hourglass-half' : ($app->status === 'accepted' ? 'check-circle' : 'times-circle') }}"></i>
                    Application Status: <strong>{{ ucfirst($app->status) }}</strong>
                    @if($app->responded_at) &nbsp;·&nbsp; Responded {{ $app->responded_at->diffForHumans() }} @endif
                </div>

                @if($lp)
                {{-- Professional Details --}}
                <div class="lf-rm-section-title"><i class="fas fa-id-badge"></i> Professional Details</div>
                <div class="lf-rm-grid">
                    <div class="lf-rm-field">
                        <span class="lf-rm-label">Specialty / Practice Area</span>
                        <span class="lf-rm-value">{{ $lp->specialty ?: '—' }}</span>
                    </div>
                    <div class="lf-rm-field">
                        <span class="lf-rm-label">Years of Experience</span>
                        <span class="lf-rm-value">{{ $lp->experience_years }} year{{ $lp->experience_years == 1 ? '' : 's' }}</span>
                    </div>
                    <div class="lf-rm-field">
                        <span class="lf-rm-label">Hourly Rate</span>
                        <span class="lf-rm-value">₱{{ number_format($lp->hourly_rate, 0) }}/hr</span>
                    </div>
                    <div class="lf-rm-field">
                        <span class="lf-rm-label">Location</span>
                        <span class="lf-rm-value">{{ $lp->location ?: '—' }}</span>
                    </div>
                    <div class="lf-rm-field">
                        <span class="lf-rm-label">Availability</span>
                        <span class="lf-rm-value">
                            <span class="lp-status-badge {{ $lp->currentStatusClass() }}">{{ $lp->currentStatusLabel() }}</span>
                        </span>
                    </div>
                    <div class="lf-rm-field">
                        <span class="lf-rm-label">IBP Certified</span>
                        <span class="lf-rm-value">
                            @if($lp->is_certified)
                                <span style="color:#1a7a3c;font-weight:600;"><i class="fas fa-certificate"></i> Certified by LexConnect Admin</span>
                            @else
                                <span style="color:#888;">Not yet certified</span>
                            @endif
                        </span>
                    </div>
                    @if($lp->bio)
                    <div class="lf-rm-field lf-rm-full">
                        <span class="lf-rm-label">Bio / About</span>
                        <span class="lf-rm-value">{{ $lp->bio }}</span>
                    </div>
                    @endif
                </div>

                {{-- Verification Documents --}}
                <div class="lf-rm-section-title lf-rm-section-gap"><i class="fas fa-file-alt"></i> Submitted Verification Documents</div>
                <div class="lf-rm-docs-grid">
                    {{-- Government ID --}}
                    <div class="lf-rm-doc-card">
                        <div class="lf-rm-doc-label"><i class="fas fa-id-card"></i> Government ID</div>
                        @if($lp->government_id_doc)
                            @php
                                $govExt = strtolower(pathinfo($lp->government_id_doc, PATHINFO_EXTENSION));
                            @endphp
                            @if(in_array($govExt, ['jpg','jpeg','png','gif','webp']))
                                <a href="{{ asset('storage/' . $lp->government_id_doc) }}" target="_blank" rel="noopener">
                                    <img src="{{ asset('storage/' . $lp->government_id_doc) }}" class="lf-rm-doc-img" alt="Government ID">
                                </a>
                            @else
                                <a href="{{ asset('storage/' . $lp->government_id_doc) }}" target="_blank" rel="noopener" class="lf-rm-doc-file">
                                    <i class="fas fa-file-pdf" style="font-size:2rem;color:#dc3545;"></i>
                                    <span>View PDF</span>
                                </a>
                            @endif
                        @else
                            <div class="lf-rm-doc-missing"><i class="fas fa-exclamation-triangle"></i> Not submitted</div>
                        @endif
                    </div>

                    {{-- IBP ID --}}
                    <div class="lf-rm-doc-card">
                        <div class="lf-rm-doc-label"><i class="fas fa-file-certificate"></i> IBP ID</div>
                        @if($lp->ibp_id_doc)
                            @php
                                $ibpExt = strtolower(pathinfo($lp->ibp_id_doc, PATHINFO_EXTENSION));
                            @endphp
                            @if(in_array($ibpExt, ['jpg','jpeg','png','gif','webp']))
                                <a href="{{ asset('storage/' . $lp->ibp_id_doc) }}" target="_blank" rel="noopener">
                                    <img src="{{ asset('storage/' . $lp->ibp_id_doc) }}" class="lf-rm-doc-img" alt="IBP ID">
                                </a>
                            @else
                                <a href="{{ asset('storage/' . $lp->ibp_id_doc) }}" target="_blank" rel="noopener" class="lf-rm-doc-file">
                                    <i class="fas fa-file-pdf" style="font-size:2rem;color:#dc3545;"></i>
                                    <span>View PDF</span>
                                </a>
                            @endif
                        @else
                            <div class="lf-rm-doc-missing"><i class="fas fa-exclamation-triangle"></i> Not submitted</div>
                        @endif
                    </div>
                </div>
                @else
                <div style="color:#888;padding:20px 0;">No profile information available for this lawyer.</div>
                @endif

                {{-- Application message --}}
                @if($app->message && $app->message !== 'Applied during registration.')
                <div class="lf-rm-section-title lf-rm-section-gap"><i class="fas fa-comment-alt"></i> Applicant's Message</div>
                <div class="lf-rm-message">"{{ $app->message }}"</div>
                @endif

                {{-- Action buttons inside modal (pending only) --}}
                @if($app->status === 'pending')
                <div class="lf-rm-actions">
                    <form method="POST" action="{{ route('lawfirm.lawyers.accept', $app->id) }}">
                        @csrf
                        <button class="lp-btn-accept lf-rm-action-btn"><i class="fas fa-check"></i> Accept Application</button>
                    </form>
                    <form method="POST" action="{{ route('lawfirm.lawyers.reject', $app->id) }}">
                        @csrf
                        <button class="lp-btn-decline lf-rm-action-btn" onclick="return confirm('Reject this application?')"><i class="fas fa-times"></i> Reject Application</button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="lp-empty-sm" style="padding:40px;text-align:center;">
        <i class="fas fa-inbox" style="font-size:2rem;color:#dee2e6;display:block;margin-bottom:12px;"></i>
        No applications yet. Lawyers will apply through their portal.
    </div>
    @endforelse
</div>

@endsection
@push('styles')
<style>
.lp-btn-review { background:#f0f4ff; color:#1e2d4d; border:1.5px solid #c5d0e8; border-radius:7px; padding:5px 13px; font-size:.8rem; font-weight:600; cursor:pointer; font-family:inherit; transition:all .2s; display:inline-flex;align-items:center;gap:6px; }
.lp-btn-review:hover { background:#1e2d4d; color:#fff; border-color:#1e2d4d; }
/* Application card action buttons */
.app-action-btn { display:inline-flex; align-items:center; gap:6px; padding:7px 16px; border-radius:8px; font-size:.82rem; font-weight:600; cursor:pointer; font-family:inherit; border:none; transition:all .18s; white-space:nowrap; }
.app-btn-outline { background:#f4f6fb; color:#1e2d4d; border:1.5px solid #d0dae8; }
.app-btn-outline:hover { background:#1e2d4d; color:#fff; border-color:#1e2d4d; }
.app-btn-accept { background:#1a7a3c; color:#fff; }
.app-btn-accept:hover { background:#155f30; }
.app-btn-reject { background:#fff; color:#dc3545; border:1.5px solid #dc3545; }
.app-btn-reject:hover { background:#dc3545; color:#fff; }
/* Review modal overlay */
.lf-review-overlay { position:fixed; inset:0; background:rgba(12,18,28,.48); backdrop-filter:blur(4px); z-index:9000; display:flex; align-items:center; justify-content:center; padding:28px; }
.lf-review-modal { background:linear-gradient(180deg, #fcfdff 0%, #f8fafc 100%); border-radius:24px; max-width:820px; width:100%; max-height:90vh; overflow-y:auto; box-shadow:0 28px 80px rgba(15,23,42,.28); display:flex; flex-direction:column; border:1px solid rgba(226,232,240,.95); }
.lf-rm-header { display:flex; align-items:flex-start; justify-content:space-between; gap:18px; padding:28px 30px 22px; border-bottom:1px solid #e8edf5; background:linear-gradient(180deg, #ffffff 0%, #f5f8fc 100%); border-radius:24px 24px 0 0; position:sticky; top:0; z-index:2; }
.lf-rm-head-main { display:flex; align-items:center; gap:16px; min-width:0; }
.lf-rm-avatar { width:64px; height:64px; line-height:64px; font-size:1.5rem; flex-shrink:0; background:linear-gradient(135deg, #22345c, #314b85); box-shadow:0 10px 24px rgba(30,45,77,.18); }
.lf-rm-head-copy { min-width:0; }
.lf-rm-name { font-size:1.35rem; font-weight:800; color:#1b2c4f; letter-spacing:-.02em; }
.lf-rm-sub { font-size:.9rem; color:#64748b; margin-top:4px; }
.lf-rm-close { width:38px; height:38px; background:#fff; border:1px solid #d9e1ee; border-radius:999px; font-size:1.4rem; line-height:1; cursor:pointer; color:#94a3b8; padding:0; display:flex; align-items:center; justify-content:center; box-shadow:0 6px 18px rgba(15,23,42,.08); }
.lf-rm-close:hover { color:#1e2d4d; border-color:#b9c7db; }
.lf-rm-body { padding:26px 30px 30px; }
.lf-rm-status-bar { border-radius:14px; padding:13px 16px; font-size:.92rem; margin-bottom:24px; display:flex; align-items:center; gap:10px; font-weight:600; }
.lf-rm-status-pending { background:#fffbeb; color:#92400e; border:1px solid #fcd34d; }
.lf-rm-status-accepted { background:#ecfdf5; color:#065f46; border:1px solid #6ee7b7; }
.lf-rm-status-rejected { background:#fef2f2; color:#991b1b; border:1px solid #fca5a5; }
.lf-rm-section-title { font-size:.76rem; font-weight:800; color:#a16207; text-transform:uppercase; letter-spacing:.14em; padding-bottom:8px; border-bottom:1px solid #eadfbe; margin-bottom:16px; display:flex; align-items:center; gap:8px; }
.lf-rm-section-gap { margin-top:28px; }
.lf-rm-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.lf-rm-full { grid-column:1/-1; }
.lf-rm-field { display:flex; flex-direction:column; gap:7px; padding:16px 18px; background:#fff; border:1px solid #e7edf5; border-radius:16px; box-shadow:0 10px 22px rgba(15,23,42,.04); }
.lf-rm-label { font-size:.72rem; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:.08em; }
.lf-rm-value { font-size:1rem; color:#16233f; line-height:1.5; }
.lf-rm-docs-grid { display:grid; grid-template-columns:1fr 1fr; gap:18px; }
.lf-rm-doc-card { border:1px solid #e7edf5; border-radius:18px; padding:16px; background:#fff; display:flex; flex-direction:column; gap:12px; align-items:center; box-shadow:0 10px 24px rgba(15,23,42,.04); min-height:210px; }
.lf-rm-doc-label { font-size:.84rem; font-weight:800; color:#1e2d4d; align-self:flex-start; display:flex; align-items:center; gap:7px; }
.lf-rm-doc-img { width:100%; max-height:220px; object-fit:contain; border-radius:12px; border:1px solid #dee6f0; background:#f8fafc; cursor:pointer; transition:transform .2s, box-shadow .2s; }
.lf-rm-doc-img:hover { transform:translateY(-2px); box-shadow:0 12px 22px rgba(15,23,42,.08); }
.lf-rm-doc-file { display:flex; flex-direction:column; align-items:center; justify-content:center; gap:8px; text-decoration:none; color:#1e2d4d; font-size:.9rem; font-weight:700; padding:18px; border:2px dashed #d7e0ec; border-radius:14px; width:100%; min-height:130px; box-sizing:border-box; background:#fbfcfe; }
.lf-rm-doc-file:hover { border-color:#b5860d; color:#b5860d; background:#fffdf8; }
.lf-rm-doc-missing { font-size:.9rem; color:#dc3545; display:flex; align-items:center; gap:8px; padding:18px 0; min-height:130px; }
.lf-rm-message { background:#fffdf8; border:1px solid #f3e4b8; border-left:4px solid #b5860d; border-radius:14px; padding:15px 18px; font-size:.92rem; color:#5b6472; font-style:italic; margin-top:8px; line-height:1.65; }
.lf-rm-actions { display:flex; gap:12px; margin-top:28px; padding-top:22px; border-top:1px solid #e8edf5; position:sticky; bottom:0; background:linear-gradient(180deg, rgba(248,250,252,0) 0%, #f8fafc 24%, #f8fafc 100%); padding-bottom:4px; }
.lf-rm-action-btn { font-size:.95rem; padding:12px 24px; border-radius:12px; box-shadow:0 10px 18px rgba(15,23,42,.08); }
@media (max-width: 760px) {
    .lf-review-overlay { padding:14px; }
    .lf-review-modal { max-height:94vh; border-radius:20px; }
    .lf-rm-header, .lf-rm-body { padding-left:18px; padding-right:18px; }
    .lf-rm-head-main { align-items:flex-start; }
    .lf-rm-grid, .lf-rm-docs-grid { grid-template-columns:1fr; }
    .lf-rm-actions { flex-direction:column; }
    .lf-rm-action-btn { width:100%; justify-content:center; }
}
</style>
@endpush
@push('scripts')
<script>
function showTab(name, btn) {
    document.querySelectorAll('.lf-tab-content').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.lp-tab').forEach(el => el.classList.remove('active'));
    document.getElementById('tab-' + name).style.display = 'block';
    btn.classList.add('active');
}
function openReviewModal(id) {
    document.getElementById('reviewModal-' + id).style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeReviewModal(id) {
    document.getElementById('reviewModal-' + id).style.display = 'none';
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.lf-review-overlay').forEach(el => {
            el.style.display = 'none';
        });
        document.body.style.overflow = '';
    }
});
</script>
@endpush

