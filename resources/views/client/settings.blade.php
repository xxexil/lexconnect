@extends('layouts.app')
@section('title', 'Settings')
@section('content')

<style>
.cs-wrap { max-width: 760px; margin: 0 auto; }
.cs-page-title { font-size: 1.5rem; font-weight: 700; color: #1e2d4d; margin-bottom: 4px; }
.cs-page-sub   { font-size: .88rem; color: #6b7280; margin-bottom: 28px; }

.cs-tabs { display: flex; gap: 0; border-bottom: 2px solid #f0f0f0; margin-bottom: 28px; }
.cs-tab {
    padding: 10px 22px; font-size: .9rem; font-weight: 600; color: #6b7280;
    border: none; background: none; cursor: pointer;
    border-bottom: 3px solid transparent; margin-bottom: -2px; font-family: inherit;
    transition: color .15s;
}
.cs-tab.active { color: #1e2d4d; border-bottom-color: #1e2d4d; }

.cs-card {
    background: #fff; border: 1px solid #eef0f3; border-radius: 16px;
    padding: 30px 32px; box-shadow: 0 1px 4px rgba(0,0,0,.04); margin-bottom: 20px;
}
.cs-section-title {
    font-size: 1rem; font-weight: 700; color: #1e2d4d;
    padding-bottom: 12px; border-bottom: 1px solid #f0f2f5; margin-bottom: 22px;
    display: flex; align-items: center; gap: 8px;
}
.cs-section-title i { color: #2563eb; }

.cs-field { margin-bottom: 18px; }
.cs-label { font-size: .8rem; font-weight: 600; color: #374151; display: block; margin-bottom: 5px; }
.cs-input {
    width: 100%; padding: 10px 14px; border: 1.5px solid #e5e7eb; border-radius: 8px;
    font-size: .9rem; color: #1e2d4d; font-family: inherit; transition: border-color .15s;
}
.cs-input:focus { outline: none; border-color: #2563eb; }
.cs-hint { font-size: .75rem; color: #9ca3af; margin-top: 4px; }

/* Toggle switch */
.cs-toggle-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 0; border-bottom: 1px solid #f5f5f7;
}
.cs-toggle-row:last-child { border-bottom: none; }
.cs-toggle-info {}
.cs-toggle-label { font-size: .9rem; font-weight: 600; color: #1e2d4d; }
.cs-toggle-desc  { font-size: .78rem; color: #6b7280; margin-top: 2px; }
.cs-switch { position: relative; display: inline-block; width: 46px; height: 26px; }
.cs-switch input { opacity:0; width:0; height:0; }
.cs-slider {
    position: absolute; inset: 0; cursor: pointer; background: #d1d5db;
    border-radius: 26px; transition: .2s;
}
.cs-slider::before {
    content: ''; position: absolute; height: 20px; width: 20px;
    left: 3px; bottom: 3px; background: #fff; border-radius: 50%; transition: .2s;
}
.cs-switch input:checked + .cs-slider { background: #1e2d4d; }
.cs-switch input:checked + .cs-slider::before { transform: translateX(20px); }

/* Danger zone */
.cs-danger-card {
    background: #fff; border: 1.5px solid #fee2e2; border-radius: 16px;
    padding: 24px 32px; margin-bottom: 20px;
}
.cs-danger-title { font-size: .95rem; font-weight: 700; color: #dc2626; margin-bottom: 6px; display: flex; align-items: center; gap: 8px; }
.cs-danger-desc  { font-size: .85rem; color: #6b7280; margin-bottom: 16px; }
.cs-danger-btn {
    padding: 9px 22px; background: none; border: 1.5px solid #dc2626;
    color: #dc2626; border-radius: 8px; font-size: .88rem; font-weight: 600;
    cursor: pointer; font-family: inherit; transition: background .15s;
}
.cs-danger-btn:hover { background: #fef2f2; }

.cs-save-btn {
    padding: 11px 30px; background: #1e2d4d; color: #fff; border: none;
    border-radius: 10px; font-size: .92rem; font-weight: 600; cursor: pointer;
    font-family: inherit; transition: background .2s; margin-top: 6px;
}
.cs-save-btn:hover { background: #162340; }

.cs-alert-success {
    background: #ecfdf5; border: 1px solid #6ee7b7; color: #065f46;
    border-radius: 10px; padding: 12px 18px; margin-bottom: 20px;
    display: flex; align-items: center; gap: 8px; font-size: .9rem;
}
.cs-alert-error {
    background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b;
    border-radius: 10px; padding: 12px 18px; margin-bottom: 20px; font-size: .9rem;
}
</style>

@php $activeTab = session('tab', 'security'); @endphp

<div class="cs-wrap">
    <div class="cs-page-title">Settings</div>
    <p class="cs-page-sub">Manage your account security and notification preferences</p>

    @if(session('success'))
    <div class="cs-alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif

    @if($errors->any())
    <div class="cs-alert-error">
        <i class="fas fa-exclamation-circle"></i>
        @foreach($errors->all() as $err) {{ $err }}<br> @endforeach
    </div>
    @endif

    <div class="cs-tabs">
        <button class="cs-tab {{ $activeTab === 'security' ? 'active' : '' }}" onclick="csTab('security',this)">
            <i class="fas fa-lock"></i> Security
        </button>
        <button class="cs-tab {{ $activeTab === 'notifications' ? 'active' : '' }}" onclick="csTab('notifications',this)">
            <i class="fas fa-bell"></i> Notifications
        </button>
        <button class="cs-tab {{ $activeTab === 'account' ? 'active' : '' }}" onclick="csTab('account',this)">
            <i class="fas fa-shield-alt"></i> Account
        </button>
    </div>

    {{-- SECURITY TAB --}}
    <div id="cst-security" style="{{ $activeTab !== 'security' ? 'display:none;' : '' }}">
        <div class="cs-card">
            <div class="cs-section-title"><i class="fas fa-lock"></i> Change Password</div>
            <form action="{{ route('client.settings.password') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="cs-field">
                    <label class="cs-label">Current Password</label>
                    <input type="password" name="current_password" class="cs-input"
                        placeholder="Enter current password" required autocomplete="current-password">
                </div>
                <div class="cs-field">
                    <label class="cs-label">New Password</label>
                    <input type="password" name="password" class="cs-input"
                        placeholder="At least 8 characters" required autocomplete="new-password">
                </div>
                <div class="cs-field">
                    <label class="cs-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="cs-input"
                        placeholder="Repeat new password" required autocomplete="new-password">
                </div>
                <button type="submit" class="cs-save-btn">
                    <i class="fas fa-key" style="margin-right:6px;"></i> Update Password
                </button>
            </form>
        </div>

        <div class="cs-card">
            <div class="cs-section-title"><i class="fas fa-shield-alt"></i> Login Security</div>
            <div style="background:#f8faff;border-radius:10px;padding:16px 20px;">
                <div style="font-size:.88rem;color:#374151;font-weight:600;">Last login</div>
                <div style="font-size:.82rem;color:#6b7280;margin-top:3px;">
                    <i class="fas fa-clock"></i> {{ Auth::user()->updated_at->format('M d, Y g:i A') }}
                </div>
            </div>
        </div>
    </div>

    {{-- NOTIFICATIONS TAB --}}
    <div id="cst-notifications" style="{{ $activeTab !== 'notifications' ? 'display:none;' : '' }}">
        <div class="cs-card">
            <div class="cs-section-title"><i class="fas fa-bell"></i> Email Notifications</div>
            <div class="cs-toggle-row">
                <div class="cs-toggle-info">
                    <div class="cs-toggle-label">Appointment Reminders</div>
                    <div class="cs-toggle-desc">Get notified 24 hours before a scheduled consultation</div>
                </div>
                <label class="cs-switch"><input type="checkbox" checked><span class="cs-slider"></span></label>
            </div>
            <div class="cs-toggle-row">
                <div class="cs-toggle-info">
                    <div class="cs-toggle-label">New Messages</div>
                    <div class="cs-toggle-desc">Receive an email when a lawyer sends you a message</div>
                </div>
                <label class="cs-switch"><input type="checkbox" checked><span class="cs-slider"></span></label>
            </div>
            <div class="cs-toggle-row">
                <div class="cs-toggle-info">
                    <div class="cs-toggle-label">Payment Receipts</div>
                    <div class="cs-toggle-desc">Email confirmation after each payment</div>
                </div>
                <label class="cs-switch"><input type="checkbox" checked><span class="cs-slider"></span></label>
            </div>
            <div class="cs-toggle-row">
                <div class="cs-toggle-info">
                    <div class="cs-toggle-label">Promotional Emails</div>
                    <div class="cs-toggle-desc">Tips, lawyer spotlights, and platform news</div>
                </div>
                <label class="cs-switch"><input type="checkbox"><span class="cs-slider"></span></label>
            </div>
        </div>
    </div>

    {{-- ACCOUNT TAB --}}
    <div id="cst-account" style="{{ $activeTab !== 'account' ? 'display:none;' : '' }}">
        <div class="cs-card">
            <div class="cs-section-title"><i class="fas fa-user"></i> Account Information</div>
            <div style="display:grid;gap:10px;">
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f5f5f7;">
                    <span style="font-size:.88rem;color:#6b7280;">Name</span>
                    <span style="font-size:.88rem;font-weight:600;color:#1e2d4d;">{{ Auth::user()->name }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f5f5f7;">
                    <span style="font-size:.88rem;color:#6b7280;">Email</span>
                    <span style="font-size:.88rem;font-weight:600;color:#1e2d4d;">{{ Auth::user()->email }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f5f5f7;">
                    <span style="font-size:.88rem;color:#6b7280;">Role</span>
                    <span style="font-size:.88rem;font-weight:600;color:#2563eb;">{{ ucfirst(Auth::user()->role) }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:10px 0;">
                    <span style="font-size:.88rem;color:#6b7280;">Member Since</span>
                    <span style="font-size:.88rem;font-weight:600;color:#1e2d4d;">{{ Auth::user()->created_at->format('F d, Y') }}</span>
                </div>
            </div>
        </div>

        <div class="cs-danger-card">
            <div class="cs-danger-title"><i class="fas fa-exclamation-triangle"></i> Danger Zone</div>
            <div class="cs-danger-desc">Deleting your account is permanent and cannot be undone. All your consultations, messages, and payment history will be lost.</div>
            <button class="cs-danger-btn" onclick="if(confirm('Are you absolutely sure? This cannot be undone.')) alert('Please contact support to delete your account.')">
                <i class="fas fa-trash-alt"></i> Delete My Account
            </button>
        </div>
    </div>
</div>

<script>
function csTab(name, el) {
    ['security','notifications','account'].forEach(function(t) {
        document.getElementById('cst-'+t).style.display = t === name ? '' : 'none';
    });
    document.querySelectorAll('.cs-tab').forEach(function(b) { b.classList.remove('active'); });
    el.classList.add('active');
}
</script>

@endsection
