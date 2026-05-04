@extends('layouts.admin')
@section('title', 'Law Firm Details')
@section('page-title', 'Law Firm Details')
@section('content')
<div id="law-firm-details-content" style="background:linear-gradient(180deg,#fcfefc 0%,#f7fbf8 100%);border-radius:24px;max-width:700px;width:96vw;box-shadow:0 28px 80px rgba(15,23,42,.22);padding:0;position:relative;border:1px solid rgba(212,226,217,.95);overflow:hidden;">
    <style>
    #law-firm-details-content { background:linear-gradient(180deg,#fcfefc 0%,#f7fbf8 100%) !important; border-radius:24px !important; max-width:700px !important; width:96vw !important; box-shadow:0 28px 80px rgba(15,23,42,.22) !important; border:1px solid rgba(212,226,217,.95) !important; overflow:hidden !important; }
    #law-firm-details-content > button { width:38px !important; height:38px !important; background:#fff !important; border:1px solid #d9e6dd !important; border-radius:999px !important; display:flex !important; align-items:center !important; justify-content:center !important; box-shadow:0 6px 18px rgba(15,23,42,.08) !important; }
    #law-firm-details-content .lfd-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
    #law-firm-details-content .lfd-card { background:#fff; border:1px solid #e3ece6; border-radius:16px; padding:16px 18px; box-shadow:0 10px 22px rgba(15,23,42,.04); }
    #law-firm-details-content .lfd-label { color:#64748b; font-size:.72rem; font-weight:800; text-transform:uppercase; letter-spacing:.08em; }
    #law-firm-details-content .lfd-value { margin-top:8px; font-size:1rem; font-weight:700; color:#16233f; }
    #law-firm-details-content .lfd-doc-grid { display:grid; grid-template-columns:1fr 1fr; gap:18px; }
    #law-firm-details-content .lfd-doc-card { text-align:center; padding:16px; background:#fff; border:1px solid #e7edf5; border-radius:18px; box-shadow:0 10px 24px rgba(15,23,42,.04); min-height:220px; }
    #law-firm-details-content .lfd-doc-title { color:#173726; font-size:.9rem; font-weight:800; margin-bottom:12px; }
    #law-firm-details-content .lfd-doc-link { display:flex; flex-direction:column; align-items:center; justify-content:center; gap:8px; margin-top:8px; min-height:130px; border:2px dashed #d7e0ec; border-radius:14px; background:#fbfcfe; color:#1e2d4d; font-weight:700; text-decoration:none; }
    #law-firm-details-content .lfd-doc-link img { width:100%; max-width:220px; border-radius:12px; border:1px solid #dee6f0; background:#f8fafc; }
    #law-firm-details-content .lfd-doc-empty { color:#b45309; font-size:.95rem; margin-top:8px; min-height:130px; display:flex; align-items:center; justify-content:center; gap:8px; background:#fff8e8; border:1px dashed #f3d08b; border-radius:14px; }
    #law-firm-details-content form button { border-radius:14px !important; padding:14px 0 !important; font-weight:700 !important; }
    @media (max-width: 720px) {
        #law-firm-details-content .lfd-grid,
        #law-firm-details-content .lfd-doc-grid { grid-template-columns:1fr !important; }
    }
    </style>
    <button style="position:absolute;top:18px;right:18px;width:38px;height:38px;background:#fff;border:1px solid #d9e6dd;border-radius:999px;font-size:1.35rem;color:#64748b;cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:0 6px 18px rgba(15,23,42,.08);" onclick="closeFirmDocsModal()">&times;</button>
    <div style="padding:34px 34px 0 34px;">
        <div style="font-size:1.95rem;font-weight:800;color:#173726;letter-spacing:-.03em;">{{ $firm->firm_name }}</div>
        <div style="font-size:1.02rem;color:#64748b;margin-top:8px;margin-bottom:6px;">{{ $firm->user->email ?? '—' }}</div>
    </div>
    <div style="background:#f5fbf6;border:1px solid #bfd9c8;color:#1f6b3b;font-size:1rem;padding:15px 18px;margin:22px 34px 0 34px;display:flex;align-items:center;gap:10px;border-radius:16px;font-weight:600;">
        <i class="fas fa-{{ $firm->is_verified ? 'certificate' : 'hourglass-half' }}"></i>
        <span>Verification Status: <b>{{ $firm->is_verified ? 'Verified' : 'Pending Review' }}</b></span>
    </div>
    <div style="padding:0 34px 32px 34px;">
        <div style="margin:30px 0 12px 0;font-size:.8rem;font-weight:800;color:#a16207;letter-spacing:.14em;text-transform:uppercase;">Firm Details</div>
        <div style="border-bottom:1px solid #eadfbe;margin-bottom:18px;"></div>
        <div class="lfd-grid">
            <div class="lfd-card"><div class="lfd-label">Firm Name</div><div class="lfd-value">{{ $firm->firm_name ?: '—' }}</div></div>
            <div class="lfd-card"><div class="lfd-label">Admin Name</div><div class="lfd-value">{{ $firm->user->name ?? '—' }}</div></div>
            <div class="lfd-card"><div class="lfd-label">City</div><div class="lfd-value">{{ $firm->city ?: '—' }}</div></div>
            <div class="lfd-card"><div class="lfd-label">Phone</div><div class="lfd-value">{{ $firm->phone ?: '—' }}</div></div>
            <div class="lfd-card"><div class="lfd-label">Website</div><div class="lfd-value">{{ $firm->website ?: '—' }}</div></div>
            <div class="lfd-card"><div class="lfd-label">Founded Year</div><div class="lfd-value">{{ $firm->founded_year ?: '—' }}</div></div>
        </div>

        <div style="margin:30px 0 12px 0;font-size:.8rem;font-weight:800;color:#a16207;letter-spacing:.14em;text-transform:uppercase;">Submitted Verification Documents</div>
        <div style="border-bottom:1px solid #eadfbe;margin-bottom:18px;"></div>
        <div class="lfd-doc-grid">
            @foreach([
                'DTI/SEC Registration' => ['key' => 'dti_sec_registration', 'path' => $firm->dti_sec_registration_doc],
                'Business Permit' => ['key' => 'business_permit', 'path' => $firm->business_permit_doc],
                'Valid ID' => ['key' => 'valid_id', 'path' => $firm->valid_id_doc],
                'IBP ID' => ['key' => 'ibp_id', 'path' => $firm->ibp_id_doc],
            ] as $label => $doc)
                <div class="lfd-doc-card">
                    <div class="lfd-doc-title">{{ $label }}</div>
                    @php
                        $path = $doc['path'];
                        $url = $firm->documentUrl($doc['key']);
                    @endphp
                    @if($path)
                        @php $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION)); @endphp
                        @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                            <a href="{{ $url }}" target="_blank" rel="noopener" class="lfd-doc-link" style="border:none;background:transparent;min-height:auto;margin-top:0;">
                                <img src="{{ $url }}" alt="{{ $label }}">
                            </a>
                        @else
                            <a href="{{ $url }}" target="_blank" rel="noopener" class="lfd-doc-link">
                                <i class="fas fa-file-pdf" style="font-size:2rem;color:#dc3545;"></i>
                                <span>View PDF</span>
                            </a>
                        @endif
                    @else
                        <div class="lfd-doc-empty"><i class="fas fa-file-circle-minus"></i> No document submitted</div>
                    @endif
                </div>
            @endforeach
        </div>

        <div style="margin-top:24px;margin-bottom:8px;">
            @if($firm->is_verified)
                <form method="POST" action="{{ route('admin.law-firms.unverify', $firm) }}">
                    @csrf
                    <button type="submit" style="width:100%;background:#fff;color:#b42318;border:1px solid #f5c2c7;border-radius:14px;font-size:1.01rem;font-weight:700;padding:14px 0;cursor:pointer;">
                        Revoke Verification
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.law-firms.verify', $firm) }}">
                    @csrf
                    <button type="submit" style="width:100%;background:linear-gradient(135deg,#059669,#0f766e);color:#fff;border:none;border-radius:14px;font-size:1.01rem;font-weight:700;padding:14px 0;cursor:pointer;">
                        Verify Firm
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
