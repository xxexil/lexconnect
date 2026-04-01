<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LexConnect – Sign In</title>
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
        .form-group input.is-invalid { border-color: #dc3545; }
        .invalid-feedback { color: #dc3545; font-size: .82rem; margin-top: 4px; }
        .remember-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
        .remember-row label { font-size: .88rem; color: #555; display: flex; align-items: center; gap: 6px; cursor: pointer; margin: 0; }
        .forgot-link { font-size: .85rem; color: #b5860d; text-decoration: none; font-weight: 500; }
        .forgot-link:hover { text-decoration: underline; }
        .btn-primary-auth { width: 100%; padding: 13px; background: #1e2d4d; color: #fff; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; letter-spacing: .5px; transition: background .2s; }
        .btn-primary-auth:hover { background: #162240; }
        .auth-footer { text-align: center; margin-top: 24px; font-size: .88rem; color: #6c757d; }
        .auth-footer a { color: #b5860d; font-weight: 600; text-decoration: none; }
        .demo-hint { background: #f8f9fa; border-left: 4px solid #b5860d; border-radius: 0 8px 8px 0; padding: 12px 16px; margin-bottom: 24px; font-size: .82rem; color: #555; }
        .demo-hint strong { color: #1e2d4d; }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-logo">
            <h1>Lex<span>Connect</span></h1>
            <p>Smart Legal Services Platform</p>
        </div>

        <div class="demo-hint">
            <strong>Client:</strong> alex@lexconnect.com / password<br>
            <strong>Lawyer:</strong> sarah@lexconnect.com / password<br>
            <strong>Law Firm:</strong> morrison@lexconnect.com / password
        </div>

        @if ($errors->any())
            <div style="background:#fff3f3;border:1px solid #dc3545;border-radius:8px;padding:12px 16px;margin-bottom:16px;font-size:.88rem;color:#721c24;">
                <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="/login">
            @csrf
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" class="{{ $errors->has('email') ? 'is-invalid' : '' }}" required autofocus>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <div class="remember-row">
                <label><input type="checkbox" name="remember"> Remember me</label>
                <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
            </div>
            <button type="submit" class="btn-primary-auth">Sign In</button>
        </form>

        <div class="auth-footer">
            Don't have an account? <a href="{{ route('register') }}">Create one</a>
        </div>
    </div>
</body>
</html>
