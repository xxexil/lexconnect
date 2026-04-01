@extends('layouts.admin')
@section('title', 'All Users')
@section('page-title', 'All Users')
@section('content')

<style>
.ad-filter-bar {
    background: #fff; border-radius: 14px; padding: 16px 22px;
    display: flex; gap: 12px; flex-wrap: wrap; align-items: center;
    margin-bottom: 22px; border: 1px solid #eef0f6;
    box-shadow: 0 1px 4px rgba(0,0,0,.04);
}
.ad-filter-bar input, .ad-filter-bar select {
    padding: 9px 14px; border: 1.5px solid #e5e7eb; border-radius: 9px;
    font-size: .875rem; font-family: inherit; flex: 1; min-width: 160px;
}
.ad-filter-bar input:focus, .ad-filter-bar select:focus { outline: none; border-color: #7c3aed; }
.ad-filter-btn {
    padding: 9px 20px; background: #7c3aed; color: #fff;
    border: none; border-radius: 9px; font-size: .875rem; font-weight: 600;
    cursor: pointer; font-family: inherit;
}
.ad-filter-btn:hover { background: #6d28d9; }
.ad-filter-clear { font-size: .82rem; color: #7c3aed; text-decoration: none; font-weight: 500; }

.ad-stat-row { display: flex; gap: 14px; margin-bottom: 22px; flex-wrap: wrap; }
.ad-mini-stat {
    background: #fff; border-radius: 12px; padding: 14px 20px;
    border: 1px solid #eef0f6; display: flex; align-items: center; gap: 12px;
    flex: 1; min-width: 130px;
}
.ad-mini-stat-icon { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: .95rem; flex-shrink: 0; }
.ad-mini-num { font-size: 1.4rem; font-weight: 800; color: #1a1a2e; line-height: 1; }
.ad-mini-lbl { font-size: .75rem; color: #6b7280; margin-top: 2px; }

.ad-table-card { background: #fff; border-radius: 16px; border: 1px solid #eef0f6; overflow: hidden; }
.ad-table { width: 100%; border-collapse: collapse; }
.ad-table th { padding: 12px 18px; text-align: left; font-size: .72rem; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: .6px; border-bottom: 1px solid #f3f4f8; background: #fafbff; }
.ad-table td { padding: 13px 18px; border-bottom: 1px solid #f3f4f8; font-size: .875rem; color: #374151; vertical-align: middle; }
.ad-table tr:last-child td { border-bottom: none; }
.ad-table tr:hover td { background: #fafbff; }

.ad-user-cell { display: flex; align-items: center; gap: 10px; }
.ad-user-init { width: 36px; height: 36px; border-radius: 50%; background: #ede9fe; color: #7c3aed; display: flex; align-items: center; justify-content: center; font-size: .8rem; font-weight: 700; flex-shrink: 0; }

.ad-pill { display: inline-flex; align-items: center; font-size: .72rem; font-weight: 600; padding: 3px 10px; border-radius: 20px; }
.pill-client   { background: #ede9fe; color: #6d28d9; }
.pill-lawyer   { background: #dbeafe; color: #1e40af; }
.pill-law_firm { background: #d1fae5; color: #065f46; }
.pill-admin    { background: #fee2e2; color: #991b1b; }

.ad-del-btn {
    padding: 5px 12px; background: #fff; color: #dc2626;
    border: 1.5px solid #fca5a5; border-radius: 7px; font-size: .78rem;
    cursor: pointer; font-family: inherit; font-weight: 600;
    transition: background .15s;
}
.ad-del-btn:hover { background: #fef2f2; }

.ad-alert-success {
    background: #ecfdf5; border: 1px solid #6ee7b7; color: #065f46;
    border-radius: 12px; padding: 12px 18px; margin-bottom: 20px;
    display: flex; align-items: center; gap: 8px; font-size: .9rem;
}
</style>

@if(session('success'))
<div class="ad-alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

<div class="ad-stat-row">
    <div class="ad-mini-stat">
        <div class="ad-mini-stat-icon" style="background:#ede9fe;color:#7c3aed;"><i class="fas fa-user"></i></div>
        <div><div class="ad-mini-num">{{ $totalClients }}</div><div class="ad-mini-lbl">Clients</div></div>
    </div>
    <div class="ad-mini-stat">
        <div class="ad-mini-stat-icon" style="background:#dbeafe;color:#1d4ed8;"><i class="fas fa-gavel"></i></div>
        <div><div class="ad-mini-num">{{ $totalLawyers }}</div><div class="ad-mini-lbl">Lawyers</div></div>
    </div>
    <div class="ad-mini-stat">
        <div class="ad-mini-stat-icon" style="background:#d1fae5;color:#059669;"><i class="fas fa-building-columns"></i></div>
        <div><div class="ad-mini-num">{{ $totalFirms }}</div><div class="ad-mini-lbl">Law Firms</div></div>
    </div>
</div>

<form method="GET" action="{{ route('admin.users') }}">
    <div class="ad-filter-bar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or email…">
        <select name="role">
            <option value="">All Roles</option>
            <option value="client"   {{ request('role')==='client'   ? 'selected' : '' }}>Client</option>
            <option value="lawyer"   {{ request('role')==='lawyer'   ? 'selected' : '' }}>Lawyer</option>
            <option value="law_firm" {{ request('role')==='law_firm' ? 'selected' : '' }}>Law Firm</option>
        </select>
        <button type="submit" class="ad-filter-btn"><i class="fas fa-search"></i> Filter</button>
        @if(request()->anyFilled(['search','role']))
        <a href="{{ route('admin.users') }}" class="ad-filter-clear">Clear</a>
        @endif
    </div>
</form>

<div class="ad-table-card">
    <table class="ad-table">
        <thead>
            <tr>
                <th>User</th>
                <th>Email</th>
                <th>Role</th>
                <th>Joined</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $u)
            <tr>
                <td>
                    <div class="ad-user-cell">
                        <div class="ad-user-init">{{ strtoupper(substr($u->name,0,1)) }}</div>
                        <span style="font-weight:600;color:#1a1a2e;">{{ $u->name }}</span>
                    </div>
                </td>
                <td style="color:#6b7280;">{{ $u->email }}</td>
                <td><span class="ad-pill pill-{{ $u->role }}">{{ ucfirst(str_replace('_',' ',$u->role)) }}</span></td>
                <td style="color:#9ca3af;font-size:.82rem;">{{ $u->created_at->format('M d, Y') }}</td>
                <td>
                    <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                        onsubmit="return confirm('Delete user {{ addslashes($u->name) }}? This cannot be undone.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="ad-del-btn"><i class="fas fa-trash-alt"></i> Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;padding:40px;color:#9ca3af;">No users found.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($users->hasPages())
    <div style="padding:16px 22px;border-top:1px solid #f3f4f8;">{{ $users->links() }}</div>
    @endif
</div>

@endsection
