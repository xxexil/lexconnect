@extends('layouts.admin')
@section('title', 'Fraud Review')
@section('page-title', 'Fraud Review')
@section('content')

<style>
.fr-summary { display:grid; grid-template-columns:repeat(5,1fr); gap:18px; margin-bottom:24px; }
@media(max-width:1100px){ .fr-summary { grid-template-columns:repeat(2,1fr); } }
@media(max-width:640px){ .fr-summary { grid-template-columns:1fr; } }
.fr-stat {
    background:#fff; border:1px solid #eef0f6; border-radius:16px; padding:20px 22px;
    box-shadow:0 1px 4px rgba(0,0,0,.04);
}
.fr-stat-num { font-size:1.7rem; font-weight:800; color:#1a1a2e; line-height:1; }
.fr-stat-lbl { margin-top:6px; font-size:.8rem; color:#6b7280; font-weight:600; }
.fr-card {
    background:#fff; border:1px solid #eef0f6; border-radius:16px; box-shadow:0 1px 4px rgba(0,0,0,.04);
    overflow:hidden;
}
.fr-toolbar { padding:18px 22px; border-bottom:1px solid #f3f4f8; }
.fr-toolbar form { display:grid; grid-template-columns:2fr 1fr 1fr 1fr auto; gap:10px; }
@media(max-width:1100px){ .fr-toolbar form { grid-template-columns:1fr 1fr; } }
@media(max-width:640px){ .fr-toolbar form { grid-template-columns:1fr; } }
.fr-input, .fr-select {
    width:100%; padding:10px 12px; border:1.5px solid #dbe1ea; border-radius:10px; font:inherit; font-size:.88rem;
    background:#fff;
}
.fr-btn, .fr-link {
    display:inline-flex; align-items:center; justify-content:center; gap:6px; border-radius:10px;
    padding:10px 14px; font-size:.84rem; font-weight:700; text-decoration:none; font-family:inherit;
}
.fr-btn { background:#1a1a2e; color:#fff; border:none; cursor:pointer; }
.fr-link { background:#f8fafc; color:#475569; border:1.5px solid #dbe1ea; }
.fr-table-wrap { overflow:auto; }
.fr-table { width:100%; border-collapse:collapse; min-width:1080px; }
.fr-table th, .fr-table td { padding:14px 16px; border-bottom:1px solid #f3f4f8; vertical-align:top; text-align:left; }
.fr-table th { font-size:.75rem; text-transform:uppercase; letter-spacing:.06em; color:#94a3b8; font-weight:800; background:#fbfcfe; }
.fr-main { font-weight:700; color:#1a1a2e; }
.fr-sub { margin-top:4px; font-size:.78rem; color:#6b7280; }
.fr-pill {
    display:inline-flex; align-items:center; gap:6px; padding:4px 10px; border-radius:999px;
    font-size:.74rem; font-weight:700;
}
.fr-pill.low { background:#ecfdf3; color:#166534; }
.fr-pill.medium { background:#fff7ed; color:#c2410c; }
.fr-pill.high { background:#fef2f2; color:#b91c1c; }
.fr-pill.allow { background:#eff6ff; color:#1d4ed8; }
.fr-pill.review { background:#fff7ed; color:#b45309; }
.fr-pill.block { background:#fef2f2; color:#b91c1c; }
.fr-flag-list { display:flex; flex-direction:column; gap:8px; }
.fr-flag {
    background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:9px 10px;
}
.fr-flag-code { font-size:.72rem; font-weight:800; color:#475569; text-transform:uppercase; }
.fr-flag-reason { margin-top:3px; font-size:.78rem; color:#64748b; line-height:1.45; }
.fr-empty { padding:36px; text-align:center; color:#94a3b8; font-size:.92rem; }
</style>

<div class="fr-summary">
    <div class="fr-stat">
        <div class="fr-stat-num">{{ $summary['total'] }}</div>
        <div class="fr-stat-lbl">Total Risk Events</div>
    </div>
    <div class="fr-stat">
        <div class="fr-stat-num" style="color:#b91c1c;">{{ $summary['high'] }}</div>
        <div class="fr-stat-lbl">High Risk</div>
    </div>
    <div class="fr-stat">
        <div class="fr-stat-num" style="color:#c2410c;">{{ $summary['medium'] }}</div>
        <div class="fr-stat-lbl">Medium Risk</div>
    </div>
    <div class="fr-stat">
        <div class="fr-stat-num" style="color:#dc2626;">{{ $summary['blocked'] }}</div>
        <div class="fr-stat-lbl">Blocked Attempts</div>
    </div>
    <div class="fr-stat">
        <div class="fr-stat-num" style="color:#1d4ed8;">{{ $summary['last24h'] }}</div>
        <div class="fr-stat-lbl">Events In Last 24 Hours</div>
    </div>
</div>

<div class="fr-card">
    <div class="fr-toolbar">
        <form method="GET" action="{{ route('admin.risk-events') }}">
            <input class="fr-input" type="text" name="search" value="{{ request('search') }}" placeholder="Search by client, lawyer, email, consultation code, or IP">
            <select class="fr-select" name="risk_level">
                <option value="">All risk levels</option>
                @foreach(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'] as $value => $label)
                    <option value="{{ $value }}" {{ request('risk_level') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <select class="fr-select" name="recommendation">
                <option value="">All recommendations</option>
                @foreach(['allow' => 'Allow', 'review' => 'Review', 'block' => 'Block'] as $value => $label)
                    <option value="{{ $value }}" {{ request('recommendation') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <select class="fr-select" name="context">
                <option value="">All contexts</option>
                @foreach($contexts as $context)
                    <option value="{{ $context }}" {{ request('context') === $context ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $context)) }}</option>
                @endforeach
            </select>
            <div style="display:flex;gap:10px;">
                <button class="fr-btn" type="submit"><i class="fas fa-filter"></i> Filter</button>
                <a class="fr-link" href="{{ route('admin.risk-events') }}">Reset</a>
            </div>
        </form>
    </div>

    <div class="fr-table-wrap">
        <table class="fr-table">
            <thead>
                <tr>
                    <th>When</th>
                    <th>Client</th>
                    <th>Lawyer</th>
                    <th>Risk</th>
                    <th>Recommendation</th>
                    <th>Amount</th>
                    <th>Context</th>
                    <th>Signals</th>
                    <th>Trace</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riskEvents as $event)
                    <tr>
                        <td>
                            <div class="fr-main">{{ $event->created_at->format('M d, Y') }}</div>
                            <div class="fr-sub">{{ $event->created_at->format('g:i A') }}</div>
                        </td>
                        <td>
                            <div class="fr-main">{{ $event->client->name ?? 'Unknown client' }}</div>
                            <div class="fr-sub">{{ $event->email ?? ($event->client->email ?? 'No email') }}</div>
                        </td>
                        <td>
                            <div class="fr-main">{{ $event->lawyer->name ?? 'Unknown lawyer' }}</div>
                            <div class="fr-sub">ID {{ $event->lawyer_id ?? '—' }}</div>
                        </td>
                        <td>
                            <span class="fr-pill {{ $event->risk_level }}">{{ ucfirst($event->risk_level) }}</span>
                            <div class="fr-sub">Score: {{ $event->risk_score }}</div>
                        </td>
                        <td>
                            <span class="fr-pill {{ $event->recommendation }}">{{ ucfirst($event->recommendation) }}</span>
                        </td>
                        <td>
                            <div class="fr-main">PHP {{ number_format((float) $event->amount, 2) }}</div>
                            <div class="fr-sub">{{ $event->currency }}</div>
                        </td>
                        <td>
                            <div class="fr-main">{{ ucfirst(str_replace('_', ' ', $event->context)) }}</div>
                        </td>
                        <td>
                            @if(!empty($event->flags))
                                <div class="fr-flag-list">
                                    @foreach($event->flags as $flag)
                                        <div class="fr-flag">
                                            <div class="fr-flag-code">{{ $flag['code'] ?? 'flag' }} · +{{ $flag['weight'] ?? 0 }}</div>
                                            <div class="fr-flag-reason">{{ $flag['reason'] ?? 'No reason recorded.' }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="fr-sub">No signals recorded.</div>
                            @endif
                        </td>
                        <td>
                            <div class="fr-sub">Consultation: {{ $event->consultation->code ?? '—' }}</div>
                            <div class="fr-sub">Payment ID: {{ $event->payment_id ?? '—' }}</div>
                            <div class="fr-sub">IP: {{ $event->ip_address ?? '—' }}</div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="fr-empty">No fraud review events match the current filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="padding:16px 22px;">
        {{ $riskEvents->links() }}
    </div>
</div>

@endsection
