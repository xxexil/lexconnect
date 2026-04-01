@extends('layouts.lawfirm')
@section('title', 'Consultations')
@section('content')

<div class="lp-page-header">
    <div>
        <h1 class="lp-page-title">Firm Consultations</h1>
        <p class="lp-page-sub">All consultations handled by your team</p>
    </div>
</div>

{{-- TABS --}}
<div class="lp-tabs" style="margin-bottom:24px;">
    <button class="lp-tab active" onclick="showTab('pending', this)">Pending <span class="lp-count-badge" style="margin-left:4px;">{{ $pending->count() }}</span></button>
    <button class="lp-tab" onclick="showTab('upcoming', this)">Upcoming <span class="lp-count-badge" style="margin-left:4px;">{{ $upcoming->count() }}</span></button>
    <button class="lp-tab" onclick="showTab('completed', this)">Completed</button>
    <button class="lp-tab" onclick="showTab('cancelled', this)">Cancelled</button>
</div>

@foreach(['pending' => $pending, 'upcoming' => $upcoming, 'completed' => $completed, 'cancelled' => $cancelled] as $status => $consultations)
<div id="tab-{{ $status }}" class="lf-tab-content" @if($status !== 'pending') style="display:none;" @endif>
    @forelse($consultations as $c)
    <div class="lp-consult-card" style="border-left-color:{{ $status === 'pending' ? '#ffc107' : ($status === 'upcoming' ? '#1e2d4d' : ($status === 'completed' ? '#28a745' : '#dc3545')) }};">
        <div style="display:flex;align-items:flex-start;gap:16px;flex-wrap:wrap;">
            <div style="flex:1;min-width:200px;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
                    <span style="font-weight:700;color:#1e2d4d;">{{ $c->client->name }}</span>
                    <span style="color:#6c757d;font-size:.82rem;">→ Atty. {{ $c->lawyer->name }}</span>
                    <span class="lp-type-badge {{ $c->type }}">{{ ucfirst($c->type) }}</span>
                </div>
                <div class="lp-req-meta">
                    <span><i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($c->scheduled_at)->format('M j, Y') }}</span>
                    <span><i class="fas fa-clock"></i> {{ \Carbon\Carbon::parse($c->scheduled_at)->format('g:i A') }}</span>
                    <span><i class="fas fa-hourglass"></i> {{ $c->duration_label }}</span>
                    <span><i class="fas fa-peso-sign"></i> ₱{{ number_format($c->price, 2) }}</span>
                    <span class="lp-code-ref" style="font-size:.78rem;background:#f0f2f5;padding:2px 8px;border-radius:4px;">REF: {{ $c->consultation_code ?? 'N/A' }}</span>
                </div>
                @if($c->notes)
                <div class="lp-req-notes" style="margin-top:6px;">"{{ \Illuminate\Support\Str::limit($c->notes, 100) }}"</div>
                @endif
            </div>
            <span class="lp-status-badge {{ $c->status }}">{{ ucfirst($c->status) }}</span>
        </div>
    </div>
    @empty
    <div class="lp-empty-sm" style="padding:40px;text-align:center;">
        <i class="fas fa-calendar" style="font-size:2rem;color:#dee2e6;display:block;margin-bottom:12px;"></i>
        No {{ $status }} consultations
    </div>
    @endforelse
</div>
@endforeach

@endsection
@push('scripts')
<script>
function showTab(name, btn) {
    document.querySelectorAll('.lf-tab-content').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.lp-tab').forEach(el => el.classList.remove('active'));
    document.getElementById('tab-' + name).style.display = 'block';
    btn.classList.add('active');
}
</script>
@endpush

