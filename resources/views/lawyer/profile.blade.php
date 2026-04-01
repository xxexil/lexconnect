@extends('layouts.lawyer')
@section('title', 'My Profile')
@section('content')

<div class="lp-page-header">
    <div>
        <h1 class="lp-page-title">My Profile</h1>
        <p class="lp-page-sub">Manage your professional information and settings</p>
    </div>
</div>

@if(session('success'))
    <div class="lp-alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="lp-alert-error">
        @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
    </div>
@endif

<div class="lp-profile-grid">
    {{-- LEFT: Profile card --}}
    <div class="lp-card lp-profile-card">

        {{-- Avatar + upload --}}
        <form method="POST" action="{{ route('lawyer.profile.avatar') }}" enctype="multipart/form-data" id="avatarForm" style="margin-bottom:16px;">
            @csrf
            <div class="lp-prof-avatar-wrap" style="position:relative;cursor:pointer;"
                 onclick="document.getElementById('avatarInput').click()" title="Click to change photo">
                @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" class="lp-prof-avatar" alt="{{ $user->name }}" id="avatarPreview">
                @else
                    <div class="lp-prof-avatar-initial" id="avatarInitial">{{ strtoupper(substr($user->name,0,1)) }}</div>
                    <img src="" class="lp-prof-avatar" id="avatarPreview" style="display:none;">
                @endif
                <div id="avatarOverlay" style="position:absolute;inset:0;background:rgba(0,0,0,.35);border-radius:50%;display:flex;flex-direction:column;align-items:center;justify-content:center;opacity:0;transition:opacity .2s;">
                    <i class="fas fa-camera" style="color:#fff;font-size:1.1rem;"></i>
                    <span style="color:#fff;font-size:.62rem;font-weight:600;margin-top:3px;">Change</span>
                </div>
            </div>
            <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display:none;" onchange="uploadAvatar(this)">
            <div id="avatarStatus" style="display:none;font-size:.78rem;color:#6c757d;margin-top:8px;text-align:center;">
                <i class="fas fa-spinner fa-spin"></i> Uploading...
            </div>
        </form>

        <div class="lp-prof-name">{{ $user->name }}</div>
        <div class="lp-prof-spec">{{ $profile->specialty }}</div>
        <div class="lp-prof-firm">{{ $profile->firm ?? 'Independent' }}</div>

        <div class="lp-prof-stats">
            <div class="lp-prof-stat">
                <div class="lp-ps-num">{{ $profile->experience_years }}</div>
                <div class="lp-ps-lbl">Yrs Exp.</div>
            </div>
            <div class="lp-prof-stat">
                <div class="lp-ps-num">{{ $profile->reviews_count }}</div>
                <div class="lp-ps-lbl">Reviews</div>
            </div>
            <div class="lp-prof-stat">
                <div class="lp-ps-num">{{ number_format($profile->rating,1) }}</div>
                <div class="lp-ps-lbl">Rating</div>
            </div>
        </div>

        <div class="lp-avail-status-box">
            <div class="lp-avail-label-sm">Availability</div>
            <form method="POST" action="{{ route('lawyer.profile.availability') }}">
                @csrf
                <div class="lp-avail-btns">
                    @foreach(['available'=>'🟢 Available','busy'=>'🟡 Busy','offline'=>'⚫ Offline'] as $v=>$l)
                    <label class="lp-avail-opt {{ $profile->availability_status===$v ? 'active-'.$v : '' }}">
                        <input type="radio" name="status" value="{{ $v }}" {{ $profile->availability_status===$v ? 'checked' : '' }} onchange="this.form.submit()" style="display:none;">
                        {{ $l }}
                    </label>
                    @endforeach
                </div>
            </form>
        </div>

        <div class="lp-cert-badge-box {{ $profile->is_certified ? 'certified' : '' }}">
            <i class="fas fa-{{ $profile->is_certified ? 'shield-alt' : 'shield' }}"></i>
            {{ $profile->is_certified ? 'Bar Certified' : 'Not Certified' }}
        </div>

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

        <div style="margin-top:16px;padding:16px;border-radius:14px;border:1px solid {{ $merchantPalette['border'] }};background:{{ $merchantPalette['bg'] }};">
            <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:8px;">
                <div style="font-weight:800;font-size:.92rem;color:#1e2d4d;">
                    <i class="fas fa-store"></i> PayMongo Child Merchant
                </div>
                <span style="padding:5px 10px;border-radius:999px;background:#fff;color:{{ $merchantPalette['text'] }};font-size:.75rem;font-weight:700;border:1px solid {{ $merchantPalette['border'] }};">
                    {{ $merchantStatus }}
                </span>
            </div>
            <div style="font-size:.82rem;line-height:1.6;color:#52607a;">
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
            <form method="POST" action="{{ route('lawyer.paymongo-child-merchant.start') }}" style="margin-top:12px;">
                @csrf
                <button type="submit" class="lp-save-btn" style="width:100%;justify-content:center;padding:10px 14px;font-size:.85rem;">
                    <i class="fas fa-link"></i>
                    {{ $paymongoMerchant ? 'Refresh Child Merchant Setup' : 'Prepare Child Merchant Setup' }}
                </button>
            </form>
        </div>
    </div>

    {{-- RIGHT: Edit form --}}
    <div class="lp-card">
        <div class="lp-card-header">
            <h2 class="lp-card-title"><i class="fas fa-edit"></i> Edit Profile</h2>
        </div>

        <form method="POST" action="{{ route('lawyer.profile.update') }}" enctype="multipart/form-data" style="padding: 6px 22px 24px;">
            @csrf

            {{-- Personal Information --}}
            <div class="lp-form-section">Personal Information</div>
            <div class="lp-form-row">
                <div class="lp-form-group">
                    <label class="lp-form-label">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="lp-form-input {{ $errors->has('name') ? 'is-invalid' : '' }}" required>
                    @error('name')<div class="lp-form-err">{{ $message }}</div>@enderror
                </div>
                <div class="lp-form-group">
                    <label class="lp-form-label">Email Address</label>
                    <input type="email" value="{{ $user->email }}" class="lp-form-input" readonly
                        style="background:#f8f9fa;color:#6c757d;cursor:not-allowed;">
                </div>
            </div>

            {{-- Professional Information --}}
            <div class="lp-form-section">Professional Information</div>
            <div class="lp-form-row">
                <div class="lp-form-group">
                    <label class="lp-form-label">Specialty / Practice Area</label>
                    <select name="specialty" class="lp-form-input">
                        @foreach(['Civil Law','Corporate Law','Criminal Defense','Employment Law','Estate Planning','Family Law','Immigration Law','Intellectual Property','Labor Law','Personal Injury','Real Estate','Tax Law'] as $sp)
                            <option value="{{ $sp }}" {{ old('specialty',$profile->specialty)===$sp ? 'selected' : '' }}>{{ $sp }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lp-form-group">
                    <label class="lp-form-label">Law Firm / Organization</label>
                    <input type="text" name="firm" value="{{ old('firm', $profile->firm) }}" class="lp-form-input" placeholder="e.g. Cruz & Associates">
                </div>
            </div>
            <div class="lp-form-row">
                <div class="lp-form-group">
                    <label class="lp-form-label">Hourly Rate (₱)</label>
                    <div style="position:relative;">
                        <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#6c757d;font-weight:600;font-size:.9rem;">₱</span>
                        <input type="number" name="hourly_rate"
                            value="{{ old('hourly_rate', $profile->hourly_rate) }}"
                            class="lp-form-input {{ $errors->has('hourly_rate') ? 'is-invalid' : '' }}"
                            style="padding-left:26px;"
                            min="0" step="100" placeholder="0">
                    </div>
                    @error('hourly_rate')<div class="lp-form-err">{{ $message }}</div>@enderror
                </div>
                <div class="lp-form-group">
                    <label class="lp-form-label">Years of Experience</label>
                    <input type="number" name="experience_years" value="{{ old('experience_years', $profile->experience_years) }}" class="lp-form-input" min="0" max="60" placeholder="0">
                </div>
            </div>
            <div class="lp-form-row">
                <div class="lp-form-group lp-full">
                    <label class="lp-form-label">Location</label>
                    <div style="position:relative;">
                        <i class="fas fa-map-marker-alt" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#aaa;font-size:.85rem;"></i>
                        <input type="text" name="location" value="{{ old('location', $profile->location) }}" class="lp-form-input" style="padding-left:32px;" placeholder="e.g. Makati, Metro Manila">
                    </div>
                </div>
            </div>
            <div class="lp-form-row">
                <div class="lp-form-group lp-full">
                    <label class="lp-form-label">Professional Bio <span style="font-weight:400;color:#999;">(optional)</span></label>
                    <textarea name="bio" rows="4" class="lp-form-input" style="resize:vertical;" placeholder="Share your background, expertise, and what clients can expect...">{{ old('bio', $profile->bio) }}</textarea>
                </div>
            </div>

            {{-- Change Password --}}
            <div class="lp-form-section">Change Password <span style="font-weight:400;font-size:.8rem;color:#999;">(leave blank to keep current)</span></div>
            <div class="lp-form-row">
                <div class="lp-form-group">
                    <label class="lp-form-label">New Password</label>
                    <input type="password" name="password" class="lp-form-input" placeholder="Min. 6 characters" autocomplete="new-password">
                    @error('password')<div class="lp-form-err">{{ $message }}</div>@enderror
                </div>
                <div class="lp-form-group">
                    <label class="lp-form-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="lp-form-input" placeholder="Repeat new password" autocomplete="new-password">
                </div>
            </div>

            {{-- Payment Information --}}
            <div class="lp-form-section">Payment Information</div>
            <div class="lp-form-row">
                <div class="lp-form-group">
                    <label class="lp-form-label">GCash Number <span style="color:#888;font-size:.8rem;">(for client payments)</span></label>
                    <input type="text" name="gcash_number" value="{{ old('gcash_number', $profile->gcash_number) }}" class="lp-form-input" placeholder="09XXXXXXXXXX">
                </div>
                <div class="lp-form-group">
                    <label class="lp-form-label">GCash QR Code <span style="color:#888;font-size:.8rem;">(optional)</span></label>
                    @if($profile->gcash_qr)
                        <img src="{{ asset('storage/'.$profile->gcash_qr) }}" alt="GCash QR" style="max-width:120px;display:block;margin-bottom:6px;">
                    @endif
                    <input type="file" name="gcash_qr" accept="image/*" class="lp-form-input">
                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;margin-top:6px;">
                <button type="submit" class="lp-save-btn"><i class="fas fa-save"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
(function(){
    // Hover overlay on avatar
    var wrap = document.querySelector('.lp-prof-avatar-wrap');
    var overlay = document.getElementById('avatarOverlay');
    if (wrap && overlay) {
        wrap.addEventListener('mouseenter', function(){ overlay.style.opacity = '1'; });
        wrap.addEventListener('mouseleave', function(){ overlay.style.opacity = '0'; });
    }
})();

function uploadAvatar(input) {
    if (!input.files || !input.files[0]) return;
    // Show instant preview
    var reader = new FileReader();
    reader.onload = function(e) {
        var preview = document.getElementById('avatarPreview');
        var initial = document.getElementById('avatarInitial');
        if (preview) { preview.src = e.target.result; preview.style.display = 'block'; }
        if (initial) initial.style.display = 'none';
        // Show uploading status then submit
        document.getElementById('avatarStatus').style.display = 'block';
        document.getElementById('avatarForm').submit();
    };
    reader.readAsDataURL(input.files[0]);
}
</script>

@endsection
