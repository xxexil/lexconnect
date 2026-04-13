@extends('layouts.lawfirm')
@section('title', 'Dashboard')
@section('content')

<div class="lp-page-header">
    <div>
        <h1 class="lp-page-title">Firm Dashboard</h1>
        <p class="lp-page-sub">{{ $firm->firm_name }} &mdash; {{ now()->format('l, F j, Y') }}</p>
    </div>
</div>

@if(session('success'))
    <div class="lp-alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

{{-- STAT CARDS --}}
<div class="lp-stats-grid">
    <div class="lp-stat-card">
        <div class="lp-stat-icon clients-icon"><i class="fas fa-users"></i></div>
        <div>
            <div class="lp-stat-num">{{ $teamCount }}</div>
            <div class="lp-stat-lbl">Team Lawyers</div>
        </div>
    </div>
    <div class="lp-stat-card">
        <div class="lp-stat-icon pending-icon"><i class="fas fa-user-clock"></i></div>
        <div>
            <div class="lp-stat-num">{{ $pendingApplications }}</div>
            <div class="lp-stat-lbl">Pending Applications</div>
        </div>
    </div>
    <div class="lp-stat-card">
        <div class="lp-stat-icon upcoming-icon"><i class="fas fa-calendar-check"></i></div>
        <div>
            <div class="lp-stat-num">{{ $totalConsultations }}</div>
            <div class="lp-stat-lbl">Total Consultations</div>
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

<div class="lp-two-col">
    {{-- PENDING APPLICATIONS --}}
    <div class="lp-card">
        <div class="lp-card-header">
            <h2 class="lp-card-title"><i class="fas fa-inbox"></i> Pending Applications
                @if($pendingApplications > 0)<span class="lp-count-badge">{{ $pendingApplications }}</span>@endif
            </h2>
            <a href="{{ route('lawfirm.lawyers') }}" class="lp-view-all">View All</a>
        </div>
        @forelse($recentApplications as $app)
        <div class="lp-request-item">
            <div class="lp-req-avatar">{{ strtoupper(substr($app->lawyer->name, 0, 1)) }}</div>
            <div class="lp-req-info">
                <div class="lp-req-name">{{ $app->lawyer->name }}</div>
                <div class="lp-req-meta">
                    <span><i class="fas fa-gavel"></i> {{ $app->lawyer->lawyerProfile->specialty ?? 'Attorney' }}</span>
                    <span><i class="fas fa-briefcase"></i> {{ $app->lawyer->lawyerProfile->experience_years ?? 0 }} yrs exp</span>
                    <span><i class="fas fa-clock"></i> {{ $app->created_at->diffForHumans() }}</span>
                </div>
                @if($app->message)
                <div class="lp-req-notes">"{{ \Illuminate\Support\Str::limit($app->message, 80) }}"</div>
                @endif
            </div>
            <div class="lp-req-actions">
                <form method="POST" action="{{ route('lawfirm.lawyers.accept', $app->id) }}" style="display:inline;">
                    @csrf
                    <button class="lp-btn-accept"><i class="fas fa-check"></i> Accept</button>
                </form>
                <form method="POST" action="{{ route('lawfirm.lawyers.reject', $app->id) }}" style="display:inline;">
                    @csrf
                    <button class="lp-btn-decline" onclick="return confirm('Reject this application?')"><i class="fas fa-times"></i> Reject</button>
                </form>
            </div>
        </div>
        @empty
        <div class="lp-empty-sm"><i class="fas fa-inbox"></i> No pending applications</div>
        @endforelse
    </div>

    {{-- TEAM OVERVIEW --}}
    <div class="lp-card">
        <div class="lp-card-header">
            <h2 class="lp-card-title"><i class="fas fa-users"></i> Team Members</h2>
            <a href="{{ route('lawfirm.lawyers') }}" class="lp-view-all">Manage Team</a>
        </div>
        @forelse($teamMembers as $member)
        <div class="lp-request-item">
            <div class="lp-req-avatar">{{ strtoupper(substr($member->user->name, 0, 1)) }}</div>
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

@endsection
