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
#lawyer-details-content > div[style*="padding:0 34px 32px 34px;"] > div[style*="display:grid;grid-template-columns:1fr 1fr;gap:14px;font-size:1rem;"] > div {
    background: #fff;
    border: 1px solid #e7edf5;
    border-radius: 16px;
    padding: 16px 18px;
    box-shadow: 0 10px 22px rgba(15,23,42,.04);
}
#lawyer-details-content > div[style*="padding:0 34px 32px 34px;"] > div[style*="display:grid;grid-template-columns:1fr 1fr;gap:14px;font-size:1rem;"] > div > div:first-child {
    color: #64748b !important;
    font-size: .72rem !important;
    font-weight: 800 !important;
    text-transform: uppercase;
    letter-spacing: .08em;
}
#lawyer-details-content > div[style*="padding:0 34px 32px 34px;"] > div[style*="display:grid;grid-template-columns:1fr 1fr;gap:14px;font-size:1rem;"] > div > div:last-child {
    margin-top: 8px;
    font-weight: 700 !important;
    color: #16233f !important;
}
@media (max-width: 720px) {
    #lawyer-details-content > div[style*="padding:0 34px 32px 34px;"] > div[style*="display:grid;grid-template-columns:1fr 1fr;gap:14px;font-size:1rem;"] {
        grid-template-columns: 1fr !important;
    }
}
</style>
<div id="lawyer-details-content" style="background:linear-gradient(180deg,#fcfdff 0%,#f8fafc 100%);border-radius:24px;max-width:660px;width:96vw;box-shadow:0 28px 80px rgba(15,23,42,.22);padding:0;position:relative;border:1px solid rgba(226,232,240,.95);overflow:hidden;">
    <style>
    #lawyer-details-content {
        background: linear-gradient(180deg, #fcfdff 0%, #f8fafc 100%) !important;
        border-radius: 24px !important;
        max-width: 660px !important;
        width: 96vw !important;
        box-shadow: 0 28px 80px rgba(15,23,42,.22) !important;
        border: 1px solid rgba(226,232,240,.95) !important;
        overflow: hidden !important;
    }
    #lawyer-details-content > button {
        width: 38px !important;
        height: 38px !important;
        background: #fff !important;
        border: 1px solid #d9e1ee !important;
        border-radius: 999px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        box-shadow: 0 6px 18px rgba(15,23,42,.08) !important;
    }
    #lawyer-details-content > div[style*="padding:34px 34px 0 34px;"] > div:first-child {
        font-size: 1.95rem !important;
        font-weight: 800 !important;
        color: #14213d !important;
        letter-spacing: -.03em !important;
    }
    #lawyer-details-content > div[style*="padding:34px 34px 0 34px;"] > div:last-child {
        font-size: 1.02rem !important;
        color: #64748b !important;
        margin-top: 8px !important;
        margin-bottom: 6px !important;
    }
    #lawyer-details-content > div[style*="background:#fffbe6"] {
        background: #fffbe6 !important;
        border: 1px solid #f3d37a !important;
        color: #9a6700 !important;
        border-radius: 16px !important;
        font-weight: 600 !important;
    }
    #lawyer-details-content > div[style*="padding:0 34px 32px 34px;"] > div[style*="display:grid;grid-template-columns:1fr 1fr;gap:14px;font-size:1rem;"] > div {
        background: #fff !important;
        border: 1px solid #e7edf5 !important;
        border-radius: 16px !important;
        padding: 16px 18px !important;
        box-shadow: 0 10px 22px rgba(15,23,42,.04) !important;
    }
    #lawyer-details-content > div[style*="padding:0 34px 32px 34px;"] > div[style*="display:grid;grid-template-columns:1fr 1fr;gap:14px;font-size:1rem;"] > div > div:first-child {
        color: #64748b !important;
        font-size: .72rem !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
        letter-spacing: .08em !important;
    }
    #lawyer-details-content > div[style*="padding:0 34px 32px 34px;"] > div[style*="display:grid;grid-template-columns:1fr 1fr;gap:14px;font-size:1rem;"] > div > div:last-child {
        margin-top: 8px !important;
        font-size: 1rem !important;
        font-weight: 700 !important;
        color: #16233f !important;
    }
    #lawyer-details-content > div[style*="padding:0 34px 32px 34px;"] > div[style*="display:grid;grid-template-columns:1fr 1fr;gap:18px;"] > div {
        background: #fff !important;
        border: 1px solid #e7edf5 !important;
        border-radius: 18px !important;
        box-shadow: 0 10px 24px rgba(15,23,42,.04) !important;
    }
    #lawyer-details-content form button {
        border-radius: 14px !important;
        padding: 14px 0 !important;
        font-weight: 700 !important;
    }
    @media (max-width: 720px) {
        #lawyer-details-content > div[style*="padding:0 34px 32px 34px;"] > div[style*="display:grid;grid-template-columns:1fr 1fr;gap:14px;font-size:1rem;"],
        #lawyer-details-content > div[style*="padding:0 34px 32px 34px;"] > div[style*="display:grid;grid-template-columns:1fr 1fr;gap:18px;"] {
            grid-template-columns: 1fr !important;
        }
    }
    </style>
    <button style="position:absolute;top:18px;right:18px;width:38px;height:38px;background:#fff;border:1px solid #d9e1ee;border-radius:999px;font-size:1.35rem;color:#64748b;cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:0 6px 18px rgba(15,23,42,.08);" onclick="closeDocsModal()">&times;</button>
    <div style="padding:34px 34px 0 34px;">
        <div style="font-size:1.95rem;font-weight:800;color:#14213d;letter-spacing:-.03em;">{{ $lp->user->name ?? '—' }}</div>
        <div style="font-size:1.02rem;color:#64748b;margin-top:8px;margin-bottom:6px;">{{ $lp->user->email ?? '—' }}</div>
    </div>
    <div style="background:#fffbe6;border:1px solid #f3d37a;color:#9a6700;font-size:1rem;padding:15px 18px;margin:22px 34px 0 34px;display:flex;align-items:center;gap:10px;border-radius:16px;font-weight:600;">
        <i class="fas fa-{{ $lp->is_certified ? 'certificate' : 'hourglass-half' }}"></i>
        <span>Application Status: <b>{{ $lp->is_certified ? 'Certified' : 'Pending' }}</b></span>
    </div>
    <div style="padding:0 34px 32px 34px;">
        <div style="margin:30px 0 12px 0;font-size:.8rem;font-weight:800;color:#a16207;letter-spacing:.14em;text-transform:uppercase;">Professional Details</div>
        <div style="border-bottom:1px solid #eadfbe;margin-bottom:18px;"></div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;font-size:1rem;">
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
                <div style="font-weight:500;color:#22223b;"><span class="lp-status-badge {{ $lp->currentStatusClass() }}">{{ $lp->currentStatusLabel() }}</span></div>
            </div>
            <div>
                <div style="color:#888;font-size:.91rem;">IBP Certified</div>
                <div style="font-weight:500;color:#22223b;">@if($lp->is_certified)Certified @else Not yet certified @endif</div>
            </div>
        </div>
        <div style="margin:30px 0 12px 0;font-size:.8rem;font-weight:800;color:#a16207;letter-spacing:.14em;text-transform:uppercase;">Submitted Verification Documents</div>
        <div style="border-bottom:1px solid #eadfbe;margin-bottom:18px;"></div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
            <div style="text-align:center;padding:16px;background:#fff;border:1px solid #e7edf5;border-radius:18px;box-shadow:0 10px 24px rgba(15,23,42,.04);min-height:220px;">
                <div style="color:#1e2d4d;font-size:.9rem;font-weight:800;margin-bottom:12px;">Government ID</div>
                @if($lp->government_id_doc)
                    @php
                        $govExt = strtolower(pathinfo($lp->government_id_doc, PATHINFO_EXTENSION));
                        $govUrl = $lp->documentUrl('government_id');
                    @endphp
                    @if(in_array($govExt, ['jpg','jpeg','png','gif','webp']))
                        <a href="{{ $govUrl }}" target="_blank" rel="noopener">
                            <img src="{{ $govUrl }}" alt="Government ID" style="width:100%;max-width:220px;border-radius:12px;border:1px solid #dee6f0;background:#f8fafc;">
                        </a>
                    @else
                        <a href="{{ $govUrl }}" target="_blank" rel="noopener" style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;margin-top:8px;min-height:130px;border:2px dashed #d7e0ec;border-radius:14px;background:#fbfcfe;color:#1e2d4d;font-weight:700;text-decoration:none;"><i class="fas fa-file-pdf" style="font-size:2rem;color:#dc3545;"></i><span>View PDF</span></a>
                    @endif
                @else
                    <div style="color:#b45309;font-size:.95rem;margin-top:8px;min-height:130px;display:flex;align-items:center;justify-content:center;gap:8px;background:#fff8e8;border:1px dashed #f3d08b;border-radius:14px;"><i class="fas fa-file-circle-minus"></i> No document submitted</div>
                @endif
            </div>
            <div style="text-align:center;padding:16px;background:#fff;border:1px solid #e7edf5;border-radius:18px;box-shadow:0 10px 24px rgba(15,23,42,.04);min-height:220px;">
                <div style="color:#1e2d4d;font-size:.9rem;font-weight:800;margin-bottom:12px;">IBP ID</div>
                @if($lp->ibp_id_doc)
                    @php
                        $ibpExt = strtolower(pathinfo($lp->ibp_id_doc, PATHINFO_EXTENSION));
                        $ibpUrl = $lp->documentUrl('ibp_id');
                    @endphp
                    @if(in_array($ibpExt, ['jpg','jpeg','png','gif','webp']))
                        <a href="{{ $ibpUrl }}" target="_blank" rel="noopener">
                            <img src="{{ $ibpUrl }}" alt="IBP ID" style="width:100%;max-width:220px;border-radius:12px;border:1px solid #dee6f0;background:#f8fafc;">
                        </a>
                    @else
                        <a href="{{ $ibpUrl }}" target="_blank" rel="noopener" style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;margin-top:8px;min-height:130px;border:2px dashed #d7e0ec;border-radius:14px;background:#fbfcfe;color:#1e2d4d;font-weight:700;text-decoration:none;"><i class="fas fa-file-pdf" style="font-size:2rem;color:#dc3545;"></i><span>View PDF</span></a>
                    @endif
                @else
                    <div style="color:#b45309;font-size:.95rem;margin-top:8px;min-height:130px;display:flex;align-items:center;justify-content:center;gap:8px;background:#fff8e8;border:1px dashed #f3d08b;border-radius:14px;"><i class="fas fa-file-circle-minus"></i> No document submitted</div>
                @endif
            </div>
        </div>
        <div style="margin-top:24px;margin-bottom:8px;">
            @if($lp->is_certified)
                <form method="POST" action="{{ route('admin.lawyers.uncertify', $lp) }}">
                    @csrf
                    <button type="submit" style="width:100%;background:#fff;color:#b42318;border:1px solid #f5c2c7;border-radius:14px;font-size:1.01rem;font-weight:700;padding:14px 0;cursor:pointer;transition:background .15s, border-color .15s;">
                        Revoke Certification
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.lawyers.certify', $lp) }}">
                    @csrf
                    <button type="submit" style="width:100%;background:linear-gradient(135deg,#059669,#0f766e);color:#fff;border:none;border-radius:14px;font-size:1.01rem;font-weight:700;padding:14px 0;cursor:pointer;transition:filter .15s;">
                        Certify Lawyer
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
</div>
@endsection
