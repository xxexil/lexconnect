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
                <div style="width:52px;height:52px;border-radius:50%;background:#1a3d2b;color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.2rem;font-weight:700;flex-shrink:0;overflow:hidden;">
                    @if($member->user->avatar_url)
                        <img src="{{ $member->user->avatar_url }}" style="width:100%;height:100%;object-fit:cover;display:block;" alt="{{ $member->user->name }}">
                    @else
                        {{ strtoupper(substr($member->user->name, 0, 1)) }}
                    @endif
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
                <div style="font-size:.78rem;color:#adb5bd;text-align:right;">
                    <i class="fas fa-calendar-check" style="margin-right:3px;"></i>{{ $member->total_consultations }} consultation{{ $member->total_consultations != 1 ? 's' : '' }}
                    @if($member->joined_at)
                    &nbsp;·&nbsp; Joined {{ \Carbon\Carbon::parse($member->joined_at)->format('M j, Y') }}
                    @endif
                </div>
                <div style="display:flex;gap:8px;justify-content:flex-end;flex-wrap:wrap;width:100%;">
                    <form method="POST" action="{{ route('lawfirm.messages.start') }}" style="margin:0;">
                        @csrf
                        <input type="hidden" name="lawyer_id" value="{{ $member->user_id }}">
                        <button type="submit" class="lp-btn-review" style="font-size:.8rem;padding:5px 12px;">
                            <i class="fas fa-comment"></i> Message
                        </button>
                    </form>
                    <form method="POST" action="{{ route('lawfirm.lawyers.remove', $member->user_id) }}" style="margin:0;">
                        @csrf
                        <button type="button" class="lp-btn-decline js-lawfirm-app-confirm" data-action="remove" data-name="{{ $member->user->name }}" style="font-size:.8rem;padding:5px 12px;">
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

    {{-- Team pagination --}}
    <div id="pg-bar-team" class="lf-pg-bar" style="display:none;">
        <button class="pg-prev" onclick="changePage('team',-1)"><i class="fas fa-chevron-left"></i> Previous</button>
        <span class="pg-info"></span>
        <button class="pg-next" onclick="changePage('team',1)">Next <i class="fas fa-chevron-right"></i></button>
    </div>
</div>

{{-- APPLICATIONS TAB --}}
<div id="tab-applications" class="lf-tab-content" style="display:none;">
    {{-- Search + Filter --}}
    <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:16px;align-items:center;">
        <div style="display:flex;align-items:center;gap:8px;background:#fff;border:1.5px solid #e2e8f0;border-radius:10px;padding:9px 14px;flex:1;min-width:200px;max-width:360px;">
            <i class="fas fa-search" style="color:#adb5bd;font-size:.85rem;"></i>
            <input type="text" id="appSearch" placeholder="Search applicants..." autocomplete="off"
                style="border:none;outline:none;font-size:.88rem;color:#1e2d4d;width:100%;background:transparent;"
                oninput="filterApps()">
        </div>
        <div style="display:flex;gap:6px;">
            <button onclick="setAppFilter('all', this)" class="app-filter-btn active" data-filter="all">
                All <span style="background:#e2e8f0;color:#6c757d;font-size:.72rem;padding:1px 7px;border-radius:20px;margin-left:4px;">{{ $applications->count() }}</span>
            </button>
            <button onclick="setAppFilter('pending', this)" class="app-filter-btn" data-filter="pending">
                Pending <span style="background:#fef3c7;color:#92400e;font-size:.72rem;padding:1px 7px;border-radius:20px;margin-left:4px;">{{ $applications->where('status','pending')->count() }}</span>
            </button>
            <button onclick="setAppFilter('rejected', this)" class="app-filter-btn" data-filter="rejected">
                Rejected <span style="background:#fee2e2;color:#991b1b;font-size:.72rem;padding:1px 7px;border-radius:20px;margin-left:4px;">{{ $applications->where('status','rejected')->count() }}</span>
            </button>
        </div>
    </div>
    @forelse($applications as $app)
    @php $lp = $app->lawyer->lawyerProfile; @endphp
    <div class="lp-consult-card app-card" data-app-name="{{ strtolower($app->lawyer->name) }}" data-app-status="{{ $app->status }}" style="border-left-color:{{ $app->status === 'pending' ? '#ffc107' : '#dc3545' }};">
        {{-- Top row: avatar + info + status badge --}}
        <div style="display:flex;align-items:flex-start;gap:14px;">
            <div style="width:48px;height:48px;border-radius:50%;background:#1e2d4d;color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.1rem;font-weight:700;flex-shrink:0;overflow:hidden;">
                @if($app->lawyer->avatar_url)
                    <img src="{{ $app->lawyer->avatar_url }}" style="width:100%;height:100%;object-fit:cover;display:block;" alt="{{ $app->lawyer->name }}">
                @else
                    {{ strtoupper(substr($app->lawyer->name, 0, 1)) }}
                @endif
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
                <button type="button" class="app-action-btn app-btn-accept js-lawfirm-app-confirm" data-action="accept" data-name="{{ $app->lawyer->name }}">
                    <i class="fas fa-check"></i> Accept
                </button>
            </form>
            <form method="POST" action="{{ route('lawfirm.lawyers.reject', $app->id) }}" style="margin:0;">
                @csrf
                <button type="button" class="app-action-btn app-btn-reject js-lawfirm-app-confirm" data-action="reject" data-name="{{ $app->lawyer->name }}">
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
                    <div class="lp-req-avatar lf-rm-avatar" style="overflow:hidden;">
                        @if($app->lawyer->avatar_url)
                            <img src="{{ $app->lawyer->avatar_url }}" style="width:100%;height:100%;object-fit:cover;display:block;" alt="{{ $app->lawyer->name }}">
                        @else
                            {{ strtoupper(substr($app->lawyer->name, 0, 1)) }}
                        @endif
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
                                $govUrl = $lp->documentUrl('government_id');
                            @endphp
                            @if(in_array($govExt, ['jpg','jpeg','png','gif','webp']))
                                <a href="{{ $govUrl }}" target="_blank" rel="noopener">
                                    <img src="{{ $govUrl }}" class="lf-rm-doc-img" alt="Government ID">
                                </a>
                            @else
                                <a href="{{ $govUrl }}" target="_blank" rel="noopener" class="lf-rm-doc-file">
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
                                $ibpUrl = $lp->documentUrl('ibp_id');
                            @endphp
                            @if(in_array($ibpExt, ['jpg','jpeg','png','gif','webp']))
                                <a href="{{ $ibpUrl }}" target="_blank" rel="noopener">
                                    <img src="{{ $ibpUrl }}" class="lf-rm-doc-img" alt="IBP ID">
                                </a>
                            @else
                                <a href="{{ $ibpUrl }}" target="_blank" rel="noopener" class="lf-rm-doc-file">
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
                        <button type="button" class="lp-btn-accept lf-rm-action-btn js-lawfirm-app-confirm" data-action="accept" data-name="{{ $app->lawyer->name }}"><i class="fas fa-check"></i> Accept Application</button>
                    </form>
                    <form method="POST" action="{{ route('lawfirm.lawyers.reject', $app->id) }}">
                        @csrf
                        <button type="button" class="lp-btn-decline lf-rm-action-btn js-lawfirm-app-confirm" data-action="reject" data-name="{{ $app->lawyer->name }}"><i class="fas fa-times"></i> Reject Application</button>
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

    {{-- Applications pagination --}}
    <div id="pg-bar-applications" class="lf-pg-bar" style="display:none;">
        <button class="pg-prev" onclick="changePage('applications',-1)"><i class="fas fa-chevron-left"></i> Previous</button>
        <span class="pg-info"></span>
        <button class="pg-next" onclick="changePage('applications',1)">Next <i class="fas fa-chevron-right"></i></button>
    </div>
</div>

<div class="lf-app-confirm-modal" id="lawfirmApplicationModal" aria-hidden="true">
    <div class="lf-app-confirm-backdrop" data-app-modal-close></div>
    <div class="lf-app-confirm-dialog" role="dialog" aria-modal="true" aria-labelledby="lawfirmAppModalTitle">
        <button type="button" class="lf-app-confirm-close" data-app-modal-close aria-label="Close">&times;</button>
        <div class="lf-app-confirm-icon" id="lawfirmAppModalIcon"><i class="fas fa-check"></i></div>
        <h2 id="lawfirmAppModalTitle">Confirm action</h2>
        <div class="lf-app-confirm-meta" id="lawfirmAppModalMeta"></div>
        <div class="lf-app-confirm-actions">
            <button type="button" class="lf-app-confirm-cancel" data-app-modal-close>Cancel</button>
            <button type="button" class="lf-app-confirm-submit" id="lawfirmAppModalSubmit">Confirm</button>
        </div>
    </div>
</div>

@endsection
@push('styles')
<style>
.lp-btn-review { background:#f0f4ff; color:#1e2d4d; border:1.5px solid #c5d0e8; border-radius:7px; padding:5px 13px; font-size:.8rem; font-weight:600; cursor:pointer; font-family:inherit; transition:all .2s; display:inline-flex;align-items:center;gap:6px; }
.lp-btn-review:hover { background:#1e2d4d; color:#fff; border-color:#1e2d4d; }
.lf-pg-bar { display:flex; align-items:center; justify-content:center; gap:16px; padding:18px 0 6px; }
.lf-pg-bar .pg-prev, .lf-pg-bar .pg-next { display:flex; align-items:center; gap:6px; padding:8px 18px; border:1.5px solid #d1d5db; border-radius:8px; background:#fff; color:#1e2d4d; font-size:.85rem; font-weight:600; cursor:pointer; font-family:inherit; transition:all .15s; }
.lf-pg-bar .pg-prev:hover:not(:disabled), .lf-pg-bar .pg-next:hover:not(:disabled) { background:#1e2d4d; color:#fff; border-color:#1e2d4d; }
.lf-pg-bar .pg-prev:disabled, .lf-pg-bar .pg-next:disabled { opacity:.35; cursor:not-allowed; }
.lf-pg-bar .pg-info { font-size:.85rem; color:#6c757d; min-width:110px; text-align:center; }
.app-filter-btn { padding:7px 14px; border:1.5px solid #e2e8f0; border-radius:8px; background:#fff; color:#6c757d; font-size:.82rem; font-weight:600; cursor:pointer; font-family:inherit; transition:all .15s; }
.app-filter-btn:hover { border-color:#1e2d4d; color:#1e2d4d; }
.app-filter-btn.active { background:#1e2d4d; color:#fff; border-color:#1e2d4d; }
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
.lf-app-confirm-modal { position:fixed; inset:0; display:none; align-items:center; justify-content:center; padding:24px; z-index:9600; }
.lf-app-confirm-modal.open { display:flex; }
.lf-app-confirm-backdrop { position:absolute; inset:0; background:rgba(15,23,42,.46); backdrop-filter:blur(4px); }
.lf-app-confirm-dialog { position:relative; width:min(100%, 420px); background:linear-gradient(180deg, #ffffff 0%, #f8fafc 100%); border:1px solid rgba(226,232,240,.95); border-radius:24px; box-shadow:0 28px 80px rgba(15,23,42,.28); padding:28px 26px 24px; text-align:center; }
.lf-app-confirm-close { position:absolute; top:14px; right:14px; width:34px; height:34px; border:none; border-radius:999px; background:#eef2f7; color:#64748b; font-size:1.25rem; line-height:1; cursor:pointer; display:flex; align-items:center; justify-content:center; }
.lf-app-confirm-close:hover { background:#e2e8f0; color:#1e2d4d; }
.lf-app-confirm-icon { width:60px; height:60px; margin:0 auto 16px; border-radius:18px; display:flex; align-items:center; justify-content:center; font-size:1.45rem; color:#fff; box-shadow:0 14px 28px rgba(15,23,42,.18); }
.lf-app-confirm-icon.accept { background:linear-gradient(135deg, #16a34a, #15803d); }
.lf-app-confirm-icon.reject { background:linear-gradient(135deg, #ef4444, #dc2626); }
.lf-app-confirm-dialog h2 { margin:0; font-size:1.18rem; font-weight:800; color:#1b2c4f; }
.lf-app-confirm-meta { margin-top:10px; color:#64748b; font-size:.92rem; line-height:1.55; }
.lf-app-confirm-actions { display:flex; justify-content:center; gap:12px; margin-top:22px; }
.lf-app-confirm-cancel,
.lf-app-confirm-submit { min-width:118px; border-radius:12px; padding:11px 18px; font-size:.92rem; font-weight:700; font-family:inherit; cursor:pointer; transition:all .18s; }
.lf-app-confirm-cancel { background:#fff; color:#475569; border:1.5px solid #d7dee8; }
.lf-app-confirm-cancel:hover { background:#f8fafc; border-color:#cbd5e1; }
.lf-app-confirm-submit { border:none; color:#fff; }
.lf-app-confirm-submit.accept { background:#1a7a3c; }
.lf-app-confirm-submit.accept:hover { background:#155f30; }
.lf-app-confirm-submit.reject { background:#dc3545; }
.lf-app-confirm-submit.reject:hover { background:#b91c1c; }
.lf-app-confirm-submit.remove { background:#dc3545; }
.lf-app-confirm-submit.remove:hover { background:#b91c1c; }
.lf-app-confirm-submit:disabled { opacity:.65; cursor:wait; }
@media (max-width: 760px) {
    .lf-review-overlay { padding:14px; }
    .lf-review-modal { max-height:94vh; border-radius:20px; }
    .lf-rm-header, .lf-rm-body { padding-left:18px; padding-right:18px; }
    .lf-rm-head-main { align-items:flex-start; }
    .lf-rm-grid, .lf-rm-docs-grid { grid-template-columns:1fr; }
    .lf-rm-actions { flex-direction:column; }
    .lf-rm-action-btn { width:100%; justify-content:center; }
    .lf-app-confirm-modal { padding:16px; }
    .lf-app-confirm-dialog { width:100%; padding:24px 18px 20px; border-radius:20px; }
    .lf-app-confirm-actions { flex-direction:column-reverse; }
    .lf-app-confirm-cancel,
    .lf-app-confirm-submit { width:100%; }
}
</style>
@endpush
@push('scripts')
<script>
var currentAppFilter = 'all';
let pendingLawfirmAppForm = null;

function setAppFilter(filter, btn) {
    currentAppFilter = filter;
    document.querySelectorAll('.app-filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    filterApps();
}

function filterApps() {
    const q = (document.getElementById('appSearch').value || '').toLowerCase();
    document.querySelectorAll('.app-card').forEach(function(card) {
        const nameMatch = card.dataset.appName.includes(q);
        const statusMatch = currentAppFilter === 'all' || card.dataset.appStatus === currentAppFilter;
        card.style.display = (nameMatch && statusMatch) ? '' : 'none';
    });
    tabPages['applications'] = 1;
    paginateTab('applications');
}

// ── Pagination ──
const PAGE_SIZE = 10;
const tabPages = { team: 1, applications: 1 };

function paginateTab(tabName) {
    const container = document.getElementById('tab-' + tabName);
    const cards = Array.from(container.querySelectorAll(tabName === 'team' ? '.lp-consult-card' : '.app-card')).filter(c => c.style.display !== 'none' || tabName === 'team');
    const allCards = Array.from(container.querySelectorAll(tabName === 'team' ? '.lp-consult-card' : '.app-card'));
    const visibleCards = tabName === 'applications'
        ? allCards.filter(c => c.style.display !== 'none')
        : allCards;

    const total = visibleCards.length;
    const page  = tabPages[tabName];
    const totalPages = Math.max(1, Math.ceil(total / PAGE_SIZE));
    const start = (page - 1) * PAGE_SIZE;
    const end   = start + PAGE_SIZE;

    // Hide all first, then show current page
    allCards.forEach(c => { if (c.style.display !== 'none' || tabName === 'team') c.style.display = 'none'; });
    visibleCards.forEach((c, i) => { c.style.display = (i >= start && i < end) ? '' : 'none'; });

    const bar = document.getElementById('pg-bar-' + tabName);
    if (!bar) return;
    bar.querySelector('.pg-info').textContent = total === 0 ? 'No records' : (start + 1) + '–' + Math.min(end, total) + ' of ' + total;
    bar.querySelector('.pg-prev').disabled = page <= 1;
    bar.querySelector('.pg-next').disabled = page >= totalPages;
    bar.style.display = total <= PAGE_SIZE ? 'none' : 'flex';
}

function changePage(tabName, dir) {
    const container = document.getElementById('tab-' + tabName);
    const allCards = Array.from(container.querySelectorAll(tabName === 'team' ? '.lp-consult-card' : '.app-card'));
    const visibleCards = tabName === 'applications' ? allCards.filter(c => c.style.display !== 'none' || true) : allCards;
    const total = allCards.length;
    const totalPages = Math.max(1, Math.ceil(total / PAGE_SIZE));
    tabPages[tabName] = Math.min(Math.max(1, tabPages[tabName] + dir), totalPages);
    paginateTab(tabName);
    container.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

['team', 'applications'].forEach(function(tab) {
    paginateTab(tab);
});

function showTab(name, btn) {
    document.querySelectorAll('.lf-tab-content').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.lp-tab').forEach(el => el.classList.remove('active'));
    document.getElementById('tab-' + name).style.display = 'block';
    btn.classList.add('active');
}

// Auto-open tab from URL hash
(function() {
    const hash = window.location.hash.replace('#', '');
    if (hash === 'applications' || hash === 'team') {
        const btn = document.querySelector('.lp-tab[onclick*="' + hash + '"]');
        if (btn) showTab(hash, btn);
    }
})();
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

const lawfirmAppModal = document.getElementById('lawfirmApplicationModal');
const lawfirmAppModalIcon = document.getElementById('lawfirmAppModalIcon');
const lawfirmAppModalTitle = document.getElementById('lawfirmAppModalTitle');
const lawfirmAppModalMeta = document.getElementById('lawfirmAppModalMeta');
const lawfirmAppModalSubmit = document.getElementById('lawfirmAppModalSubmit');

function openLawfirmAppModal(button) {
    pendingLawfirmAppForm = button.closest('form');
    if (!pendingLawfirmAppForm) return;

    const action = button.dataset.action === 'reject'
        ? 'reject'
        : (button.dataset.action === 'remove' ? 'remove' : 'accept');
    const lawyerName = button.dataset.name || 'this applicant';

    lawfirmAppModalIcon.className = 'lf-app-confirm-icon ' + (action === 'accept' ? 'accept' : 'reject');
    lawfirmAppModalIcon.innerHTML = action === 'accept'
        ? '<i class="fas fa-check"></i>'
        : (action === 'remove' ? '<i class="fas fa-user-minus"></i>' : '<i class="fas fa-times"></i>');
    lawfirmAppModalTitle.textContent = action === 'accept'
        ? 'Accept application?'
        : (action === 'remove' ? 'Remove team member?' : 'Reject application?');
    lawfirmAppModalMeta.textContent = action === 'accept'
        ? 'You are about to accept ' + lawyerName + ' into your law firm.'
        : (action === 'remove'
            ? 'You are about to remove ' + lawyerName + ' from your law firm.'
            : 'You are about to reject ' + lawyerName + '\'s application.');
    lawfirmAppModalSubmit.className = 'lf-app-confirm-submit ' + action;
    lawfirmAppModalSubmit.textContent = action === 'accept'
        ? 'Accept'
        : (action === 'remove' ? 'Remove' : 'Reject');
    lawfirmAppModalSubmit.dataset.action = action;

    lawfirmAppModal.classList.add('open');
    lawfirmAppModal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
}

function closeLawfirmAppModal() {
    lawfirmAppModal.classList.remove('open');
    lawfirmAppModal.setAttribute('aria-hidden', 'true');
    lawfirmAppModalSubmit.disabled = false;
    pendingLawfirmAppForm = null;
    document.body.style.overflow = '';
}

document.querySelectorAll('.js-lawfirm-app-confirm').forEach(function(button) {
    button.addEventListener('click', function() {
        openLawfirmAppModal(button);
    });
});

document.querySelectorAll('[data-app-modal-close]').forEach(function(button) {
    button.addEventListener('click', closeLawfirmAppModal);
});

lawfirmAppModalSubmit.addEventListener('click', function() {
    if (!pendingLawfirmAppForm) return;
    lawfirmAppModalSubmit.disabled = true;
    pendingLawfirmAppForm.submit();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && lawfirmAppModal.classList.contains('open')) {
        closeLawfirmAppModal();
    }
});
</script>
@endpush

