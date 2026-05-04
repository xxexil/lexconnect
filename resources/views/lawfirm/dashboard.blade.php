@extends('layouts.lawfirm')
@section('title', 'Dashboard')
@section('content')

@push('styles')
<style>
.lp-btn-review { background:#f0f4ff; color:#1e2d4d; border:1.5px solid #c5d0e8; border-radius:7px; padding:5px 13px; font-size:.8rem; font-weight:600; cursor:pointer; font-family:inherit; transition:all .2s; display:inline-flex;align-items:center;gap:6px; }
.lp-btn-review:hover { background:#1e2d4d; color:#fff; border-color:#1e2d4d; }
.lf-review-overlay { position:fixed; inset:0; background:rgba(12,18,28,.48); backdrop-filter:blur(4px); z-index:9000; display:flex; align-items:center; justify-content:center; padding:28px; }
.lf-review-modal { background:linear-gradient(180deg,#fcfdff 0%,#f8fafc 100%); border-radius:24px; max-width:820px; width:100%; max-height:90vh; overflow-y:auto; box-shadow:0 28px 80px rgba(15,23,42,.28); display:flex; flex-direction:column; border:1px solid rgba(226,232,240,.95); }
.lf-rm-header { display:flex; align-items:flex-start; justify-content:space-between; gap:18px; padding:28px 30px 22px; border-bottom:1px solid #e8edf5; background:linear-gradient(180deg,#ffffff 0%,#f5f8fc 100%); border-radius:24px 24px 0 0; position:sticky; top:0; z-index:2; }
.lf-rm-head-main { display:flex; align-items:center; gap:16px; min-width:0; }
.lf-rm-avatar { width:64px!important; height:64px!important; line-height:64px!important; font-size:1.5rem!important; flex-shrink:0; background:linear-gradient(135deg,#22345c,#314b85)!important; box-shadow:0 10px 24px rgba(30,45,77,.18); }
.lf-rm-head-copy { min-width:0; }
.lf-rm-name { font-size:1.35rem; font-weight:800; color:#1b2c4f; }
.lf-rm-sub { font-size:.9rem; color:#64748b; margin-top:4px; }
.lf-rm-close { width:38px; height:38px; background:#fff; border:1px solid #d9e1ee; border-radius:999px; font-size:1.4rem; line-height:1; cursor:pointer; color:#94a3b8; padding:0; display:flex; align-items:center; justify-content:center; }
.lf-rm-close:hover { color:#1e2d4d; }
.lf-rm-body { padding:26px 30px 30px; }
.lf-rm-status-bar { border-radius:14px; padding:13px 16px; font-size:.92rem; margin-bottom:24px; display:flex; align-items:center; gap:10px; font-weight:600; }
.lf-rm-status-pending { background:#fffbeb; color:#92400e; border:1px solid #fcd34d; }
.lf-rm-status-accepted { background:#ecfdf5; color:#065f46; border:1px solid #6ee7b7; }
.lf-rm-status-rejected { background:#fef2f2; color:#991b1b; border:1px solid #fca5a5; }
.lf-rm-section-title { font-size:.76rem; font-weight:800; color:#a16207; text-transform:uppercase; letter-spacing:.14em; padding-bottom:8px; border-bottom:1px solid #eadfbe; margin-bottom:16px; display:flex; align-items:center; gap:8px; }
.lf-rm-section-gap { margin-top:28px; }
.lf-rm-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.lf-rm-full { grid-column:1/-1; }
.lf-rm-field { display:flex; flex-direction:column; gap:7px; padding:16px 18px; background:#fff; border:1px solid #e7edf5; border-radius:16px; }
.lf-rm-label { font-size:.72rem; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:.08em; }
.lf-rm-value { font-size:1rem; color:#16233f; line-height:1.5; }
.lf-rm-docs-grid { display:grid; grid-template-columns:1fr 1fr; gap:18px; }
.lf-rm-doc-card { border:1px solid #e7edf5; border-radius:18px; padding:16px; background:#fff; display:flex; flex-direction:column; gap:12px; align-items:center; min-height:210px; }
.lf-rm-doc-label { font-size:.84rem; font-weight:800; color:#1e2d4d; align-self:flex-start; display:flex; align-items:center; gap:7px; }
.lf-rm-doc-img { width:100%; max-height:220px; object-fit:contain; border-radius:12px; border:1px solid #dee6f0; background:#f8fafc; cursor:pointer; }
.lf-rm-doc-file { display:flex; flex-direction:column; align-items:center; justify-content:center; gap:8px; text-decoration:none; color:#1e2d4d; font-size:.9rem; font-weight:700; padding:18px; border:2px dashed #d7e0ec; border-radius:14px; width:100%; min-height:130px; box-sizing:border-box; background:#fbfcfe; }
.lf-rm-doc-missing { font-size:.9rem; color:#dc3545; display:flex; align-items:center; gap:8px; padding:18px 0; min-height:130px; }
.lf-rm-message { background:#fffdf8; border:1px solid #f3e4b8; border-left:4px solid #b5860d; border-radius:14px; padding:15px 18px; font-size:.92rem; color:#5b6472; font-style:italic; margin-top:8px; line-height:1.65; }
.lf-rm-actions { display:flex; gap:12px; margin-top:28px; padding-top:22px; border-top:1px solid #e8edf5; }
.lf-rm-action-btn { font-size:.95rem; padding:12px 24px; border-radius:12px; }
.lf-app-confirm-modal { position: fixed; inset: 0; z-index: 9999; display: none; align-items: center; justify-content: center; padding: 18px; }
.lf-app-confirm-modal.open { display: flex; }
.lf-app-confirm-backdrop { position: absolute; inset: 0; background: rgba(15, 23, 42, .55); }
.lf-app-confirm-dialog { position: relative; z-index: 1; width: min(420px, 100%); background: #fff; border-radius: 8px; box-shadow: 0 24px 60px rgba(15, 23, 42, .22); padding: 24px; text-align: center; }
.lf-app-confirm-close { position: absolute; top: 10px; right: 12px; width: 30px; height: 30px; border: none; border-radius: 6px; background: #f1f5f9; color: #475569; font-size: 1.25rem; line-height: 1; cursor: pointer; }
.lf-app-confirm-icon { width: 62px; height: 62px; border-radius: 999px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; margin-bottom: 14px; }
.lf-app-confirm-icon.accept { background: #dcfce7; color: #16a34a; }
.lf-app-confirm-icon.reject { background: #fee2e2; color: #dc2626; }
.lf-app-confirm-dialog h2 { margin: 0 0 8px; color: #0f172a; font-size: 1.2rem; }
.lf-app-confirm-meta { color: #64748b; font-size: .92rem; line-height: 1.5; }
.lf-app-confirm-actions { display: flex; justify-content: center; gap: 12px; margin-top: 22px; }
.lf-app-confirm-cancel, .lf-app-confirm-submit { border: none; border-radius: 999px; padding: 10px 18px; font: inherit; font-weight: 600; cursor: pointer; }
.lf-app-confirm-cancel { background: #e2e8f0; color: #334155; }
.lf-app-confirm-submit.accept { background: #16a34a; color: #fff; }
.lf-app-confirm-submit.reject { background: #dc2626; color: #fff; }
.lf-app-confirm-submit:disabled { opacity: .65; cursor: wait; }
</style>
@endpush

<div class="lp-page-header">
    <div>
        <h1 class="lp-page-title">Firm Dashboard</h1>
        <p class="lp-page-sub">{{ $firm->firm_name }} &mdash; {{ now()->format('l, F j, Y') }}</p>
    </div>
</div>

@if(session('success'))
    <div class="lp-alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

{{-- ── STAT CARDS ── --}}
<div class="lp-stats-grid" style="grid-template-columns:repeat(6,1fr);">
    <div class="lp-stat-card">
        <div class="lp-stat-icon clients-icon"><i class="fas fa-users"></i></div>
        <div>
            <div class="lp-stat-num">{{ $teamCount }}</div>
            <div class="lp-stat-lbl">Team Lawyers</div>
        </div>
    </div>
    <div class="lp-stat-card" style="border-left:3px solid #16a34a;">
        <div class="lp-stat-icon" style="background:rgba(22,163,74,.1);color:#16a34a;"><i class="fas fa-circle"></i></div>
        <div>
            <div class="lp-stat-num">{{ $activeCount }}</div>
            <div class="lp-stat-lbl">Active Now</div>
        </div>
    </div>
    <div class="lp-stat-card">
        <div class="lp-stat-icon pending-icon"><i class="fas fa-user-clock"></i></div>
        <div>
            <div class="lp-stat-num">{{ $pendingApplications }}</div>
            <div class="lp-stat-lbl">Pending Apps</div>
        </div>
    </div>
    <div class="lp-stat-card">
        <div class="lp-stat-icon upcoming-icon"><i class="fas fa-calendar-check"></i></div>
        <div>
            <div class="lp-stat-num">{{ $totalConsultations }}</div>
            <div class="lp-stat-lbl">Total Consults</div>
        </div>
    </div>
    <div class="lp-stat-card">
        <div class="lp-stat-icon upcoming-icon"><i class="fas fa-calendar-alt"></i></div>
        <div>
            <div class="lp-stat-num" style="font-size:1rem;">₱{{ number_format($thisMonthEarned, 2) }}</div>
            <div class="lp-stat-lbl">This Month</div>
        </div>
    </div>
    <div class="lp-stat-card">
        <div class="lp-stat-icon earned-icon"><i class="fas fa-peso-sign"></i></div>
        <div>
            <div class="lp-stat-num" style="font-size:1rem;">₱{{ number_format($totalEarned, 2) }}</div>
            <div class="lp-stat-lbl">Total Earned</div>
        </div>
    </div>
</div>

{{-- ── CONSULTATION STATUS BREAKDOWN ── --}}
<div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:20px;">
    <div style="display:flex;align-items:center;gap:8px;background:#fff;border:1.5px solid #e8edf5;border-radius:10px;padding:10px 18px;box-shadow:0 1px 4px rgba(0,0,0,.04);">
        <i class="fas fa-hourglass-half" style="color:#f59e0b;"></i>
        <span style="font-size:1.1rem;font-weight:700;color:#1e2d4d;">{{ $consultationBreakdown['pending'] }}</span>
        <span style="font-size:.82rem;color:#6c757d;">Pending this month</span>
    </div>
    <div style="display:flex;align-items:center;gap:8px;background:#fff;border:1.5px solid #e8edf5;border-radius:10px;padding:10px 18px;box-shadow:0 1px 4px rgba(0,0,0,.04);">
        <i class="fas fa-calendar-check" style="color:#3b82f6;"></i>
        <span style="font-size:1.1rem;font-weight:700;color:#1e2d4d;">{{ $consultationBreakdown['upcoming'] }}</span>
        <span style="font-size:.82rem;color:#6c757d;">Upcoming sessions</span>
    </div>
    <div style="display:flex;align-items:center;gap:8px;background:#fff;border:1.5px solid #e8edf5;border-radius:10px;padding:10px 18px;box-shadow:0 1px 4px rgba(0,0,0,.04);">
        <i class="fas fa-check-circle" style="color:#16a34a;"></i>
        <span style="font-size:1.1rem;font-weight:700;color:#1e2d4d;">{{ $consultationBreakdown['completed'] }}</span>
        <span style="font-size:.82rem;color:#6c757d;">Completed this month</span>
    </div>
</div>

{{-- ── TODAY'S SESSIONS ── --}}
@if($todaySessions->count() > 0)
<div class="lp-card" style="margin-bottom:22px;border-top:4px solid #1e2d4d;">
    <div class="lp-card-header" style="background:linear-gradient(135deg,#1e2d4d,#2a3f6f);border-radius:0;padding:16px 22px;margin:-1px -1px 0;">
        <div style="display:flex;align-items:center;gap:10px;">
            <i class="fas fa-calendar-day" style="color:#fff;font-size:1rem;"></i>
            <h2 style="color:#fff;font-size:.95rem;font-weight:700;margin:0;">Today's Sessions</h2>
            <span style="background:#b5860d;color:#fff;font-size:.72rem;font-weight:700;padding:2px 9px;border-radius:20px;">
                {{ $todaySessions->count() }} session{{ $todaySessions->count() > 1 ? 's' : '' }}
            </span>
        </div>
        <a href="{{ route('lawfirm.consultations') }}" style="font-size:.8rem;color:rgba(255,255,255,.8);font-weight:600;text-decoration:none;padding:5px 12px;border:1px solid rgba(255,255,255,.3);border-radius:7px;">
            View All
        </a>
    </div>
    <div style="overflow-x:auto;">
        <table class="lp-table">
            <thead>
                <tr><th>Time</th><th>Client</th><th>Lawyer</th><th>Type</th><th>Duration</th></tr>
            </thead>
            <tbody>
                @foreach($todaySessions as $s)
                <tr>
                    <td style="font-weight:700;color:#1e2d4d;">{{ \Carbon\Carbon::parse($s->scheduled_at)->format('g:i A') }}</td>
                    <td>{{ $s->client->name }}</td>
                    <td>{{ $s->lawyer->name }}</td>
                    <td><span class="lp-type-badge {{ $s->type }}">{{ ucfirst($s->type) }}</span></td>
                    <td>{{ $s->duration_label }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<div class="lp-two-col">
    {{-- PENDING APPLICATIONS --}}
    <div class="lp-card">
        <div class="lp-card-header">
            <h2 class="lp-card-title"><i class="fas fa-inbox"></i> Pending Applications
                @if($pendingApplications > 0)<span class="lp-count-badge">{{ $pendingApplications }}</span>@endif
            </h2>
            <a href="{{ route('lawfirm.lawyers') }}#applications" class="lp-view-all">View All</a>
        </div>
        @forelse($recentApplications->take(5) as $app)
        <div style="display:flex;align-items:center;gap:14px;padding:14px 20px;border-bottom:1px solid #f0f2f5;transition:background .15s;" onmouseover="this.style.background='#fafbfd'" onmouseout="this.style.background='transparent'">
            {{-- Avatar --}}
            <div style="width:46px;height:46px;border-radius:50%;background:linear-gradient(135deg,#1e2d4d,#2a3f6f);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1rem;font-weight:700;flex-shrink:0;overflow:hidden;">
                @if($app->lawyer->avatar_url)
                    <img src="{{ $app->lawyer->avatar_url }}" style="width:100%;height:100%;object-fit:cover;display:block;" alt="{{ $app->lawyer->name }}">
                @else
                    {{ strtoupper(substr($app->lawyer->name, 0, 1)) }}
                @endif
            </div>
            {{-- Info --}}
            <div style="flex:1;min-width:0;">
                <div style="font-size:.92rem;font-weight:700;color:#1e2d4d;margin-bottom:3px;">{{ $app->lawyer->name }}</div>
                <div style="display:flex;flex-wrap:wrap;gap:8px;font-size:.78rem;color:#6c757d;margin-bottom:4px;">
                    <span style="background:#eef2ff;color:#3730a3;padding:2px 8px;border-radius:20px;font-weight:600;">
                        <i class="fas fa-gavel" style="margin-right:3px;"></i>{{ $app->lawyer->lawyerProfile->specialty ?? 'Attorney' }}
                    </span>
                    <span><i class="fas fa-briefcase" style="margin-right:3px;color:#9ca3af;"></i>{{ $app->lawyer->lawyerProfile->experience_years ?? 0 }} yrs exp</span>
                    <span><i class="fas fa-clock" style="margin-right:3px;color:#9ca3af;"></i>{{ $app->created_at->diffForHumans() }}</span>
                </div>
                @if($app->message)
                <div style="font-size:.78rem;color:#6c757d;font-style:italic;background:#f8f9fa;padding:4px 10px;border-radius:6px;border-left:2px solid #d1d5db;margin-top:2px;">
                    "{{ \Illuminate\Support\Str::limit($app->message, 80) }}"
                </div>
                @endif
                {{-- Review Docs button --}}
                @php $lp = $app->lawyer->lawyerProfile; @endphp
                <div style="margin-top:6px;">
                    <button class="lp-btn-review" onclick="openReviewModal({{ $app->id }})">
                        <i class="fas fa-file-alt"></i> Review Docs
                    </button>
                </div>
            </div>
            {{-- Actions --}}
            <div style="display:flex;gap:6px;flex-shrink:0;">
                <form method="POST" action="{{ route('lawfirm.lawyers.accept', $app->id) }}">
                    @csrf
                    <button type="button" class="js-lawfirm-app-confirm" data-action="accept" data-name="{{ $app->lawyer->name }}" style="display:inline-flex;align-items:center;gap:5px;padding:7px 14px;background:#16a34a;color:#fff;border:none;border-radius:8px;font-size:.8rem;font-weight:600;cursor:pointer;font-family:inherit;transition:background .15s;"
                        onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
                        <i class="fas fa-check"></i> Accept
                    </button>
                </form>
                <form method="POST" action="{{ route('lawfirm.lawyers.reject', $app->id) }}">
                    @csrf
                    <button type="button" class="js-lawfirm-app-confirm" data-action="reject" data-name="{{ $app->lawyer->name }}"
                        style="display:inline-flex;align-items:center;gap:5px;padding:7px 14px;background:#fff;color:#dc2626;border:1.5px solid #fca5a5;border-radius:8px;font-size:.8rem;font-weight:600;cursor:pointer;font-family:inherit;transition:all .15s;"
                        onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='#fff'">
                        <i class="fas fa-times"></i> Reject
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="lp-empty-sm"><i class="fas fa-inbox"></i> No pending applications</div>
        @endforelse
    </div>

    {{-- Review Modals --}}
    @foreach($recentApplications as $app)
    @php $lp = $app->lawyer->lawyerProfile; @endphp
    <div id="reviewModal-{{ $app->id }}" class="lf-review-overlay" style="display:none;" onclick="if(event.target===this)closeReviewModal({{ $app->id }})">
        <div class="lf-review-modal">
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
                    </div>
                </div>
                <button class="lf-rm-close" onclick="closeReviewModal({{ $app->id }})">&times;</button>
            </div>
            <div class="lf-rm-body">
                <div class="lf-rm-status-bar lf-rm-status-{{ $app->status }}">
                    <i class="fas fa-hourglass-half"></i>
                    Application Status: <strong>{{ ucfirst($app->status) }}</strong>
                </div>
                @if($lp)
                <div class="lf-rm-section-title"><i class="fas fa-id-badge"></i> Professional Details</div>
                <div class="lf-rm-grid">
                    <div class="lf-rm-field"><span class="lf-rm-label">Specialty</span><span class="lf-rm-value">{{ $lp->specialty ?: '—' }}</span></div>
                    <div class="lf-rm-field"><span class="lf-rm-label">Experience</span><span class="lf-rm-value">{{ $lp->experience_years }} yrs</span></div>
                    <div class="lf-rm-field"><span class="lf-rm-label">Hourly Rate</span><span class="lf-rm-value">₱{{ number_format($lp->hourly_rate, 0) }}/hr</span></div>
                    <div class="lf-rm-field"><span class="lf-rm-label">Location</span><span class="lf-rm-value">{{ $lp->location ?: '—' }}</span></div>
                    <div class="lf-rm-field"><span class="lf-rm-label">IBP Certified</span><span class="lf-rm-value">
                        @if($lp->is_certified)<span style="color:#1a7a3c;font-weight:600;"><i class="fas fa-certificate"></i> Certified</span>@else<span style="color:#888;">Not yet certified</span>@endif
                    </span></div>
                    @if($lp->bio)
                    <div class="lf-rm-field lf-rm-full"><span class="lf-rm-label">Bio</span><span class="lf-rm-value">{{ $lp->bio }}</span></div>
                    @endif
                </div>
                <div class="lf-rm-section-title lf-rm-section-gap"><i class="fas fa-file-alt"></i> Submitted Verification Documents</div>
                <div class="lf-rm-docs-grid">
                    <div class="lf-rm-doc-card">
                        <div class="lf-rm-doc-label"><i class="fas fa-id-card"></i> Government ID</div>
                        @if($lp->government_id_doc)
                            @php
                                $ext = strtolower(pathinfo($lp->government_id_doc, PATHINFO_EXTENSION));
                                $govUrl = $lp->documentUrl('government_id');
                            @endphp
                            @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                                <a href="{{ $govUrl }}" target="_blank"><img src="{{ $govUrl }}" class="lf-rm-doc-img" alt="Gov ID"></a>
                            @else
                                <a href="{{ $govUrl }}" target="_blank" class="lf-rm-doc-file"><i class="fas fa-file-pdf" style="font-size:2rem;color:#dc3545;"></i><span>View PDF</span></a>
                            @endif
                        @else
                            <div class="lf-rm-doc-missing"><i class="fas fa-exclamation-triangle"></i> Not submitted</div>
                        @endif
                    </div>
                    <div class="lf-rm-doc-card">
                        <div class="lf-rm-doc-label"><i class="fas fa-file-certificate"></i> IBP ID</div>
                        @if($lp->ibp_id_doc)
                            @php
                                $ext = strtolower(pathinfo($lp->ibp_id_doc, PATHINFO_EXTENSION));
                                $ibpUrl = $lp->documentUrl('ibp_id');
                            @endphp
                            @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                                <a href="{{ $ibpUrl }}" target="_blank"><img src="{{ $ibpUrl }}" class="lf-rm-doc-img" alt="IBP ID"></a>
                            @else
                                <a href="{{ $ibpUrl }}" target="_blank" class="lf-rm-doc-file"><i class="fas fa-file-pdf" style="font-size:2rem;color:#dc3545;"></i><span>View PDF</span></a>
                            @endif
                        @else
                            <div class="lf-rm-doc-missing"><i class="fas fa-exclamation-triangle"></i> Not submitted</div>
                        @endif
                    </div>
                </div>
                @endif
                @if($app->message && $app->message !== 'Applied during registration.')
                <div class="lf-rm-section-title lf-rm-section-gap"><i class="fas fa-comment-alt"></i> Applicant's Message</div>
                <div class="lf-rm-message">"{{ $app->message }}"</div>
                @endif
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
    @endforeach

    {{-- TEAM OVERVIEW --}}
    <div class="lp-card">
        <div class="lp-card-header">
            <h2 class="lp-card-title"><i class="fas fa-users"></i> Team Members
                <span style="font-size:.78rem;font-weight:400;color:#6c757d;margin-left:6px;">{{ $teamCount }} total</span>
            </h2>
            <a href="{{ route('lawfirm.lawyers') }}" class="lp-view-all">Manage Team</a>
        </div>
        @forelse($teamMembers as $member)
        <div class="lp-request-item">
            <div style="width:40px;height:40px;border-radius:50%;background:#1e2d4d;color:#fff;display:flex;align-items:center;justify-content:center;font-size:.9rem;font-weight:700;flex-shrink:0;overflow:hidden;">
                @if($member->user->avatar_url)
                    <img src="{{ $member->user->avatar_url }}" style="width:100%;height:100%;object-fit:cover;display:block;" alt="{{ $member->user->name }}">
                @else
                    {{ strtoupper(substr($member->user->name, 0, 1)) }}
                @endif
            </div>
            <div class="lp-req-info">
                <div class="lp-req-name">{{ $member->user->name }}</div>
                <div class="lp-req-meta">
                    <span><i class="fas fa-gavel"></i> {{ $member->specialty }}</span>
                    <span><i class="fas fa-clock"></i> {{ $member->experience_years }} yrs</span>
                </div>
            </div>
            <div>
                <span class="lp-status-badge {{ $member->currentStatusClass() }}">{{ $member->currentStatusLabel() }}</span>
            </div>
        </div>
        @empty
        <div class="lp-empty-sm"><i class="fas fa-users"></i> No team members yet. Accept lawyer applications to build your team.</div>
        @endforelse
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

{{-- RECENT CONSULTATIONS --}}
<div class="lp-card" style="margin-top:24px;">
    <div class="lp-card-header">
        <h2 class="lp-card-title"><i class="fas fa-calendar-alt"></i> Recent Consultations</h2>
        <a href="{{ route('lawfirm.consultations') }}" class="lp-view-all">View All</a>
    </div>
    @if($recentConsultations->isEmpty())
        <div class="lp-empty-sm"><i class="fas fa-calendar"></i> No consultations yet</div>
    @else
    <div style="overflow-x:auto;">
        <table class="lp-table">
            <thead>
                <tr><th>Client</th><th>Lawyer</th><th>Type</th><th>Scheduled</th><th>Status</th><th>Amount</th></tr>
            </thead>
            <tbody>
                @foreach($recentConsultations as $c)
                <tr>
                    <td>{{ $c->client->name }}</td>
                    <td>{{ $c->lawyer->name }}</td>
                    <td><span class="lp-type-badge {{ $c->type }}">{{ ucfirst($c->type) }}</span></td>
                    <td>{{ \Carbon\Carbon::parse($c->scheduled_at)->format('M j, Y g:i A') }}</td>
                    <td><span class="lp-status-badge {{ $c->status }}">{{ ucfirst($c->status) }}</span></td>
                    <td>₱{{ number_format($c->price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- MONTHLY EARNINGS CHART --}}
<div class="lp-card" style="margin-top:24px;">
    <div class="lp-card-header">
        <h2 class="lp-card-title"><i class="fas fa-chart-line"></i> Monthly Earnings</h2>
        <div style="display:flex;align-items:center;gap:10px;">
            <select id="earningsYearSelect" onchange="switchEarningsYear(this.value)"
                style="height:34px;padding:0 10px;border:1px solid #d9e2ef;border-radius:8px;font-size:.82rem;color:#1e2d4d;background:#fff;cursor:pointer;">
                <option value="rolling" selected>Last 12 months</option>
                @for($y = now()->year; $y >= now()->year - 4; $y--)
                <option value="{{ $y }}">{{ $y }}</option>
                @endfor
            </select>
        </div>
    </div>
    @php $hasEarnings = $monthlyEarnings->sum('total') > 0; @endphp
    @if(!$hasEarnings)
    <div style="padding:40px 24px;text-align:center;color:#adb5bd;">
        <i class="fas fa-chart-line" style="font-size:2.5rem;margin-bottom:12px;display:block;opacity:.3;"></i>
        <p style="font-size:.9rem;font-weight:600;color:#6c757d;margin:0 0 4px;">No earnings data yet.</p>
        <p style="font-size:.82rem;margin:0;">Earnings will appear here once your lawyers complete consultations.</p>
    </div>
    @else
    <div style="padding:12px 20px 6px;display:flex;gap:20px;flex-wrap:wrap;" id="earningsInsights">
        <div style="font-size:.82rem;color:#6c757d;">
            <i class="fas fa-trophy" style="color:#f59e0b;margin-right:4px;"></i>
            <strong>Highest:</strong> <span id="insightHighest">{{ $highestMonth['month'] }} — ₱{{ number_format($highestMonth['total'], 2) }}</span>
        </div>
        <div style="font-size:.82rem;color:#6c757d;">
            <i class="fas fa-calendar" style="color:#3b82f6;margin-right:4px;"></i>
            <strong><span id="insightYearLabel">This year</span>:</strong> <span id="insightTotal">₱{{ number_format($totalThisYear, 2) }}</span>
        </div>
    </div>
    <div style="padding:8px 24px 24px;">
        <div style="position:relative;height:280px;">
            <canvas id="earningsChart"></canvas>
        </div>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
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
        document.querySelectorAll('.lf-review-overlay').forEach(function(el) {
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
let pendingLawfirmAppForm = null;

function openLawfirmAppModal(button) {
    const action = button.dataset.action;
    const name = button.dataset.name || 'this applicant';
    const isAccept = action === 'accept';

    pendingLawfirmAppForm = button.closest('form');
    lawfirmAppModalTitle.textContent = isAccept ? 'Accept Application?' : 'Reject Application?';
    lawfirmAppModalMeta.textContent = isAccept
        ? 'You are about to accept ' + name + ' into the firm.'
        : 'You are about to reject ' + name + '\'s application.';
    lawfirmAppModalIcon.innerHTML = isAccept ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times"></i>';
    lawfirmAppModalIcon.className = isAccept ? 'lf-app-confirm-icon accept' : 'lf-app-confirm-icon reject';
    lawfirmAppModalSubmit.textContent = isAccept ? 'Accept Application' : 'Reject Application';
    lawfirmAppModalSubmit.className = isAccept ? 'lf-app-confirm-submit accept' : 'lf-app-confirm-submit reject';
    lawfirmAppModal.classList.add('open');
    lawfirmAppModal.setAttribute('aria-hidden', 'false');
    lawfirmAppModalSubmit.disabled = false;
    lawfirmAppModalSubmit.focus();
}

function closeLawfirmAppModal() {
    lawfirmAppModal.classList.remove('open');
    lawfirmAppModal.setAttribute('aria-hidden', 'true');
    pendingLawfirmAppForm = null;
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
    if (pendingLawfirmAppForm) {
        lawfirmAppModalSubmit.disabled = true;
        pendingLawfirmAppForm.submit();
    }
});

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape' && lawfirmAppModal.classList.contains('open')) {
        closeLawfirmAppModal();
    }
});
@if($monthlyEarnings->sum('total') > 0)
(function () {
    const rollingLabels = @json($monthlyEarnings->pluck('month'));
    const rollingData   = @json($monthlyEarnings->pluck('total')).map(v => parseFloat(v) || 0);

    // Per-year data keyed by year
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
</script>
@endpush
