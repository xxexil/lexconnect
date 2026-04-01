@extends('layouts.lawfirm')
@section('title', 'Firm Profile')
@section('content')

@php
$specialtyOptions = ['Corporate Law','Family Law','Criminal Defense','Immigration Law','Real Estate','Personal Injury','Employment Law','Tax Law','Intellectual Property','Estate Planning'];
$raw = $firm->specialties;
$selectedSpecs = is_array($raw) ? $raw : (is_string($raw) && $raw ? (json_decode($raw, true) ?? []) : []);
$teamCount = \App\Models\LawyerProfile::where('law_firm_id', $firm->id)->count();
@endphp

<style>
/* ── Profile hero banner ── */
.fp-hero {
    background: linear-gradient(135deg, #1a3d2b 0%, #1e5235 100%);
    border-radius: 18px;
    padding: 36px 36px 28px;
    margin-bottom: 28px;
    position: relative;
    overflow: hidden;
    color: #fff;
}
.fp-hero::before {
    content: '';
    position: absolute;
    top: -40px; right: -40px;
    width: 220px; height: 220px;
    border-radius: 50%;
    background: rgba(255,255,255,.04);
}
.fp-hero::after {
    content: '';
    position: absolute;
    bottom: -60px; right: 80px;
    width: 180px; height: 180px;
    border-radius: 50%;
    background: rgba(255,255,255,.03);
}
.fp-hero-inner { display: flex; align-items: center; gap: 28px; position: relative; z-index: 1; }
.fp-hero-badge {
    width: 90px; height: 90px; border-radius: 20px;
    background: rgba(255,255,255,.15);
    border: 2px solid rgba(255,255,255,.25);
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem; font-weight: 800; color: #fff;
    flex-shrink: 0; letter-spacing: -1px;
}
.fp-hero-name { font-size: 1.65rem; font-weight: 800; margin: 0 0 4px; }
.fp-hero-tagline { font-size: .9rem; color: rgba(255,255,255,.7); margin: 0 0 14px; }
.fp-hero-pills { display: flex; flex-wrap: wrap; gap: 8px; }
.fp-hero-pill {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 5px 13px; border-radius: 20px;
    font-size: .78rem; font-weight: 600; background: rgba(255,255,255,.12);
    color: #fff; border: 1px solid rgba(255,255,255,.2);
}
.fp-hero-pill.verified { background: rgba(193,127,36,.3); border-color: rgba(193,127,36,.5); }

/* ── Stat row inside hero ── */
.fp-stats-row {
    display: flex; gap: 0;
    margin-top: 28px; padding-top: 22px;
    border-top: 1px solid rgba(255,255,255,.12);
    position: relative; z-index: 1;
}
.fp-stat-item { flex: 1; text-align: center; }
.fp-stat-item + .fp-stat-item { border-left: 1px solid rgba(255,255,255,.12); }
.fp-stat-val { font-size: 1.55rem; font-weight: 800; color: #fff; line-height: 1; }
.fp-stat-lbl { font-size: .75rem; color: rgba(255,255,255,.6); margin-top: 4px; font-weight: 500; }

/* ── Info card (left) ── */
.fp-info-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e8edf5;
    box-shadow: 0 1px 6px rgba(0,0,0,.05);
    overflow: hidden;
    margin-bottom: 18px;
}
.fp-info-section { padding: 20px 22px; }
.fp-info-section + .fp-info-section { border-top: 1px solid #f0f4fa; }
.fp-info-section-title {
    font-size: .72rem; font-weight: 700; color: #c17f24;
    text-transform: uppercase; letter-spacing: .8px;
    margin-bottom: 12px;
}
.fp-info-row {
    display: flex; align-items: flex-start; gap: 10px;
    margin-bottom: 10px; font-size: .875rem; color: #374151;
}
.fp-info-row:last-child { margin-bottom: 0; }
.fp-info-icon { color: #c17f24; width: 16px; flex-shrink: 0; margin-top: 2px; }
.fp-spec-tags { display: flex; flex-wrap: wrap; gap: 6px; }
.fp-spec-tag {
    background: #f0f7f2; color: #1a3d2b;
    font-size: .78rem; padding: 4px 11px;
    border-radius: 20px; border: 1px solid #bfd9c8;
    font-weight: 500;
}

/* ── Edit form card ── */
.fp-form-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e8edf5;
    box-shadow: 0 1px 6px rgba(0,0,0,.05);
    overflow: hidden;
}
.fp-form-header {
    padding: 20px 26px;
    border-bottom: 1px solid #f0f4fa;
    display: flex; align-items: center; gap: 10px;
}
.fp-form-header-icon {
    width: 38px; height: 38px; border-radius: 10px;
    background: #eaf5ee; color: #1a3d2b;
    display: flex; align-items: center; justify-content: center;
    font-size: .95rem;
}
.fp-form-header-text { font-size: 1rem; font-weight: 700; color: #1a3d2b; }
.fp-form-header-sub  { font-size: .8rem; color: #6b7280; margin-top: 1px; }
.fp-form-body { padding: 24px 26px; }

/* Section dividers inside form */
.fp-section-divider {
    display: flex; align-items: center; gap: 12px;
    margin: 24px 0 18px;
}
.fp-section-divider:first-child { margin-top: 0; }
.fp-section-divider-label {
    font-size: .72rem; font-weight: 700; color: #c17f24;
    text-transform: uppercase; letter-spacing: .8px;
    white-space: nowrap;
}
.fp-section-divider-line { flex: 1; height: 1px; background: #f0f4fa; }

/* Practice area checkboxes */
.fp-spec-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(190px,1fr)); gap: 8px; margin-bottom: 6px; }
.fp-spec-label {
    display: flex; align-items: center; gap: 8px;
    font-size: .85rem; cursor: pointer;
    padding: 9px 12px;
    border: 1.5px solid #e8edf5;
    border-radius: 9px;
    transition: border-color .15s, background .15s;
    user-select: none;
}
.fp-spec-label:hover { border-color: #8ab89a; background: #f0f7f2; }
.fp-spec-label input:checked ~ * { color: #1e2d4d; }
.fp-spec-label:has(input:checked) { border-color: #1a3d2b; background: #eaf5ee; }

/* Save button */
.fp-save-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 12px 28px; background: #1a3d2b; color: #fff;
    border: none; border-radius: 10px; cursor: pointer;
    font-size: .92rem; font-weight: 700; font-family: inherit;
    margin-top: 8px; transition: background .2s;
}
.fp-save-btn:hover { background: #122b1e; }

/* Alert */
.fp-alert-success {
    background: #ecfdf5; border: 1px solid #6ee7b7; color: #065f46;
    border-radius: 12px; padding: 14px 18px; margin-bottom: 24px;
    display: flex; align-items: center; gap: 10px; font-size: .9rem;
}
.fp-alert-error {
    background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b;
    border-radius: 12px; padding: 14px 18px; margin-bottom: 24px; font-size: .88rem;
}
.fp-alert-error ul { margin: 6px 0 0 14px; padding: 0; }
.fp-alert-error li { margin-bottom: 3px; }

/* Layout */
.fp-grid { display: grid; grid-template-columns: 300px 1fr; gap: 22px; align-items: start; }
@media (max-width: 960px) { .fp-grid { grid-template-columns: 1fr; } }

.fp-merchant-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e8edf5;
    box-shadow: 0 1px 6px rgba(0,0,0,.05);
    overflow: hidden;
    margin-bottom: 18px;
}
.fp-merchant-card-body { padding: 20px 22px; }
.fp-merchant-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    border-radius: 999px;
    font-size: .74rem;
    font-weight: 700;
    border: 1px solid currentColor;
}
</style>

{{-- ── HERO BANNER ── --}}
<div class="fp-hero">
    <div class="fp-hero-inner">
        <div class="fp-hero-badge">{{ strtoupper(substr($firm->firm_name, 0, 2)) }}</div>
        <div>
            <div class="fp-hero-name">{{ $firm->firm_name }}</div>
            @if($firm->tagline)
            <div class="fp-hero-tagline">{{ $firm->tagline }}</div>
            @endif
            <div class="fp-hero-pills">
                @if($firm->is_verified)
                <span class="fp-hero-pill verified"><i class="fas fa-circle-check"></i> Verified</span>
                @endif
                <span class="fp-hero-pill"><i class="fas fa-users"></i> {{ $firm->firm_size_label }}</span>
                @if($firm->founded_year)
                <span class="fp-hero-pill"><i class="fas fa-landmark"></i> Est. {{ $firm->founded_year }}</span>
                @endif
                @if($firm->city)
                <span class="fp-hero-pill"><i class="fas fa-map-marker-alt"></i> {{ $firm->city }}</span>
                @endif
            </div>
        </div>
    </div>
    <div class="fp-stats-row">
        <div class="fp-stat-item">
            <div class="fp-stat-val">{{ $teamCount }}</div>
            <div class="fp-stat-lbl">Lawyers</div>
        </div>
        <div class="fp-stat-item">
            <div class="fp-stat-val">{{ number_format($firm->rating, 1) }}</div>
            <div class="fp-stat-lbl">Rating</div>
        </div>
        <div class="fp-stat-item">
            <div class="fp-stat-val">{{ $firm->reviews_count }}</div>
            <div class="fp-stat-lbl">Reviews</div>
        </div>
        <div class="fp-stat-item">
            <div class="fp-stat-val">{{ count($selectedSpecs) }}</div>
            <div class="fp-stat-lbl">Practice Areas</div>
        </div>
    </div>
</div>

@if(session('success'))
<div class="fp-alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif
@if($errors->any())
<div class="fp-alert-error">
    <strong><i class="fas fa-exclamation-circle"></i> Please fix the following:</strong>
    <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="fp-grid">
    {{-- ── LEFT: FIRM INFO CARD ── --}}
    <div>
        @php
            $merchantTone = $paymongoMerchant->status_tone ?? 'neutral';
            $merchantStatus = $paymongoMerchant->status_label ?? 'Not Started';
            $merchantToneStyles = [
                'success' => ['bg' => '#ecfdf5', 'border' => '#6ee7b7', 'text' => '#065f46'],
                'info' => ['bg' => '#eff6ff', 'border' => '#93c5fd', 'text' => '#1d4ed8'],
                'warning' => ['bg' => '#fffbeb', 'border' => '#fcd34d', 'text' => '#b45309'],
                'danger' => ['bg' => '#fef2f2', 'border' => '#fca5a5', 'text' => '#b91c1c'],
                'neutral' => ['bg' => '#f8fafc', 'border' => '#dbe3ef', 'text' => '#475569'],
            ];
            $merchantPalette = $merchantToneStyles[$merchantTone] ?? $merchantToneStyles['neutral'];
        @endphp

        <div class="fp-merchant-card" style="border-color: {{ $merchantPalette['border'] }}; background: {{ $merchantPalette['bg'] }};">
            <div class="fp-merchant-card-body">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:10px;">
                    <div class="fp-info-section-title" style="margin-bottom:0;color:{{ $merchantPalette['text'] }};">
                        <i class="fas fa-store"></i> PayMongo Child Merchant
                    </div>
                    <span class="fp-merchant-pill" style="color:{{ $merchantPalette['text'] }};background:#fff;border-color:{{ $merchantPalette['border'] }};">
                        {{ $merchantStatus }}
                    </span>
                </div>
                <div style="font-size:.86rem;color:#475569;line-height:1.6;">
                    {{ $childMerchantSupportMessage }}
                </div>
                @if($paymongoMerchant)
                    <div style="margin-top:10px;font-size:.78rem;color:#64748b;">
                        Mode: {{ ucfirst($paymongoMerchant->onboarding_mode) }}
                        @if($paymongoMerchant->last_synced_at)
                            • Synced {{ $paymongoMerchant->last_synced_at->diffForHumans() }}
                        @endif
                    </div>
                @endif
                <form method="POST" action="{{ route('lawfirm.paymongo-child-merchant.start') }}" style="margin-top:14px;">
                    @csrf
                    <button type="submit" class="fp-save-btn" style="width:100%;justify-content:center;margin-top:0;">
                        <i class="fas fa-link"></i>
                        {{ $paymongoMerchant ? 'Refresh Child Merchant Setup' : 'Prepare Child Merchant Setup' }}
                    </button>
                </form>
            </div>
        </div>

        {{-- Contact info --}}
        <div class="fp-info-card">
            <div class="fp-info-section">
                <div class="fp-info-section-title"><i class="fas fa-address-card"></i> Contact Info</div>
                @if($firm->city)
                <div class="fp-info-row"><i class="fas fa-map-marker-alt fp-info-icon"></i> {{ $firm->city }}@if($firm->address), {{ $firm->address }}@endif</div>
                @endif
                @if($firm->phone)
                <div class="fp-info-row"><i class="fas fa-phone fp-info-icon"></i> {{ $firm->phone }}</div>
                @endif
                @if($firm->website)
                <div class="fp-info-row">
                    <i class="fas fa-globe fp-info-icon"></i>
                    <a href="{{ $firm->website }}" target="_blank" style="color:#2563eb;word-break:break-all;">{{ $firm->website }}</a>
                </div>
                @endif
                @if(!$firm->city && !$firm->phone && !$firm->website)
                <div style="font-size:.85rem;color:#9ca3af;">No contact details added yet.</div>
                @endif
            </div>

            @if($firm->description)
            <div class="fp-info-section">
                <div class="fp-info-section-title"><i class="fas fa-file-alt"></i> About</div>
                <p style="font-size:.875rem;color:#374151;line-height:1.6;margin:0;">{{ $firm->description }}</p>
            </div>
            @endif

            @if($selectedSpecs)
            <div class="fp-info-section">
                <div class="fp-info-section-title"><i class="fas fa-balance-scale"></i> Practice Areas</div>
                <div class="fp-spec-tags">
                    @foreach($selectedSpecs as $sp)
                    <span class="fp-spec-tag">{{ $sp }}</span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Admin account info --}}
        <div class="fp-info-card">
            <div class="fp-info-section">
                <div class="fp-info-section-title"><i class="fas fa-user-shield"></i> Admin Account</div>
                <div class="fp-info-row"><i class="fas fa-user fp-info-icon"></i> {{ Auth::user()->name }}</div>
                <div class="fp-info-row"><i class="fas fa-envelope fp-info-icon"></i> {{ Auth::user()->email }}</div>
            </div>
        </div>
    </div>

    {{-- ── RIGHT: EDIT FORM ── --}}
    <div class="fp-form-card">
        <div class="fp-form-header">
            <div class="fp-form-header-icon"><i class="fas fa-pen-to-square"></i></div>
            <div>
                <div class="fp-form-header-text">Edit Firm Information</div>
                <div class="fp-form-header-sub">Update your firm's public-facing profile</div>
            </div>
        </div>
        <div class="fp-form-body">
            <form method="POST" action="{{ route('lawfirm.profile.update') }}">
                @csrf

                {{-- Account --}}
                <div class="fp-section-divider">
                    <span class="fp-section-divider-label"><i class="fas fa-user-circle"></i> Account</span>
                    <span class="fp-section-divider-line"></span>
                </div>
                <div class="lp-form-row">
                    <div class="lp-form-group">
                        <label class="lp-form-label">Admin Name</label>
                        <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}"
                            class="lp-form-input {{ $errors->has('name') ? 'is-invalid' : '' }}" required>
                        @error('name')<div class="lp-form-err">{{ $message }}</div>@enderror
                    </div>
                    <div class="lp-form-group">
                        <label class="lp-form-label">Email Address</label>
                        <input type="email" value="{{ Auth::user()->email }}" class="lp-form-input"
                            style="background:#f8f9fb;color:#6b7280;" disabled>
                    </div>
                </div>

                {{-- Firm Details --}}
                <div class="fp-section-divider">
                    <span class="fp-section-divider-label"><i class="fas fa-building"></i> Firm Details</span>
                    <span class="fp-section-divider-line"></span>
                </div>
                <div class="lp-form-row">
                    <div class="lp-form-group">
                        <label class="lp-form-label">Firm Name *</label>
                        <input type="text" name="firm_name" value="{{ old('firm_name', $firm->firm_name) }}"
                            class="lp-form-input {{ $errors->has('firm_name') ? 'is-invalid' : '' }}" required>
                        @error('firm_name')<div class="lp-form-err">{{ $message }}</div>@enderror
                    </div>
                    <div class="lp-form-group">
                        <label class="lp-form-label">Tagline</label>
                        <input type="text" name="tagline" value="{{ old('tagline', $firm->tagline) }}"
                            class="lp-form-input" placeholder="e.g. Trusted Legal Experts">
                    </div>
                </div>
                <div class="lp-form-group">
                    <label class="lp-form-label">Description</label>
                    <textarea name="description" class="lp-form-input" rows="4"
                        placeholder="Tell lawyers and clients about your firm...">{{ old('description', $firm->description) }}</textarea>
                </div>
                <div class="lp-form-row">
                    <div class="lp-form-group">
                        <label class="lp-form-label">Founded Year</label>
                        <input type="number" name="founded_year" value="{{ old('founded_year', $firm->founded_year) }}"
                            class="lp-form-input" min="1800" max="{{ date('Y') }}" placeholder="e.g. 2005">
                    </div>
                    <div class="lp-form-group">
                        <label class="lp-form-label">Firm Size</label>
                        <select name="firm_size" class="lp-form-input">
                            @foreach(['solo' => 'Solo Practice', 'small' => 'Small (2–10)', 'medium' => 'Mid-size (11–50)', 'large' => 'Large (50+)'] as $v => $l)
                            <option value="{{ $v }}" {{ old('firm_size', $firm->firm_size) === $v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Location & Contact --}}
                <div class="fp-section-divider">
                    <span class="fp-section-divider-label"><i class="fas fa-map-marked-alt"></i> Location &amp; Contact</span>
                    <span class="fp-section-divider-line"></span>
                </div>
                <div class="lp-form-row">
                    <div class="lp-form-group">
                        <label class="lp-form-label">City</label>
                        <input type="text" name="city" value="{{ old('city', $firm->city) }}"
                            class="lp-form-input" placeholder="e.g. Makati, Metro Manila">
                    </div>
                    <div class="lp-form-group">
                        <label class="lp-form-label">Street Address</label>
                        <input type="text" name="address" value="{{ old('address', $firm->address) }}"
                            class="lp-form-input" placeholder="Street address">
                    </div>
                </div>
                <div class="lp-form-row">
                    <div class="lp-form-group">
                        <label class="lp-form-label">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $firm->phone) }}"
                            class="lp-form-input" placeholder="+63 2 8888 1234">
                    </div>
                    <div class="lp-form-group">
                        <label class="lp-form-label">Website</label>
                        <input type="url" name="website" value="{{ old('website', $firm->website) }}"
                            class="lp-form-input" placeholder="https://yourfirm.com">
                    </div>
                </div>

                {{-- Practice Areas --}}
                <div class="fp-section-divider">
                    <span class="fp-section-divider-label"><i class="fas fa-balance-scale"></i> Practice Areas</span>
                    <span class="fp-section-divider-line"></span>
                </div>
                <div class="fp-spec-grid">
                    @foreach($specialtyOptions as $sp)
                    <label class="fp-spec-label">
                        <input type="checkbox" name="specialties[]" value="{{ $sp }}"
                            {{ in_array($sp, $selectedSpecs) ? 'checked' : '' }}
                            style="accent-color:#1a3d2b;width:15px;height:15px;flex-shrink:0;">
                        <span>{{ $sp }}</span>
                    </label>
                    @endforeach
                </div>

                {{-- Security --}}
                <div class="fp-section-divider" style="margin-top:26px;">
                    <span class="fp-section-divider-label"><i class="fas fa-lock"></i> Change Password</span>
                    <span class="fp-section-divider-line"></span>
                </div>
                <div class="lp-form-row">
                    <div class="lp-form-group">
                        <label class="lp-form-label">New Password</label>
                        <input type="password" name="password" class="lp-form-input"
                            placeholder="Leave blank to keep current">
                        @error('password')<div class="lp-form-err">{{ $message }}</div>@enderror
                    </div>
                    <div class="lp-form-group">
                        <label class="lp-form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="lp-form-input"
                            placeholder="Repeat new password">
                    </div>
                </div>

                <div style="padding-top:8px;border-top:1px solid #f0f4fa;margin-top:8px;">
                    <button type="submit" class="fp-save-btn">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
