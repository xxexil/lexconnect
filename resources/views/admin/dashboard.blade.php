@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('content')

<style>
.ad-stat-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 18px; margin-bottom: 28px; }
@media(max-width:1100px){ .ad-stat-grid { grid-template-columns: repeat(2,1fr); } }
.ad-stat-card {
    background: #fff; border-radius: 16px; padding: 22px 24px;
    display: flex; align-items: center; gap: 18px;
    box-shadow: 0 1px 4px rgba(0,0,0,.05); border: 1px solid #eef0f6;
}
.ad-stat-icon {
    width: 52px; height: 52px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.25rem; flex-shrink: 0;
}
.si-purple { background: #ede9fe; color: #7c3aed; }
.si-blue   { background: #dbeafe; color: #1d4ed8; }
.si-green  { background: #d1fae5; color: #059669; }
.si-amber  { background: #fef3c7; color: #d97706; }
.si-red    { background: #fee2e2; color: #dc2626; }
.si-slate  { background: #f1f5f9; color: #475569; }
.ad-stat-num { font-size: 1.9rem; font-weight: 800; color: #1a1a2e; line-height: 1; }
.ad-stat-lbl { font-size: .8rem; color: #6b7280; margin-top: 4px; font-weight: 500; }

.ad-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 22px; }
@media(max-width:900px){ .ad-two-col { grid-template-columns: 1fr; } }

.ad-card { background: #fff; border-radius: 16px; border: 1px solid #eef0f6; box-shadow: 0 1px 4px rgba(0,0,0,.04); overflow: hidden; }
.ad-card-header { padding: 18px 22px; border-bottom: 1px solid #f3f4f8; display: flex; align-items: center; justify-content: space-between; }
.ad-card-title  { font-size: .97rem; font-weight: 700; color: #1a1a2e; display: flex; align-items: center; gap: 8px; }

.ad-consult-row {
    display: flex; align-items: center; gap: 12px;
    padding: 12px 22px; border-bottom: 1px solid #f3f4f8; font-size: .86rem;
}
.ad-consult-row:last-child { border-bottom: none; }
.ad-consult-code { font-weight: 700; color: #1a1a2e; min-width: 110px; font-size: .8rem; }
.ad-consult-names { flex: 1; }
.ad-consult-client { font-weight: 600; color: #374151; }
.ad-consult-lawyer { color: #6b7280; font-size: .8rem; }
.ad-pill {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: .72rem; font-weight: 600; padding: 3px 10px; border-radius: 20px;
}
.pill-pending   { background: #fef9c3; color: #854d0e; }
.pill-upcoming  { background: #dbeafe; color: #1d4ed8; }
.pill-completed { background: #d1fae5; color: #065f46; }
.pill-cancelled { background: #fee2e2; color: #991b1b; }

.ad-user-row {
    display: flex; align-items: center; gap: 12px;
    padding: 11px 22px; border-bottom: 1px solid #f3f4f8; font-size: .86rem;
}
.ad-user-row:last-child { border-bottom: none; }
.ad-user-init {
    width: 36px; height: 36px; border-radius: 50%;
    background: #ede9fe; color: #7c3aed;
    display: flex; align-items: center; justify-content: center;
    font-size: .8rem; font-weight: 700; flex-shrink: 0;
}
.ad-user-name  { font-weight: 600; color: #1a1a2e; }
.ad-user-email { color: #9ca3af; font-size: .78rem; }

.pill-client   { background: #ede9fe; color: #6d28d9; }
.pill-lawyer   { background: #dbeafe; color: #1e40af; }
.pill-law_firm { background: #d1fae5; color: #065f46; }
.pill-admin    { background: #fee2e2; color: #991b1b; }

.ad-consult-status-bar { display: grid; grid-template-columns: repeat(4,1fr); gap: 1px; background: #eef0f6; margin-bottom: 28px; border-radius: 14px; overflow: hidden; }
.ad-status-block { background: #fff; padding: 20px 22px; text-align: center; }
.ad-status-num   { font-size: 1.6rem; font-weight: 800; }
.ad-status-lbl   { font-size: .78rem; color: #6b7280; margin-top: 3px; font-weight: 500; }
</style>

{{-- Summary stat cards --}}
<div class="ad-stat-grid">
    <div class="ad-stat-card">
        <div class="ad-stat-icon si-purple"><i class="fas fa-users"></i></div>
        <div>
            <div class="ad-stat-num">{{ $totalClients }}</div>
            <div class="ad-stat-lbl">Clients</div>
        </div>
    </div>
    <div class="ad-stat-card">
        <div class="ad-stat-icon si-blue"><i class="fas fa-gavel"></i></div>
        <div>
            <div class="ad-stat-num">{{ $totalLawyers }}</div>
            <div class="ad-stat-lbl">Lawyers</div>
        </div>
    </div>
    <div class="ad-stat-card">
        <div class="ad-stat-icon si-green"><i class="fas fa-building-columns"></i></div>
        <div>
            <div class="ad-stat-num">{{ $totalFirms }}</div>
            <div class="ad-stat-lbl">Law Firms</div>
        </div>
    </div>
    <div class="ad-stat-card">
        <div class="ad-stat-icon si-amber"><i class="fas fa-calendar-check"></i></div>
        <div>
            <div class="ad-stat-num">{{ $totalConsults }}</div>
            <div class="ad-stat-lbl">Total Consultations</div>
        </div>
    </div>
</div>

{{-- Consultation status bar --}}
<div class="ad-consult-status-bar">
    <div class="ad-status-block">
        <div class="ad-status-num" style="color:#d97706;">{{ $pendingConsults }}</div>
        <div class="ad-status-lbl">Pending</div>
    </div>
    <div class="ad-status-block">
        <div class="ad-status-num" style="color:#1d4ed8;">{{ $upcomingConsults }}</div>
        <div class="ad-status-lbl">Upcoming</div>
    </div>
    <div class="ad-status-block">
        <div class="ad-status-num" style="color:#059669;">{{ $completedConsults }}</div>
        <div class="ad-status-lbl">Completed</div>
    </div>
    <div class="ad-status-block">
        <div class="ad-status-num" style="color:#dc2626;">{{ $cancelledConsults }}</div>
        <div class="ad-status-lbl">Cancelled</div>
    </div>
</div>

{{-- Extra stats --}}
<div class="ad-stat-grid" style="margin-bottom:28px;">
    <div class="ad-stat-card">
        <div class="ad-stat-icon si-red"><i class="fas fa-clock"></i></div>
        <div>
            <div class="ad-stat-num">{{ $unverifiedFirms }}</div>
            <div class="ad-stat-lbl">Unverified Firms</div>
        </div>
    </div>
    <div class="ad-stat-card">
        <div class="ad-stat-icon si-slate"><i class="fas fa-certificate"></i></div>
        <div>
            <div class="ad-stat-num">{{ $certifiedLawyers }}</div>
            <div class="ad-stat-lbl">Certified Lawyers</div>
        </div>
    </div>
</div>

<div class="ad-two-col">
    {{-- Recent Consultations --}}
    <div class="ad-card">
        <div class="ad-card-header">
            <span class="ad-card-title"><i class="fas fa-calendar-alt" style="color:#7c3aed;"></i> Recent Consultations</span>
            <a href="{{ route('admin.consultations') }}" style="font-size:.8rem;color:#7c3aed;text-decoration:none;font-weight:600;">View all →</a>
        </div>
        @forelse($recentConsultations as $c)
        <div class="ad-consult-row">
            <div class="ad-consult-code">{{ $c->code }}</div>
            <div class="ad-consult-names">
                <div class="ad-consult-client">{{ $c->client->name ?? '—' }}</div>
                <div class="ad-consult-lawyer"><i class="fas fa-gavel fa-xs"></i> {{ $c->lawyer->name ?? '—' }}</div>
            </div>
            <span class="ad-pill pill-{{ $c->status }}">{{ ucfirst($c->status) }}</span>
        </div>
        @empty
        <div style="padding:28px;text-align:center;color:#9ca3af;font-size:.88rem;">No consultations yet.</div>
        @endforelse
    </div>

    {{-- Recent Users --}}
    <div class="ad-card">
        <div class="ad-card-header">
            <span class="ad-card-title"><i class="fas fa-user-plus" style="color:#7c3aed;"></i> Recent Users</span>
            <a href="{{ route('admin.users') }}" style="font-size:.8rem;color:#7c3aed;text-decoration:none;font-weight:600;">View all →</a>
        </div>
        @forelse($recentUsers as $u)
        <div class="ad-user-row">
            <div class="ad-user-init">{{ strtoupper(substr($u->name,0,1)) }}</div>
            <div style="flex:1;">
                <div class="ad-user-name">{{ $u->name }}</div>
                <div class="ad-user-email">{{ $u->email }}</div>
            </div>
            <span class="ad-pill pill-{{ $u->role }}">{{ ucfirst(str_replace('_',' ',$u->role)) }}</span>
        </div>
        @empty
        <div style="padding:28px;text-align:center;color:#9ca3af;font-size:.88rem;">No users yet.</div>
        @endforelse
    </div>
</div>

@endsection
