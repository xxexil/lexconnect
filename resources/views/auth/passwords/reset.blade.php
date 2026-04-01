<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password – LexConnect</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { background: #f0f2f5; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .auth-card { background: #fff; border-radius: 16px; box-shadow: 0 8px 30px rgba(0,0,0,.12); padding: 48px 40px; width: 100%; max-width: 440px; }
        .auth-logo { text-align: center; margin-bottom: 32px; }
        .auth-logo h1 { font-size: 1.8rem; font-weight: 700; color: #1e2d4d; margin: 0; }
        .auth-logo span { color: #b5860d; }
        .auth-logo p { color: #6c757d; font-size: .9rem; margin-top: 4px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: .85rem; font-weight: 600; color: #1e2d4d; margin-bottom: 6px; }
        .form-group input { width: 100%; padding: 11px 14px; border: 1.5px solid #dee2e6; border-radius: 8px; font-size: .95rem; font-family: inherit; box-sizing: border-box; transition: border-color .2s; }
        .form-group input:focus { outline: none; border-color: #1e2d4d; }
        .btn-primary-auth { width: 100%; padding: 13px; background: #1e2d4d; color: #fff; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; letter-spacing: .5px; transition: background .2s; }
        .btn-primary-auth:hover { background: #162240; }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-logo">
            <h1>Lex<span>Connect</span></h1>
            <p>Set New Password</p>
        </div>

        @if ($errors->any())
            <div style="background:#fff3f3;border:1px solid #dc3545;border-radius:8px;padding:12px 16px;margin-bottom:16px;font-size:.88rem;color:#721c24;">
                <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="{{ $email ?? old('email') }}" placeholder="you@example.com" required readonly>
            </div>

            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="password" placeholder="••••••••" required autofocus>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="password_confirmation" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-primary-auth">Reset Password</button>
        </form>
    </div>
</body>
</html>
