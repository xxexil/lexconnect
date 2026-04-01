@extends('layouts.app')
@section('title', 'My Profile')
@section('content')

<style>
.cp-wrap { max-width: 860px; margin: 0 auto; }
.cp-page-title { font-size: 1.5rem; font-weight: 700; color: #1e2d4d; margin-bottom: 4px; }
.cp-page-sub   { font-size: .88rem; color: #6b7280; margin-bottom: 28px; }

.cp-card {
    background: #fff; border: 1px solid #eef0f3; border-radius: 16px;
    padding: 32px; box-shadow: 0 1px 4px rgba(0,0,0,.04); margin-bottom: 24px;
}
.cp-section-title { font-size: 1rem; font-weight: 700; color: #1e2d4d; margin-bottom: 20px;
    padding-bottom: 12px; border-bottom: 1px solid #f0f2f5; }

/* Avatar area */
.cp-avatar-area { display: flex; align-items: center; gap: 24px; margin-bottom: 28px; }
.cp-avatar-circle {
    width: 90px; height: 90px; border-radius: 50%; object-fit: cover;
    background: #e8edf6; color: #1e2d4d; border: 3px solid #c7d0de;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.6rem; font-weight: 700; flex-shrink: 0; overflow: hidden;
}
.cp-avatar-circle img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
.cp-avatar-btns { display: flex; flex-direction: column; gap: 8px; }
.cp-avatar-upload-btn {
    display: inline-flex; align-items: center; gap: 7px; padding: 9px 18px;
    background: #1e2d4d; color: #fff; border: none; border-radius: 8px;
    font-size: .88rem; font-weight: 600; cursor: pointer; font-family: inherit;
}
.cp-avatar-upload-btn:hover { background: #162340; }
.cp-avatar-hint { font-size: .78rem; color: #9ca3af; }

/* Form grid */
.cp-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
.cp-form-grid.full { grid-template-columns: 1fr; }
.cp-field { display: flex; flex-direction: column; gap: 5px; }
.cp-field.span2 { grid-column: span 2; }
.cp-label { font-size: .8rem; font-weight: 600; color: #374151; }
.cp-input, .cp-textarea {
    padding: 10px 14px; border: 1.5px solid #e5e7eb; border-radius: 8px;
    font-size: .9rem; color: #1e2d4d; font-family: inherit;
    transition: border-color .15s;
}
.cp-input:focus, .cp-textarea:focus { outline: none; border-color: #2563eb; }
.cp-textarea { resize: vertical; min-height: 90px; }
.cp-input[readonly] { background: #f9fafb; color: #6b7280; cursor: default; }

/* Badges */
.cp-role-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 14px; border-radius: 20px;
    background: #eff5ff; color: #2563eb; font-size: .8rem; font-weight: 600;
    border: 1px solid #bfdbfe; width: fit-content; margin-top: 4px;
}

/* Save button */
.cp-save-btn {
    padding: 11px 30px; background: #1e2d4d; color: #fff; border: none;
    border-radius: 10px; font-size: .92rem; font-weight: 600; cursor: pointer;
    font-family: inherit; transition: background .2s;
}
.cp-save-btn:hover { background: #162340; }

/* Alert */
.cp-alert-success {
    background: #ecfdf5; border: 1px solid #6ee7b7; color: #065f46;
    border-radius: 10px; padding: 12px 18px; margin-bottom: 20px;
    display: flex; align-items: center; gap: 8px; font-size: .9rem;
}

/* Stats row */
.cp-stats-row { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; margin-bottom: 0; }
.cp-stat { text-align: center; padding: 16px; background: #f8faff; border-radius: 12px; border: 1px solid #e8eef8; }
.cp-stat-val { font-size: 1.4rem; font-weight: 700; color: #1e2d4d; }
.cp-stat-lbl { font-size: .78rem; color: #6b7280; margin-top: 2px; }
</style>

<div class="cp-wrap">
    <div class="cp-page-title">My Profile</div>
    <p class="cp-page-sub">Manage your personal information and account details</p>

    @if(session('success'))
    <div class="cp-alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif

    @php
        $user = Auth::user();
        $initials = collect(explode(' ', $user->name))->map(fn($p) => strtoupper($p[0]))->implode('');
        $totalConsultations = \App\Models\Consultation::where('client_id', $user->id)->count();
        $totalSpent = \App\Models\Payment::where('client_id', $user->id)->where('status','paid')->sum('amount');
        $memberSince = $user->created_at->format('M Y');
    @endphp

    <form action="{{ route('client.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Avatar + Stats --}}
        <div class="cp-card">
            <div class="cp-section-title"><i class="fas fa-user-circle" style="color:#2563eb;margin-right:8px;"></i>Profile Photo</div>
            <div class="cp-avatar-area">
                <div class="cp-avatar-circle">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="Avatar" id="avatarPreview">
                    @else
                        <span id="avatarInitials">{{ $initials }}</span>
                        <img src="" alt="" id="avatarPreview" style="display:none;">
                    @endif
                </div>
                <div class="cp-avatar-btns">
                    <label class="cp-avatar-upload-btn">
                        <i class="fas fa-camera"></i> Upload Photo
                        <input type="file" name="avatar" accept="image/*" style="display:none;"
                            onchange="previewAvatar(this)">
                    </label>
                    <span class="cp-avatar-hint">JPG, PNG or WebP · Max 4MB</span>
                </div>
            </div>

            {{-- Account Stats --}}
            <div class="cp-stats-row">
                <div class="cp-stat">
                    <div class="cp-stat-val">{{ $totalConsultations }}</div>
                    <div class="cp-stat-lbl">Consultations</div>
                </div>
                <div class="cp-stat">
                    <div class="cp-stat-val">₱{{ number_format($totalSpent, 0) }}</div>
                    <div class="cp-stat-lbl">Total Spent</div>
                </div>
                <div class="cp-stat">
                    <div class="cp-stat-val">{{ $memberSince }}</div>
                    <div class="cp-stat-lbl">Member Since</div>
                </div>
            </div>
        </div>

        {{-- Personal Info --}}
        <div class="cp-card">
            <div class="cp-section-title"><i class="fas fa-id-card" style="color:#2563eb;margin-right:8px;"></i>Personal Information</div>
            <div class="cp-form-grid">
                <div class="cp-field">
                    <label class="cp-label">Full Name</label>
                    <input type="text" name="name" class="cp-input @error('name') border-red-400 @enderror"
                        value="{{ old('name', $user->name) }}" required>
                    @error('name')<span style="color:#dc2626;font-size:.78rem;">{{ $message }}</span>@enderror
                </div>
                <div class="cp-field">
                    <label class="cp-label">Email Address</label>
                    <input type="email" name="email" class="cp-input @error('email') border-red-400 @enderror"
                        value="{{ old('email', $user->email) }}" required>
                    @error('email')<span style="color:#dc2626;font-size:.78rem;">{{ $message }}</span>@enderror
                </div>
                <div class="cp-field">
                    <label class="cp-label">Phone Number</label>
                    <input type="text" name="phone" class="cp-input"
                        value="{{ old('phone', $user->phone) }}" placeholder="+63 912 345 6789">
                </div>
                <div class="cp-field">
                    <label class="cp-label">Account Role</label>
                    <div class="cp-role-badge"><i class="fas fa-user"></i> {{ ucfirst($user->role) }}</div>
                </div>
                <div class="cp-field span2">
                    <label class="cp-label">Bio <span style="color:#9ca3af;font-weight:400;">(optional)</span></label>
                    <textarea name="bio" class="cp-textarea"
                        placeholder="Tell us a little about yourself...">{{ old('bio', $user->bio) }}</textarea>
                </div>
            </div>
        </div>

        <button type="submit" class="cp-save-btn">
            <i class="fas fa-save" style="margin-right:6px;"></i> Save Changes
        </button>
    </form>
</div>

<script>
function previewAvatar(input) {
    if (!input.files || !input.files[0]) return;
    var reader = new FileReader();
    reader.onload = function(e) {
        var preview = document.getElementById('avatarPreview');
        var initials = document.getElementById('avatarInitials');
        preview.src = e.target.result;
        preview.style.display = 'block';
        if (initials) initials.style.display = 'none';
    };
    reader.readAsDataURL(input.files[0]);
}
</script>

@endsection
