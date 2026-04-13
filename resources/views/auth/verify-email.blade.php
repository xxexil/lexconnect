@extends('layouts.app')
@section('title', 'Verify Your Email')
@section('content')
<div style="min-height:80vh;display:flex;align-items:center;justify-content:center;padding:40px 20px;background:#f5f7fa;">
    <div style="background:#fff;border-radius:20px;box-shadow:0 8px 32px rgba(0,0,0,.1);padding:48px 40px;max-width:520px;width:100%;text-align:center;">

        {{-- Icon --}}
        <div style="width:80px;height:80px;background:linear-gradient(135deg,#1e2d4d,#2d4a7a);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;box-shadow:0 4px 16px rgba(30,45,77,.3);">
            <i class="fas fa-envelope" style="font-size:2rem;color:#fff;"></i>
        </div>

        <h2 style="font-size:1.5rem;font-weight:800;color:#1e2d4d;margin:0 0 8px;">Verify your email address</h2>
        <p style="color:#6c757d;font-size:.95rem;margin:0 0 28px;">
            Almost there! We sent a verification email to<br>
            @auth <strong style="color:#1e2d4d;">{{ Auth::user()->email }}</strong> @endauth
        </p>

        @if(session('resent'))
        <div style="background:#d1fae5;border:1px solid #6ee7b7;border-radius:10px;padding:12px 16px;margin-bottom:24px;color:#065f46;font-size:.88rem;display:flex;align-items:center;gap:8px;">
            <i class="fas fa-check-circle"></i> A new verification link has been sent to your email.
        </div>
        @endif

        {{-- Steps --}}
        <div style="background:#f8fafc;border-radius:12px;padding:20px 24px;margin-bottom:28px;text-align:left;">
            <p style="font-size:.8rem;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;margin:0 0 14px;">What to do next</p>
            <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:12px;">
                <div style="width:28px;height:28px;background:#1e2d4d;border-radius:50%;color:#fff;font-size:.75rem;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;">1</div>
                <div>
                    <div style="font-weight:600;color:#1e2d4d;font-size:.9rem;">Open your email inbox</div>
                    <div style="color:#6c757d;font-size:.82rem;">Go to Gmail, Outlook, or whichever email you registered with.</div>
                </div>
            </div>
            <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:12px;">
                <div style="width:28px;height:28px;background:#1e2d4d;border-radius:50%;color:#fff;font-size:.75rem;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;">2</div>
                <div>
                    <div style="font-weight:600;color:#1e2d4d;font-size:.9rem;">Find the email from LexConnect</div>
                    <div style="color:#6c757d;font-size:.82rem;">Look for a subject line: <em>"Verify Email Address"</em>. Check your spam folder if you don't see it.</div>
                </div>
            </div>
            <div style="display:flex;align-items:flex-start;gap:12px;">
                <div style="width:28px;height:28px;background:#1e2d4d;border-radius:50%;color:#fff;font-size:.75rem;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;">3</div>
                <div>
                    <div style="font-weight:600;color:#1e2d4d;font-size:.9rem;">Click "Verify Email Address"</div>
                    <div style="color:#6c757d;font-size:.82rem;">You'll be automatically logged in and redirected to your dashboard.</div>
                </div>
            </div>
        </div>

        {{-- Resend --}}
        <p style="color:#6c757d;font-size:.85rem;margin:0 0 10px;">Didn't receive the email?</p>
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" style="width:100%;padding:13px;background:#1e2d4d;color:#fff;border:none;border-radius:10px;font-size:.93rem;font-weight:700;cursor:pointer;font-family:inherit;transition:background .2s;">
                <i class="fas fa-redo"></i> Resend Verification Email
            </button>
        </form>

        <p style="margin-top:18px;font-size:.83rem;color:#9ca3af;">
            Used the wrong email?
            <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();" style="color:#1e2d4d;font-weight:600;">Sign out</a>
            and register again.
        </p>
        <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display:none;">@csrf</form>
    </div>
</div>
@endsection
