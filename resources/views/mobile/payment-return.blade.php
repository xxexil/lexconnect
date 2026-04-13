<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return To LexConnect</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
            background: linear-gradient(180deg, #f8fafc 0%, #eef4ff 100%);
            color: #1e293b;
        }
        .mpr-card {
            width: 100%;
            max-width: 420px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.12);
            border: 1px solid #e2e8f0;
            padding: 28px 24px;
            text-align: center;
        }
        .mpr-icon {
            width: 64px;
            height: 64px;
            border-radius: 999px;
            margin: 0 auto 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: 700;
            color: #fff;
            background: {{ $result === 'success' ? "'#059669'" : "'#f59e0b'" }};
        }
        .mpr-title {
            margin: 0 0 10px;
            font-size: 1.4rem;
            font-weight: 800;
            color: #0f172a;
        }
        .mpr-copy {
            margin: 0 0 20px;
            line-height: 1.6;
            color: #475569;
            font-size: .95rem;
        }
        .mpr-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 48px;
            border-radius: 12px;
            background: #1e3a8a;
            color: #fff;
            font-weight: 700;
            text-decoration: none;
        }
        .mpr-note {
            margin-top: 14px;
            font-size: .82rem;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="mpr-card">
        <div class="mpr-icon">{{ $result === 'success' ? 'OK' : '!' }}</div>
        <h1 class="mpr-title">{{ $result === 'success' ? 'Payment Received' : 'Payment Not Completed' }}</h1>
        <p class="mpr-copy">
            {{ $result === 'success'
                ? 'We are sending you back to the LexConnect app now.'
                : 'We are sending you back to the LexConnect app now so you can continue there.' }}
        </p>
        <a href="{{ $targetUrl }}" class="mpr-btn" id="openAppBtn">Open LexConnect</a>
        <div class="mpr-note">If the app does not open automatically, tap the button above.</div>
    </div>

    <script>
        (function () {
            var targetUrl = @json($targetUrl);
            var opened = false;

            function openApp() {
                if (opened) return;
                opened = true;
                window.location.href = targetUrl;
                setTimeout(function () {
                    opened = false;
                }, 1200);
            }

            window.addEventListener('load', function () {
                setTimeout(openApp, 150);
            });

            document.getElementById('openAppBtn').addEventListener('click', function () {
                openApp();
            });
        })();
    </script>
</body>
</html>
