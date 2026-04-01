@extends('layouts.admin')
@section('title', 'Lawyer Details')
@section('page-title', 'Lawyer Details')
@section('content')
.admin-review-popup {
    background: #fff;
    border-radius: 12px;
    max-width: 370px;
    width: 96vw;
    box-shadow: 0 4px 24px rgba(30,41,59,0.13);
    padding: 0 0 18px 0;
    font-family: inherit;
}
.admin-review-list {
    list-style: none;
    margin: 0;
    padding: 0 22px;
}
.admin-review-list li {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: .97rem;
    color: #22223b;
    margin-bottom: 7px;
}
.admin-review-list .icon {
    width: 18px;
    text-align: center;
    color: #6366f1;
}
.admin-review-section-title {
    font-size: .97rem;
    font-weight: 700;
    color: #7c3aed;
    margin: 12px 0 6px 0;
    padding-left: 22px;
    display: flex;
    align-items: center;
    gap: 7px;
}
.admin-doc-thumb {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 7px;
    border: 1px solid #eee;
    margin-right: 8px;
}
.admin-certify-btn {
    display: block;
    margin: 18px 0 0 22px;
    background: #059669;
    color: #fff;
    border: none;
    border-radius: 7px;
    font-size: .97rem;
    font-weight: 700;
    padding: 9px 22px;
    cursor: pointer;
    transition: background .15s;
}
.admin-certify-btn:hover { background: #047857; }
.admin-unverify-btn {
    display: block;
    margin: 18px 0 0 22px;
    background: #fff;
    color: #dc2626;
    border: 1.5px solid #fca5a5;
    border-radius: 7px;
    font-size: .97rem;
    font-weight: 700;
    padding: 9px 22px;
    cursor: pointer;
    transition: background .15s;
}
.admin-unverify-btn:hover { background: #fef2f2; }
.admin-popup-close {
    position: absolute;
    top: 12px;
    right: 16px;
    background: none;
    border: none;
    font-size: 1.5rem;
    color: #64748b;
    cursor: pointer;
}
</style>
.lf-review-modal {
    background: #fff;
    border-radius: 18px;
    max-width: 420px;
    width: 96vw;
    box-shadow: 0 8px 32px rgba(30,41,59,0.18);
    padding: 0;
    position: relative;
    font-family: inherit;
}
.lf-rm-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 22px 24px 12px 24px;
    border-bottom: 1px solid #f0f2f5;
}
.lf-rm-header .lp-req-avatar {
    width: 56px;
    height: 56px;
    line-height: 56px;
    font-size: 1.4rem;
    border-radius: 50%;
    background: #6366f1;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
}
.lf-rm-name { font-weight: 700; font-size: 1.08rem; color: #1e2d4d; }
.lf-rm-sub { font-size: .93rem; color: #64748b; }
.lf-rm-close {
    background: none;
    border: none;
    font-size: 1.7rem;
    color: #64748b;
    cursor: pointer;
    margin-left: 12px;
}
.lf-rm-status-bar {
    background: #f8fafc;
    color: #6366f1;
    font-size: .97rem;
    padding: 10px 22px;
    border-bottom: 1px solid #f0f2f5;
    display: flex;
    align-items: center;
    gap: 8px;
}
.lf-rm-status-pending { color: #b5860d; }
.lf-rm-status-accepted { color: #1a7a3c; }
.lf-rm-status-rejected { color: #dc2626; }
.lf-rm-body { padding: 20px 24px 24px 24px; }
.lf-rm-section-title {
    font-size: 1.01rem;
    font-weight: 700;
    color: #7c3aed;
    margin: 18px 0 8px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}
.lf-rm-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px 18px;
    margin-bottom: 10px;
}
.lf-rm-field { font-size: .97rem; }
.lf-rm-label { color: #888; font-size: .91rem; }
.lf-rm-value { font-weight: 500; color: #22223b; }
.lf-rm-docs-grid {
    display: flex;
    gap: 16px;
    margin-top: 8px;
    flex-wrap: wrap;
}
.lf-rm-doc-card {
    background: #f8fafc;
    border-radius: 10px;
    padding: 10px 12px;
    flex: 1 1 140px;
    min-width: 120px;
    text-align: center;
}
.lf-rm-doc-label { font-size: .93rem; color: #6366f1; font-weight: 600; margin-bottom: 6px; }
.lf-rm-doc-img { width: 100%; max-width: 110px; border-radius: 7px; margin: 0 auto; display: block; }
.lf-rm-doc-missing { color: #dc2626; font-size: .91rem; margin-top: 8px; }
.lf-rm-actions { display: flex; gap: 12px; justify-content: center; margin-top: 22px; }
.lf-rm-action-btn {
    font-size: .97rem;
    padding: 9px 22px;
    border-radius: 9px;
    font-weight: 700;
    border: none;
    background: linear-gradient(90deg,#7c3aed,#6366f1);
    color: #fff;
    cursor: pointer;
    transition: background .15s;
}
.lf-rm-action-btn:hover { background: linear-gradient(90deg,#6366f1,#7c3aed); }
.lf-rm-action-btn.decline { background: #fff; color: #dc2626; border: 2px solid #fca5a5; }
.lf-rm-action-btn.decline:hover { background: #fef2f2; }
</style>
<div id="lawyer-details-content" style="background:#fff;border-radius:14px;max-width:540px;width:96vw;box-shadow:0 8px 32px rgba(30,41,59,0.18);padding:0;position:relative;">
    <button style="position:absolute;top:18px;right:18px;background:none;border:none;font-size:1.5rem;color:#64748b;cursor:pointer;" onclick="closeDocsModal()">&times;</button>
    <div style="padding:32px 28px 0 28px;">
        <div style="font-size:1.18rem;font-weight:700;color:#1a1a2e;">{{ $lp->user->name ?? '—' }}</div>
        <div style="font-size:.98rem;color:#64748b;margin-bottom:8px;">{{ $lp->user->email ?? '—' }}</div>
    </div>
    <div style="background:#fffbe6;border:1px solid #fde68a;color:#b5860d;font-size:.99rem;padding:12px 28px 12px 28px;margin:18px 0 0 0;display:flex;align-items:center;gap:8px;">
        <i class="fas fa-hourglass-half"></i>
        <span>Application Status: <b>{{ $lp->is_certified ? 'Certified' : 'Pending' }}</b></span>
    </div>
    <div style="padding:0 28px 0 28px;">
        <div style="margin:22px 0 8px 0;font-size:.93rem;font-weight:700;color:#b5860d;letter-spacing:1px;text-transform:uppercase;">Professional Details</div>
        <div style="border-bottom:2px solid #f3f4f6;margin-bottom:18px;"></div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px 24px;font-size:.99rem;">
            <div>
                <div style="color:#888;font-size:.91rem;">Specialty / Practice Area</div>
                <div style="font-weight:500;color:#22223b;">{{ $lp->specialty ?: '—' }}</div>
            </div>
            <div>
                <div style="color:#888;font-size:.91rem;">Years of Experience</div>
                <div style="font-weight:500;color:#22223b;">{{ $lp->experience_years }} year{{ $lp->experience_years == 1 ? '' : 's' }}</div>
            </div>
            <div>
                <div style="color:#888;font-size:.91rem;">Hourly Rate</div>
                <div style="font-weight:500;color:#22223b;">₱{{ number_format($lp->hourly_rate, 0) }}/hr</div>
            </div>
            <div>
                <div style="color:#888;font-size:.91rem;">Location</div>
                <div style="font-weight:500;color:#22223b;">{{ $lp->location ?: '—' }}</div>
            </div>
            <div>
                <div style="color:#888;font-size:.91rem;">Availability</div>
                <div style="font-weight:500;color:#22223b;"><span class="lp-status-badge {{ $lp->availability_status }}">{{ ucfirst($lp->availability_status) }}</span></div>
            </div>
            <div>
                <div style="color:#888;font-size:.91rem;">IBP Certified</div>
                <div style="font-weight:500;color:#22223b;">@if($lp->is_certified)Certified @else Not yet certified @endif</div>
            </div>
        </div>
        <div style="margin:28px 0 8px 0;font-size:.93rem;font-weight:700;color:#b5860d;letter-spacing:1px;text-transform:uppercase;">Submitted Verification Documents</div>
        <div style="border-bottom:2px solid #f3f4f6;margin-bottom:18px;"></div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px 18px;">
            <div style="text-align:center;padding:0;">
                <div style="color:#6366f1;font-size:.93rem;font-weight:600;margin-bottom:6px;">Government ID</div>
                @if($lp->government_id_doc)
                    @php $govExt = strtolower(pathinfo($lp->government_id_doc, PATHINFO_EXTENSION)); @endphp
                    @if(in_array($govExt, ['jpg','jpeg','png','gif','webp']))
                        <a href="{{ asset('storage/' . $lp->government_id_doc) }}" target="_blank" rel="noopener">
                            <img src="{{ asset('storage/' . $lp->government_id_doc) }}" alt="Government ID" style="width:110px;max-width:100%;border-radius:7px;">
                        </a>
                    @else
                        <a href="{{ asset('storage/' . $lp->government_id_doc) }}" target="_blank" rel="noopener" style="display:inline-block;margin-top:8px;"><i class="fas fa-file-pdf" style="font-size:2rem;color:#dc3545;"></i><br>View PDF</a>
                    @endif
                @else
                    <div style="color:#dc2626;font-size:.95rem;margin-top:8px;"><i class="fas fa-exclamation-triangle"></i> Not submitted</div>
                @endif
            </div>
            <div style="text-align:center;padding:0;">
                <div style="color:#6366f1;font-size:.93rem;font-weight:600;margin-bottom:6px;">IBP ID</div>
                @if($lp->ibp_id_doc)
                    @php $ibpExt = strtolower(pathinfo($lp->ibp_id_doc, PATHINFO_EXTENSION)); @endphp
                    @if(in_array($ibpExt, ['jpg','jpeg','png','gif','webp']))
                        <a href="{{ asset('storage/' . $lp->ibp_id_doc) }}" target="_blank" rel="noopener">
                            <img src="{{ asset('storage/' . $lp->ibp_id_doc) }}" alt="IBP ID" style="width:110px;max-width:100%;border-radius:7px;">
                        </a>
                    @else
                        <a href="{{ asset('storage/' . $lp->ibp_id_doc) }}" target="_blank" rel="noopener" style="display:inline-block;margin-top:8px;"><i class="fas fa-file-pdf" style="font-size:2rem;color:#dc3545;"></i><br>View PDF</a>
                    @endif
                @else
                    <div style="color:#dc2626;font-size:.95rem;margin-top:8px;"><i class="fas fa-exclamation-triangle"></i> Not submitted</div>
                @endif
            </div>
        </div>
        <div style="margin-top:24px;margin-bottom:8px;">
            @if($lp->is_certified)
                <form method="POST" action="{{ route('admin.lawyers.uncertify', $lp) }}">
                    @csrf
                    <button type="submit" style="width:100%;background:#fff;color:#dc2626;border:1.5px solid #fca5a5;border-radius:8px;font-size:1.01rem;font-weight:700;padding:11px 0;cursor:pointer;transition:background .15s;">
                        Revoke Certification
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.lawyers.certify', $lp) }}">
                    @csrf
                    <button type="submit" style="width:100%;background:#059669;color:#fff;border:none;border-radius:8px;font-size:1.01rem;font-weight:700;padding:11px 0;cursor:pointer;transition:background .15s;">
                        Certify Lawyer
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
</div>
@endsection
