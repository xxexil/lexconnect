@extends('layouts.lawyer')
@section('title', 'Find a Law Firm')
@section('content')

<style>
.firm-hero { background: linear-gradient(135deg, #1e2d4d 0%, #2d4a7a 100%); border-radius: 16px; padding: 32px; margin-bottom: 28px; color: #fff; display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap; }
.firm-hero-text h2 { margin: 0 0 6px; font-size: 1.4rem; font-weight: 700; }
.firm-hero-text p { margin: 0; opacity: .75; font-size: .9rem; }
.firm-hero-icon { font-size: 3rem; opacity: .2; }

.firm-card { background: #fff; border-radius: 14px; border: 1px solid #e8ecf0; padding: 24px; margin-bottom: 16px; transition: box-shadow .2s, transform .2s; }
.firm-card:hover { box-shadow: 0 6px 24px rgba(30,45,77,.1); transform: translateY(-2px); }
.firm-card.current { border: 2px solid #1e2d4d; background: linear-gradient(135deg, #f8faff 0%, #fff 100%); }

.firm-avatar { width: 56px; height: 56px; border-radius: 14px; background: linear-gradient(135deg, #1e2d4d, #2d4a7a); color: #fff; font-weight: 700; font-size: 1.1rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.firm-avatar.sm { width: 42px; height: 42px; font-size: .9rem; border-radius: 10px; }

.firm-badge-verified { display: inline-flex; align-items: center; gap: 4px; background: #d1fae5; color: #065f46; font-size: .72rem; font-weight: 600; padding: 3px 9px; border-radius: 20px; }
.firm-badge-current { display: inline-flex; align-items: center; gap: 4px; background: #dbeafe; color: #1e40af; font-size: .72rem; font-weight: 600; padding: 3px 9px; border-radius: 20px; }
.firm-tag { background: #f0f4ff; color: #1e2d4d; font-size: .73rem; padding: 3px 10px; border-radius: 20px; font-weight: 500; }

.firm-meta { display: flex; flex-wrap: wrap; gap: 14px; font-size: .82rem; color: #6c757d; margin-top: 10px; }
.firm-meta span { display: flex; align-items: center; gap: 5px; }
.firm-meta i { color: #b5860d; }

.firm-desc { margin-top: 14px; padding-top: 14px; border-top: 1px solid #f0f2f5; font-size: .87rem; color: #555; line-height: 1.65; }

.btn-leave { display: inline-flex; align-items: center; gap: 6px; background: #fff0f0; color: #dc3545; border: 1.5px solid #f5c6cb; border-radius: 8px; padding: 8px 16px; font-size: .83rem; font-weight: 600; cursor: pointer; transition: background .2s; }
.btn-leave:hover { background: #ffe0e0; }
.btn-apply { display: inline-flex; align-items: center; gap: 6px; background: #1e2d4d; color: #fff; border: none; border-radius: 8px; padding: 9px 18px; font-size: .85rem; font-weight: 600; cursor: pointer; transition: background .2s; white-space: nowrap; }
.btn-apply:hover { background: #2d4a7a; }

.section-title { font-size: 1rem; font-weight: 700; color: #1e2d4d; margin: 0 0 16px; display: flex; align-items: center; gap: 8px; }
.section-title i { color: #b5860d; }

.app-row { display: flex; align-items: center; gap: 14px; padding: 14px 0; border-bottom: 1px solid #f0f2f5; }
.app-row:last-child { border-bottom: none; padding-bottom: 0; }

.status-pill { font-size: .75rem; font-weight: 600; padding: 4px 12px; border-radius: 20px; }
.status-pill.accepted { background: #d1fae5; color: #065f46; }
.status-pill.pending { background: #fef3c7; color: #92400e; }
.status-pill.rejected { background: #fee2e2; color: #991b1b; }

.empty-state { text-align: center; padding: 48px 20px; color: #adb5bd; }
.empty-state i { font-size: 2.5rem; display: block; margin-bottom: 12px; }
.empty-state p { margin: 0; font-size: .9rem; }
</style>

{{-- Hero --}}
<div class="firm-hero">
    <div class="firm-hero-text">
        <h2>{{ $currentFirm ? 'Your Law Firm' : 'Find a Law Firm' }}</h2>
        <p>{{ $currentFirm ? 'You are currently a member of ' . $currentFirm->firm_name : 'Browse and apply to join a law firm' }}</p>
    </div>
    <i class="fas fa-building-columns firm-hero-icon"></i>
</div>

@if(session('success'))
<div style="background:#d1fae5;border:1px solid #6ee7b7;border-radius:10px;padding:13px 18px;margin-bottom:20px;color:#065f46;font-size:.88rem;display:flex;align-items:center;gap:8px;">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif
@if($errors->any())
<div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:10px;padding:13px 18px;margin-bottom:20px;">
    @foreach($errors->all() as $e)
    <div style="color:#991b1b;font-size:.85rem;">• {{ $e }}</div>
    @endforeach
</div>
@endif

{{-- CURRENT FIRM CARD --}}
@if($currentFirm)
<div class="firm-card current">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;">
        <div style="display:flex;align-items:center;gap:16px;">
            <div class="firm-avatar">{{ strtoupper(substr($currentFirm->firm_name, 0, 2)) }}</div>
            <div>
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:6px;">
                    <span style="font-weight:700;font-size:1.1rem;color:#1e2d4d;">{{ $currentFirm->firm_name }}</span>
                    @if($currentFirm->is_verified)
                    <span class="firm-badge-verified"><i class="fas fa-circle-check"></i> Verified</span>
                    @endif
                    <span class="firm-badge-current"><i class="fas fa-star"></i> Current Firm</span>
                </div>
                <div style="font-size:.84rem;color:#6c757d;">
                    @if($currentFirm->tagline){{ $currentFirm->tagline }} &bull; @endif
                    {{ $currentFirm->firm_size_label }}
                    @if($currentFirm->city) &bull; <i class="fas fa-map-marker-alt"></i> {{ $currentFirm->city }}@endif
                </div>
                @if($currentFirm->specialties)
                <div style="margin-top:8px;display:flex;flex-wrap:wrap;gap:5px;">
                    @foreach(array_slice($currentFirm->specialties, 0, 5) as $sp)
                    <span class="firm-tag">{{ $sp }}</span>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        <form method="POST" action="{{ route('lawyer.firms.leave') }}" onsubmit="return confirm('Leave {{ addslashes($currentFirm->firm_name) }}?')">
            @csrf
            <button type="submit" class="btn-leave"><i class="fas fa-sign-out-alt"></i> Leave Firm</button>
        </form>
    </div>

    @if($currentFirm->description)
    <div class="firm-desc">{{ $currentFirm->description }}</div>
    @endif

    <div class="firm-meta">
        @if($currentFirm->phone)<span><i class="fas fa-phone"></i> {{ $currentFirm->phone }}</span>@endif
        @if($currentFirm->website)<span><i class="fas fa-globe"></i> <a href="{{ $currentFirm->website }}" target="_blank" style="color:#1e2d4d;text-decoration:none;">{{ $currentFirm->website }}</a></span>@endif
        @if($currentFirm->founded_year)<span><i class="fas fa-history"></i> Est. {{ $currentFirm->founded_year }}</span>@endif
    </div>
</div>
@endif

{{-- MY APPLICATIONS --}}
@if($myApplications->count() > 0)
<div class="firm-card" style="margin-bottom:24px;">
    <div class="section-title"><i class="fas fa-paper-plane"></i> My Applications</div>
    @foreach($myApplications as $app)
    <div class="app-row">
        <div class="firm-avatar sm">{{ strtoupper(substr($app->lawFirm->firm_name ?? 'F', 0, 2)) }}</div>
        <div style="flex:1;">
            <div style="font-weight:600;color:#1e2d4d;font-size:.92rem;">{{ $app->lawFirm->firm_name ?? 'Unknown Firm' }}</div>
            <div style="font-size:.78rem;color:#adb5bd;margin-top:2px;">Applied {{ $app->created_at->diffForHumans() }}</div>
        </div>
        <span class="status-pill {{ $app->status }}">{{ ucfirst($app->status) }}</span>
    </div>
    @endforeach
</div>
@endif

{{-- BROWSE FIRMS --}}
@if(!$currentFirm)
<div class="firm-card">
    <div class="section-title"><i class="fas fa-search"></i> Browse Law Firms</div>

    @forelse($firms as $f)
    <div style="display:flex;align-items:flex-start;gap:16px;padding:18px 0;border-bottom:1px solid #f0f2f5;flex-wrap:wrap;">
        <div class="firm-avatar">{{ strtoupper(substr($f->firm_name, 0, 2)) }}</div>
        <div style="flex:1;min-width:200px;">
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:4px;">
                <span style="font-weight:700;font-size:1rem;color:#1e2d4d;">{{ $f->firm_name }}</span>
                @if($f->is_verified)
                <span class="firm-badge-verified"><i class="fas fa-circle-check"></i> Verified</span>
                @endif
            </div>
            @if($f->tagline)
            <div style="font-size:.84rem;color:#6c757d;margin-bottom:6px;">{{ $f->tagline }}</div>
            @endif
            <div class="firm-meta" style="margin-top:4px;">
                <span><i class="fas fa-users"></i> {{ $f->lawyers_count }} lawyer{{ $f->lawyers_count !== 1 ? 's' : '' }}</span>
                <span><i class="fas fa-building"></i> {{ $f->firm_size_label }}</span>
                @if($f->city)<span><i class="fas fa-map-marker-alt"></i> {{ $f->city }}</span>@endif
                @if($f->founded_year)<span><i class="fas fa-history"></i> Est. {{ $f->founded_year }}</span>@endif
            </div>
            @if($f->specialties)
            <div style="margin-top:8px;display:flex;flex-wrap:wrap;gap:5px;">
                @foreach(array_slice($f->specialties, 0, 5) as $sp)
                <span class="firm-tag">{{ $sp }}</span>
                @endforeach
            </div>
            @endif
            @if($f->description)
            <div style="margin-top:8px;font-size:.84rem;color:#6c757d;line-height:1.5;">{{ \Illuminate\Support\Str::limit($f->description, 160) }}</div>
            @endif
        </div>
        <button class="btn-apply" onclick="openApplyModal({{ $f->id }}, '{{ addslashes($f->firm_name) }}')">
            <i class="fas fa-paper-plane"></i> Apply
        </button>
    </div>
    @empty
    <div class="empty-state">
        <i class="fas fa-building"></i>
        <p>No law firms available right now. Check back later.</p>
    </div>
    @endforelse
</div>
@endif

{{-- APPLY MODAL --}}
<div id="applyModal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.55);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:18px;padding:32px;width:100%;max-width:480px;margin:20px;box-shadow:0 20px 60px rgba(0,0,0,.2);">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:6px;">
            <div style="width:40px;height:40px;background:#f0f4ff;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-paper-plane" style="color:#1e2d4d;"></i>
            </div>
            <div>
                <div style="font-weight:700;color:#1e2d4d;font-size:1rem;">Apply to <span id="modalFirmName"></span></div>
                <div style="font-size:.8rem;color:#adb5bd;">Introduce yourself to the firm</div>
            </div>
        </div>
        <form method="POST" action="{{ route('lawyer.firms.apply') }}" style="margin-top:20px;">
            @csrf
            <input type="hidden" name="law_firm_id" id="modalFirmId">
            <textarea name="message" rows="4"
                style="width:100%;padding:12px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:.9rem;font-family:inherit;box-sizing:border-box;resize:vertical;outline:none;transition:border .2s;"
                onfocus="this.style.borderColor='#1e2d4d'" onblur="this.style.borderColor='#e2e8f0'"
                placeholder="e.g. I have 5 years of experience in corporate law and I'm interested in joining your team..."></textarea>
            <div style="display:flex;gap:10px;margin-top:16px;">
                <button type="submit" class="btn-apply" style="flex:1;justify-content:center;padding:12px;">
                    <i class="fas fa-paper-plane"></i> Submit Application
                </button>
                <button type="button" onclick="closeApplyModal()"
                    style="padding:12px 20px;background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.88rem;cursor:pointer;font-family:inherit;color:#555;font-weight:500;">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
@push('scripts')
<script>
function openApplyModal(firmId, firmName) {
    document.getElementById('modalFirmId').value = firmId;
    document.getElementById('modalFirmName').textContent = firmName;
    var modal = document.getElementById('applyModal');
    modal.style.display = 'flex';
}
function closeApplyModal() {
    document.getElementById('applyModal').style.display = 'none';
}
document.getElementById('applyModal').addEventListener('click', function(e) {
    if (e.target === this) closeApplyModal();
});
</script>
@endpush
