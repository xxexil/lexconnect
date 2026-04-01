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
        <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
            <div class="lp-req-avatar" style="background:#1a3d2b;width:52px;height:52px;line-height:52px;font-size:1.2rem;">
                {{ strtoupper(substr($member->user->name, 0, 1)) }}
            </div>
            <div style="flex:1;min-width:200px;">
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
                    <span class="lp-status-badge {{ $member->availability_status }}">{{ ucfirst($member->availability_status) }}</span>
                    @if($member->is_certified)<span class="lp-pay-badge paid"><i class="fas fa-certificate"></i> Certified</span>@endif
                    <span style="font-size:.82rem;color:#6c757d;"><i class="fas fa-peso-sign"></i> ₱{{ number_format($member->hourly_rate, 0) }}/hr</span>
                </div>
            </div>
            <div style="display:flex;flex-direction:column;gap:6px;align-items:flex-end;">
                <div style="font-size:.82rem;color:#6c757d;text-align:right;">
                    <i class="fas fa-star" style="color:#b5860d;"></i> {{ number_format($member->rating, 1) }}
                    ({{ $member->reviews_count }} reviews)
                </div>
                <div style="display:flex;gap:6px;">
                    <form method="POST" action="{{ route('lawfirm.messages.start') }}">
                        @csrf
                        <input type="hidden" name="lawyer_id" value="{{ $member->user_id }}">
                        <button type="submit" class="lp-btn-review" style="font-size:.8rem;padding:5px 12px;">
                            <i class="fas fa-comment"></i> Message
                        </button>
                    </form>
                    <form method="POST" action="{{ route('lawfirm.lawyers.remove', $member->user_id) }}"
                        onsubmit="return confirm('Remove {{ addslashes($member->user->name) }} from your firm?')">
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
                <div style="display:flex;align-items:center;gap:14px;">
                    <div class="lp-req-avatar" style="width:56px;height:56px;line-height:56px;font-size:1.4rem;flex-shrink:0;">
                        {{ strtoupper(substr($app->lawyer->name, 0, 1)) }}
                    </div>
                    <div>
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
                            <span class="lp-status-badge {{ $lp->availability_status }}">{{ ucfirst($lp->availability_status) }}</span>
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
                <div class="lf-rm-section-title" style="margin-top:22px;"><i class="fas fa-file-alt"></i> Submitted Verification Documents</div>
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
                <div class="lf-rm-section-title" style="margin-top:22px;"><i class="fas fa-comment-alt"></i> Applicant's Message</div>
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
.lf-review-overlay { position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:9000; display:flex; align-items:center; justify-content:center; padding:20px; }
.lf-review-modal { background:#fff; border-radius:16px; max-width:700px; width:100%; max-height:90vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,.25); display:flex; flex-direction:column; }
.lf-rm-header { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; padding:24px 28px 20px; border-bottom:1px solid #f0f0f0; background:#f7f9fc; border-radius:16px 16px 0 0; }
.lf-rm-name { font-size:1.15rem; font-weight:700; color:#1e2d4d; }
.lf-rm-sub { font-size:.83rem; color:#6c757d; margin-top:2px; }
.lf-rm-close { background:none; border:none; font-size:1.6rem; line-height:1; cursor:pointer; color:#aaa; padding:0 4px; align-self:flex-start; }
.lf-rm-close:hover { color:#1e2d4d; }
.lf-rm-body { padding:24px 28px; }
.lf-rm-status-bar { border-radius:8px; padding:10px 16px; font-size:.87rem; margin-bottom:20px; }
.lf-rm-status-pending { background:#fffbeb; color:#92400e; border:1px solid #fcd34d; }
.lf-rm-status-accepted { background:#ecfdf5; color:#065f46; border:1px solid #6ee7b7; }
.lf-rm-status-rejected { background:#fef2f2; color:#991b1b; border:1px solid #fca5a5; }
.lf-rm-section-title { font-size:.78rem; font-weight:700; color:#b5860d; text-transform:uppercase; letter-spacing:.8px; padding-bottom:6px; border-bottom:1px solid #f0e6c8; margin-bottom:14px; display:flex; align-items:center; gap:7px; }
.lf-rm-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px 24px; }
.lf-rm-full { grid-column:1/-1; }
.lf-rm-field { display:flex; flex-direction:column; gap:3px; }
.lf-rm-label { font-size:.75rem; font-weight:600; color:#6c757d; text-transform:uppercase; letter-spacing:.4px; }
.lf-rm-value { font-size:.9rem; color:#1e2d4d; }
.lf-rm-docs-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
.lf-rm-doc-card { border:1.5px solid #e9ecef; border-radius:10px; padding:14px; background:#fafafa; display:flex; flex-direction:column; gap:10px; align-items:center; }
.lf-rm-doc-label { font-size:.8rem; font-weight:700; color:#1e2d4d; align-self:flex-start; display:flex; align-items:center; gap:6px; }
.lf-rm-doc-img { width:100%; max-height:200px; object-fit:contain; border-radius:6px; border:1px solid #dee2e6; cursor:pointer; transition:transform .2s; }
.lf-rm-doc-img:hover { transform:scale(1.02); }
.lf-rm-doc-file { display:flex; flex-direction:column; align-items:center; gap:6px; text-decoration:none; color:#1e2d4d; font-size:.85rem; font-weight:600; padding:16px; border:2px dashed #dee2e6; border-radius:8px; width:100%; box-sizing:border-box; }
.lf-rm-doc-file:hover { border-color:#b5860d; color:#b5860d; }
.lf-rm-doc-missing { font-size:.83rem; color:#dc3545; display:flex; align-items:center; gap:6px; padding:12px 0; }
.lf-rm-message { background:#f8f9fa; border-left:3px solid #b5860d; border-radius:0 6px 6px 0; padding:12px 16px; font-size:.88rem; color:#555; font-style:italic; margin-top:6px; }
.lf-rm-actions { display:flex; gap:12px; margin-top:24px; padding-top:20px; border-top:1px solid #f0f0f0; }
.lf-rm-action-btn { font-size:.9rem; padding:10px 22px; }
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
