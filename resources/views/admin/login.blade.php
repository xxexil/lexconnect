<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LexConnect – Admin Login</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .admin-login-card {
            background: rgba(255,255,255,.05);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 20px;
            padding: 52px 44px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 24px 60px rgba(0,0,0,.5);
        }
        .admin-logo {
            text-align: center;
            margin-bottom: 36px;
        }
        .admin-logo .shield {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #7c3aed, #5b21b6);
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
            box-shadow: 0 8px 24px rgba(124,58,237,.4);
        }
        .admin-logo .shield i { color: #fff; font-size: 1.4rem; }
        .admin-logo h1 { font-size: 1.7rem; font-weight: 700; color: #fff; }
        .admin-logo h1 span { color: #a78bfa; }
        .admin-logo p { color: rgba(255,255,255,.5); font-size: .88rem; margin-top: 4px; }
        .admin-badge {
            display: inline-block;
            background: rgba(124,58,237,.25);
            color: #a78bfa;
            border: 1px solid rgba(124,58,237,.4);
            border-radius: 20px;
            padding: 4px 14px;
            font-size: .78rem;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 10px;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            font-size: .82rem;
            font-weight: 600;
            color: rgba(255,255,255,.7);
            margin-bottom: 8px;
            letter-spacing: .3px;
        }
        .form-group .input-wrap { position: relative; }
        .form-group .input-wrap i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,.35);
            font-size: .9rem;
        }
        .form-group input {
            width: 100%;
            padding: 12px 14px 12px 40px;
            background: rgba(255,255,255,.08);
            border: 1.5px solid rgba(255,255,255,.12);
            border-radius: 10px;
            font-size: .95rem;
            font-family: 'Inter', sans-serif;
            color: #fff;
            transition: border-color .2s, background .2s;
        }
        .form-group input::placeholder { color: rgba(255,255,255,.3); }
        .form-group input:focus { outline: none; border-color: #7c3aed; background: rgba(255,255,255,.12); }
        .form-group input.is-invalid { border-color: #f87171; }
        .error-box {
            background: rgba(248,113,113,.12);
            border: 1px solid rgba(248,113,113,.4);
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: .85rem;
            color: #fca5a5;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .remember-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 28px;
        }
        .remember-row input[type="checkbox"] { accent-color: #7c3aed; width: 15px; height: 15px; cursor: pointer; }
        .remember-row label { font-size: .86rem; color: rgba(255,255,255,.55); cursor: pointer; }
        .btn-admin-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #7c3aed, #5b21b6);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            letter-spacing: .5px;
            transition: opacity .2s, transform .1s;
            box-shadow: 0 6px 20px rgba(124,58,237,.4);
        }
        .btn-admin-login:hover { opacity: .9; transform: translateY(-1px); }
        .btn-admin-login:active { transform: translateY(0); }
        .back-link {
            text-align: center;
            margin-top: 28px;
            font-size: .84rem;
            color: rgba(255,255,255,.35);
        }
        .back-link a { color: rgba(255,255,255,.5); text-decoration: none; transition: color .2s; }
        .back-link a:hover { color: #a78bfa; }
    </style>
</head>
<body>
    <div class="admin-login-card">
        <div class="admin-logo">
            <div class="shield"><i class="fas fa-shield-halved"></i></div>
            <h1>Lex<span>Connect</span></h1>
            <p>Administration Portal</p>
            <span class="admin-badge"><i class="fas fa-lock" style="font-size:.65rem;margin-right:4px;"></i> Restricted Access</span>
        </div>

        @if ($errors->any())
            <div class="error-box">
                <i class="fas fa-exclamation-circle"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf
            <div class="form-group">
                <label>Admin Email</label>
                <div class="input-wrap">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" value="{{ old('email') }}"
                           placeholder="admin@lexconnect.com"
                           class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                           required autofocus>
                </div>
            </div>
            <div class="form-group">
                <label>Password</label>
                <div class="input-wrap">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
            </div>
            <div class="remember-row">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">Keep me signed in</label>
            </div>
            <button type="submit" class="btn-admin-login">
                <i class="fas fa-arrow-right-to-bracket" style="margin-right:8px;"></i>Access Admin Panel
            </button>
        </form>

        <div class="back-link">
            <a href="{{ route('login') }}"><i class="fas fa-arrow-left" style="font-size:.8rem;margin-right:4px;"></i>Back to main site</a>
        </div>
    </div>
</body>
</html>
