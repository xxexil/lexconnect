@extends('layouts.lawyer')
@section('title', 'My Profile')
@section('content')

<div class="lp-page-header">
    <div>
        <h1 class="lp-page-title">My Profile</h1>
        <p class="lp-page-sub">Manage your professional information and settings
            @if($profile->updated_at)
                &nbsp;·&nbsp; <span style="font-size:.8rem;color:#adb5bd;">Last updated {{ $profile->updated_at->diffForHumans() }}</span>
            @endif
        </p>
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

{{-- ── PROFILE COMPLETENESS ── --}}
@php
    $fields = [
        'name'          => $user->name,
        'avatar'        => $user->avatar,
        'specialty'     => $profile->specialty,
        'location'      => $profile->location,
        'bio'           => $profile->bio,
        'hourly_rate'   => $profile->hourly_rate,
        'experience'    => $profile->experience_years,
        'gov_id'        => $profile->government_id_doc,
        'ibp_id'        => $profile->ibp_id_doc,
    ];
    $filled   = collect($fields)->filter(fn($v) => !empty($v))->count();
    $total    = count($fields);
    $pct      = (int) round($filled / $total * 100);
    $pctColor = $pct >= 80 ? '#16a34a' : ($pct >= 50 ? '#f59e0b' : '#dc2626');
@endphp
<div class="lp-card" style="margin-bottom:20px;padding:16px 22px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
        <span style="font-size:.88rem;font-weight:700;color:#1e2d4d;">Profile Completeness</span>
        <span style="font-size:.88rem;font-weight:700;color:{{ $pctColor }};">{{ $pct }}%</span>
    </div>
    <div style="background:#e9ecef;border-radius:999px;height:8px;overflow:hidden;">
        <div style="width:{{ $pct }}%;height:100%;background:{{ $pctColor }};border-radius:999px;transition:width .4s;"></div>
    </div>
    @if($pct < 100)
    <div style="margin-top:8px;font-size:.78rem;color:#6c757d;">
        @if(!$user->avatar)<span style="margin-right:12px;"><i class="fas fa-circle" style="font-size:.4rem;vertical-align:middle;color:#dc2626;margin-right:4px;"></i>Add a profile photo</span>@endif
        @if(!$profile->bio)<span style="margin-right:12px;"><i class="fas fa-circle" style="font-size:.4rem;vertical-align:middle;color:#f59e0b;margin-right:4px;"></i>Write a bio</span>@endif
        @if(!$profile->government_id_doc)<span style="margin-right:12px;"><i class="fas fa-circle" style="font-size:.4rem;vertical-align:middle;color:#f59e0b;margin-right:4px;"></i>Upload Government ID</span>@endif
        @if(!$profile->ibp_id_doc)<span><i class="fas fa-circle" style="font-size:.4rem;vertical-align:middle;color:#f59e0b;margin-right:4px;"></i>Upload IBP ID</span>@endif
    </div>
    @endif
</div>

<div class="lp-profile-grid">
    {{-- LEFT: Profile card --}}
    <div class="lp-card lp-profile-card">

        <form method="POST" action="{{ route('lawyer.profile.avatar') }}" enctype="multipart/form-data" id="avatarForm" style="margin-bottom:16px;">
            @csrf
            <div class="lp-prof-avatar-wrap" style="position:relative;cursor:pointer;"
                 onclick="document.getElementById('avatarInput').click()" title="Click to change photo">
                @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" class="lp-prof-avatar" alt="{{ $user->name }}" id="avatarPreview"
                         onerror="this.style.display='none';document.getElementById('avatarInitial').style.display='flex';">
                    <div class="lp-prof-avatar-initial" id="avatarInitial" style="display:none;">{{ strtoupper(substr($user->name,0,1)) }}</div>
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

        <div class="lp-cert-badge-box {{ $profile->is_certified ? 'certified' : '' }}">
            <i class="fas fa-{{ $profile->is_certified ? 'shield-alt' : 'shield' }}"></i>
            {{ $profile->is_certified ? 'Bar Certified' : 'Not Certified' }}
        </div>

        @if($profile->reviews_count > 0)
        <a href="#profilePreviewModal" onclick="document.getElementById('profilePreviewModal').style.display='flex';return false;"
           style="display:block;margin-top:14px;text-align:center;font-size:.82rem;color:#2563eb;font-weight:600;text-decoration:none;cursor:pointer;">
            <i class="fas fa-star" style="color:#f59e0b;margin-right:4px;"></i>View my {{ $profile->reviews_count }} review{{ $profile->reviews_count > 1 ? 's' : '' }}
        </a>
        @endif

    </div>

    {{-- RIGHT: Edit form --}}
    <div class="lp-card">
        <div class="lp-card-header">
            <h2 class="lp-card-title"><i class="fas fa-edit"></i> Edit Profile</h2>
        </div>

        <form method="POST" action="{{ route('lawyer.profile.update') }}" enctype="multipart/form-data" style="padding:6px 22px 24px;">
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
                    <div style="margin-top:4px;font-size:.75rem;color:#adb5bd;"><i class="fas fa-info-circle" style="margin-right:3px;"></i>Contact support to change your email.</div>
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
                            style="padding-left:26px;" min="0" step="100" placeholder="0">
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
                    <label class="lp-form-label" style="display:flex;justify-content:space-between;">
                        <span>Professional Bio <span style="font-weight:400;color:#999;">(optional)</span></span>
                        <span id="bioCounter" style="font-size:.75rem;color:#adb5bd;font-weight:400;">0 / 2000</span>
                    </label>
                    <textarea name="bio" id="bioTextarea" rows="4" class="lp-form-input" style="resize:vertical;"
                        placeholder="Share your background, expertise, and what clients can expect..."
                        maxlength="2000">{{ old('bio', $profile->bio) }}</textarea>
                </div>
            </div>

            {{-- Submitted IDs --}}
            <div class="lp-form-section">Submitted IDs</div>
            <div class="lp-form-row">
                <div class="lp-form-group">
                    <label class="lp-form-label">Government ID</label>
                    @if($profile->government_id_doc)
                        <a href="{{ $profile->documentUrl('government_id') }}" target="_blank" rel="noopener"
                           style="display:inline-flex;align-items:center;gap:7px;margin-bottom:8px;color:#2563eb;font-size:.86rem;font-weight:700;text-decoration:none;">
                            <i class="fas fa-id-card"></i> View current Government ID
                        </a>
                    @else
                        <div style="margin-bottom:8px;color:#b45309;font-size:.86rem;"><i class="fas fa-file-circle-minus"></i> No Government ID submitted</div>
                    @endif
                    <input type="file" name="government_id" accept="image/*,.pdf" class="lp-form-input">
                    <div style="margin-top:5px;color:#8a94a6;font-size:.78rem;">Upload a JPG, PNG, or PDF to replace the current file.</div>
                    @error('government_id')<div class="lp-form-err">{{ $message }}</div>@enderror
                </div>
                <div class="lp-form-group">
                    <label class="lp-form-label">IBP ID</label>
                    @if($profile->ibp_id_doc)
                        <a href="{{ $profile->documentUrl('ibp_id') }}" target="_blank" rel="noopener"
                           style="display:inline-flex;align-items:center;gap:7px;margin-bottom:8px;color:#2563eb;font-size:.86rem;font-weight:700;text-decoration:none;">
                            <i class="fas fa-file-certificate"></i> View current IBP ID
                        </a>
                    @else
                        <div style="margin-bottom:8px;color:#b45309;font-size:.86rem;"><i class="fas fa-file-circle-minus"></i> No IBP ID submitted</div>
                    @endif
                    <input type="file" name="ibp_id" accept="image/*,.pdf" class="lp-form-input">
                    <div style="margin-top:5px;color:#8a94a6;font-size:.78rem;">Upload a JPG, PNG, or PDF to replace the current file.</div>
                    @error('ibp_id')<div class="lp-form-err">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Change Password --}}
            <div class="lp-form-section">Change Password <span style="font-weight:400;font-size:.8rem;color:#999;">(leave blank to keep current)</span></div>
            <div class="lp-form-row">
                <div class="lp-form-group">
                    <label class="lp-form-label">New Password</label>
                    <div style="position:relative;">
                        <input type="password" name="password" id="newPassword" class="lp-form-input" placeholder="Min. 6 characters" autocomplete="new-password" style="padding-right:42px;">
                        <button type="button" onclick="togglePwd('newPassword','eyeNew')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#adb5bd;padding:0;">
                            <i class="fas fa-eye" id="eyeNew"></i>
                        </button>
                    </div>
                    @error('password')<div class="lp-form-err">{{ $message }}</div>@enderror
                </div>
                <div class="lp-form-group">
                    <label class="lp-form-label">Confirm New Password</label>
                    <div style="position:relative;">
                        <input type="password" name="password_confirmation" id="confirmPassword" class="lp-form-input" placeholder="Repeat new password" autocomplete="new-password" style="padding-right:42px;">
                        <button type="button" onclick="togglePwd('confirmPassword','eyeConfirm')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#adb5bd;padding:0;">
                            <i class="fas fa-eye" id="eyeConfirm"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;margin-top:6px;">
                <button type="submit" class="lp-save-btn"><i class="fas fa-save"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
// Avatar hover overlay
(function(){
    var wrap = document.querySelector('.lp-prof-avatar-wrap');
    var overlay = document.getElementById('avatarOverlay');
    if (wrap && overlay) {
        wrap.addEventListener('mouseenter', function(){ overlay.style.opacity = '1'; });
        wrap.addEventListener('mouseleave', function(){ overlay.style.opacity = '0'; });
    }
})();

function uploadAvatar(input) {
    if (!input.files || !input.files[0]) return;
    var reader = new FileReader();
    reader.onload = function(e) {
        var preview = document.getElementById('avatarPreview');
        var initial = document.getElementById('avatarInitial');
        if (preview) { preview.src = e.target.result; preview.style.display = 'block'; }
        if (initial) initial.style.display = 'none';
        document.getElementById('avatarStatus').style.display = 'block';
        document.getElementById('avatarForm').submit();
    };
    reader.readAsDataURL(input.files[0]);
}

// Bio character counter
(function(){
    var bio = document.getElementById('bioTextarea');
    var counter = document.getElementById('bioCounter');
    if (!bio || !counter) return;
    function update() {
        var len = bio.value.length;
        counter.textContent = len + ' / 2000';
        counter.style.color = len > 1800 ? '#dc2626' : len > 1500 ? '#f59e0b' : '#adb5bd';
    }
    bio.addEventListener('input', update);
    update();
})();

// Password show/hide toggle
function togglePwd(inputId, iconId) {
    var input = document.getElementById(inputId);
    var icon  = document.getElementById(iconId);
    if (!input || !icon) return;
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>

{{-- ── PROFILE PREVIEW MODAL ── --}}
<div id="profilePreviewModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:9999;align-items:center;justify-content:center;padding:20px;"
     onclick="if(event.target===this)this.style.display='none'">
    <div style="background:#fff;border-radius:16px;max-width:600px;width:100%;max-height:88vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.3);">
        {{-- Modal header --}}
        <div style="background:linear-gradient(135deg,#1e2d4d,#2a3f6f);border-radius:16px 16px 0 0;padding:20px 24px;display:flex;align-items:center;justify-content:space-between;">
            <div style="color:#fff;font-size:1rem;font-weight:700;"><i class="fas fa-eye" style="margin-right:8px;color:#b5860d;"></i>How clients see your profile</div>
            <button onclick="document.getElementById('profilePreviewModal').style.display='none'"
                style="background:none;border:none;color:rgba(255,255,255,.7);font-size:1.4rem;cursor:pointer;line-height:1;">&times;</button>
        </div>
        {{-- Profile preview content --}}
        <div style="padding:28px 28px 24px;">
            {{-- Hero --}}
            <div style="display:flex;align-items:center;gap:18px;margin-bottom:20px;">
                <div style="width:72px;height:72px;border-radius:50%;background:#1e2d4d;display:flex;align-items:center;justify-content:center;font-size:1.6rem;color:#fff;flex-shrink:0;overflow:hidden;">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" style="width:100%;height:100%;object-fit:cover;">
                    @else
                        {{ strtoupper(substr($user->name,0,1)) }}
                    @endif
                </div>
                <div>
                    <div style="font-size:1.2rem;font-weight:800;color:#1e2d4d;">{{ $user->name }}</div>
                    <div style="font-size:.9rem;color:#6c757d;">{{ $profile->specialty }}</div>
                    @if($profile->is_certified)
                    <span style="display:inline-flex;align-items:center;gap:5px;margin-top:4px;font-size:.75rem;font-weight:600;color:#16a34a;background:#f0fdf4;padding:3px 10px;border-radius:20px;border:1px solid #bbf7d0;">
                        <i class="fas fa-shield-alt"></i> Bar Certified
                    </span>
                    @endif
                </div>
            </div>
            {{-- Stats --}}
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:20px;">
                @foreach([['₱'.number_format($profile->hourly_rate ?? 0),'Rate/hr'],[$profile->experience_years.' yrs','Experience'],[number_format($profile->rating,1).'★','Rating'],[$profile->reviews_count,'Reviews']] as $s)
                <div style="text-align:center;background:#f8fafc;border-radius:10px;padding:12px 8px;">
                    <div style="font-size:1rem;font-weight:800;color:#1e2d4d;">{{ $s[0] }}</div>
                    <div style="font-size:.7rem;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-top:2px;">{{ $s[1] }}</div>
                </div>
                @endforeach
            </div>
            {{-- Bio --}}
            @if($profile->bio)
            <div style="margin-bottom:18px;">
                <div style="font-size:.78rem;font-weight:700;color:#1e2d4d;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;padding-bottom:5px;border-bottom:2px solid #f0f4f8;">About</div>
                <p style="font-size:.9rem;color:#444;line-height:1.65;margin:0;">{{ $profile->bio }}</p>
            </div>
            @endif
            {{-- Details --}}
            <div style="margin-bottom:18px;">
                <div style="font-size:.78rem;font-weight:700;color:#1e2d4d;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px;padding-bottom:5px;border-bottom:2px solid #f0f4f8;">Details</div>
                <div style="display:flex;flex-direction:column;gap:6px;font-size:.88rem;color:#334155;">
                    @if($profile->location)<div><i class="fas fa-map-marker-alt" style="width:16px;color:#6c757d;margin-right:6px;"></i>{{ $profile->location }}</div>@endif
                    @if($profile->firm)<div><i class="fas fa-building" style="width:16px;color:#6c757d;margin-right:6px;"></i>{{ $profile->firm }}</div>@endif
                </div>
            </div>
            {{-- Reviews --}}
            @if($profile->reviews_count > 0)
            <div>
                <div style="font-size:.78rem;font-weight:700;color:#1e2d4d;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px;padding-bottom:5px;border-bottom:2px solid #f0f4f8;">Client Reviews ({{ $profile->reviews_count }})</div>
                @foreach($reviews->take(3) as $r)
                <div style="background:#f8fafc;border:1px solid #e8ecf0;border-radius:10px;padding:12px 14px;margin-bottom:8px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:5px;">
                        <span style="font-size:.85rem;font-weight:700;color:#1e2d4d;">{{ $r->client->name }}</span>
                        <span style="color:#f59e0b;font-size:.8rem;">
                            @for($i=1;$i<=5;$i++)<i class="fa{{ $i<=$r->rating?'s':'r' }} fa-star"></i>@endfor
                        </span>
                    </div>
                    @if($r->comment)<p style="font-size:.85rem;color:#444;margin:0;line-height:1.5;">{{ $r->comment }}</p>@endif
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>

@endsection
