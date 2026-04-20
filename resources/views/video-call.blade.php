<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LexConnect – Video Call</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: #0f1117;
            color: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Top bar ── */
        .vc-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 24px;
            background: #1a1d27;
            border-bottom: 1px solid rgba(255,255,255,.08);
            flex-shrink: 0;
        }
        .vc-brand { display: flex; align-items: center; gap: 10px; font-weight: 700; font-size: 1rem; }
        .vc-brand-logo {
            width: 34px; height: 34px; background: #2563eb; border-radius: 8px;
            display: flex; align-items: center; justify-content: center; font-size: .9rem;
        }
        .vc-info { display: flex; align-items: center; gap: 18px; font-size: .83rem; color: rgba(255,255,255,.65); }
        .vc-info-item { display: flex; align-items: center; gap: 6px; }
        .vc-info-item i { color: rgba(255,255,255,.4); }
        .vc-pill {
            display: inline-flex; align-items: center; gap: 6px;
            background: rgba(34,197,94,.15); color: #4ade80;
            border: 1px solid rgba(34,197,94,.3);
            border-radius: 20px; padding: 4px 12px; font-size: .78rem; font-weight: 600;
        }
        .vc-pill .dot { width: 7px; height: 7px; border-radius: 50%; background: #4ade80; animation: blink 1.8s infinite; }
        @keyframes blink { 0%,100%{opacity:1;} 50%{opacity:.3;} }

        .vc-end-btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 9px 22px;
            background: #dc2626; color: #fff;
            border: none; border-radius: 8px;
            font-size: .88rem; font-weight: 700;
            cursor: pointer; font-family: inherit;
            transition: background .2s; text-decoration: none;
        }
        .vc-end-btn:hover { background: #b91c1c; color: #fff; }

        /* ── Centre panel ── */
        .vc-main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        .vc-panel {
            background: #1a1d27;
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 20px;
            padding: 48px 56px;
            text-align: center;
            max-width: 560px;
            width: 100%;
        }
        .vc-icon-ring {
            width: 90px; height: 90px; border-radius: 50%;
            background: rgba(37,99,235,.2);
            border: 2px solid rgba(37,99,235,.4);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 28px;
            font-size: 2rem; color: #60a5fa;
        }
        .vc-session-title { font-size: 1.5rem; font-weight: 700; margin-bottom: 8px; }
        .vc-session-sub   { font-size: .9rem; color: rgba(255,255,255,.55); margin-bottom: 32px; line-height: 1.6; }

        .vc-meta-grid {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 14px; margin-bottom: 32px; text-align: left;
        }
        .vc-meta-box {
            background: rgba(255,255,255,.05);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 12px; padding: 14px 16px;
        }
        .vc-meta-lbl { font-size: .72rem; font-weight: 600; color: rgba(255,255,255,.4); text-transform: uppercase; letter-spacing: .05em; margin-bottom: 4px; }
        .vc-meta-val { font-size: .92rem; font-weight: 600; color: #fff; }

        .vc-join-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 10px;
            width: 100%; padding: 15px;
            background: #2563eb; color: #fff;
            border: none; border-radius: 12px;
            font-size: 1rem; font-weight: 700;
            cursor: pointer; font-family: inherit;
            transition: background .2s;
            margin-bottom: 12px;
        }
        .vc-join-btn:hover { background: #1d4ed8; }

        .vc-rejoin-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            width: 100%; padding: 12px;
            background: transparent; color: rgba(255,255,255,.55);
            border: 1px solid rgba(255,255,255,.15); border-radius: 12px;
            font-size: .88rem; font-weight: 600; cursor: pointer; font-family: inherit;
            transition: all .2s;
        }
        .vc-rejoin-btn:hover { background: rgba(255,255,255,.06); color: #fff; border-color: rgba(255,255,255,.3); }

        .vc-note {
            margin-top: 24px; padding: 14px 16px;
            background: rgba(234,179,8,.08);
            border: 1px solid rgba(234,179,8,.2);
            border-radius: 10px;
            font-size: .8rem; color: rgba(255,255,255,.6);
            line-height: 1.6; text-align: left;
        }
        .vc-note i { color: #facc15; margin-right: 4px; }

        #vc-status { margin-bottom: 16px; display: none; }
        #vc-status span {
            display: inline-flex; align-items: center; gap: 6px;
            background: rgba(34,197,94,.1); color: #4ade80;
            border: 1px solid rgba(34,197,94,.25); border-radius: 20px;
            padding: 5px 14px; font-size: .8rem; font-weight: 600;
        }

        #balance-status { margin-top: 14px; display: none; }
        #balance-status span {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(5,150,105,.12); color: #6ee7b7;
            border: 1px solid rgba(5,150,105,.3); border-radius: 20px;
            padding: 7px 14px; font-size: .82rem; font-weight: 700;
        }
    </style>
</head>
<body>

{{-- Top bar --}}
<div class="vc-bar">
    <div class="vc-brand">
        <div class="vc-brand-logo"><i class="fas fa-shield-alt"></i></div>
        LexConnect
    </div>

    <div class="vc-info">
        <div class="vc-pill"><span class="dot"></span> Session Active</div>
        <div class="vc-info-item"><i class="fas fa-hashtag"></i> {{ $consultation->code }}</div>
        <div class="vc-info-item"><i class="fas fa-clock"></i> {{ $consultation->duration_minutes }} min session</div>
        <div class="vc-info-item"><i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($consultation->scheduled_at)->format('M d, Y g:i A') }}</div>
    </div>

    @if(Auth::user()->role === 'lawyer')
        <form method="POST" action="{{ route('consultations.video.end', $consultation) }}" style="margin:0;">
            @csrf
            <button type="submit" class="vc-end-btn" onclick="return confirm('End the session and mark it as completed?')">
                <i class="fas fa-phone-slash"></i> End Session
            </button>
        </form>
    @else
        <a href="{{ $returnRoute }}" class="vc-end-btn">
            <i class="fas fa-sign-out-alt"></i> Leave Session
        </a>
    @endif
</div>

{{-- Session panel --}}
<div class="vc-main">
    <div class="vc-panel">
        <div class="vc-icon-ring">
            <i class="fas fa-video"></i>
        </div>

        <div class="vc-session-title">Your video room is ready</div>
        <div class="vc-session-sub">
            Click the button below to start the video call.<br>
            The call opens in a separate window — keep this page open so you can end the session when done.
        </div>

        @php $jitsiUrl = 'https://meet.jit.si/' . rawurlencode($roomName); @endphp

        <div class="vc-meta-grid">
            <div class="vc-meta-box">
                <div class="vc-meta-lbl">Consultation Code</div>
                <div class="vc-meta-val">{{ $consultation->code }}</div>
            </div>
            <div class="vc-meta-box">
                <div class="vc-meta-lbl">Your Name</div>
                <div class="vc-meta-val">{{ $displayName }}</div>
            </div>
            <div class="vc-meta-box">
                <div class="vc-meta-lbl">Duration</div>
                <div class="vc-meta-val">{{ $consultation->duration_minutes }} minutes</div>
            </div>
            <div class="vc-meta-box">
                <div class="vc-meta-lbl">Scheduled At</div>
                <div class="vc-meta-val">{{ \Carbon\Carbon::parse($consultation->scheduled_at)->format('g:i A') }}</div>
            </div>
        </div>

        <div id="vc-status">
            <span><i class="fas fa-circle" style="font-size:.45rem;"></i> Video call window is open</span>
        </div>
        @if(Auth::user()->role !== 'lawyer')
        <div id="balance-status">
            <span><i class="fas fa-credit-card"></i> Session ended. Opening remaining balance payment...</span>
        </div>
        @endif

        <button type="button" class="vc-join-btn" id="joinBtn" onclick="openCall()">
            <i class="fas fa-video"></i> Start Video Call
        </button>

        <button type="button" class="vc-rejoin-btn" onclick="openCall()">
            <i class="fas fa-redo"></i> Reopen / Rejoin Call
        </button>

        <div class="vc-note">
            <i class="fas fa-info-circle"></i>
            The video call opens in a separate floating window — camera and microphone will work normally.
            When done, close that window then click
            <strong>{{ Auth::user()->role === 'lawyer' ? 'End Session' : 'Leave Session' }}</strong> above.
        </div>
    </div>
</div>

<script>
    const jitsiUrl = @json($jitsiUrl);
    const isClient = @json(Auth::user()->role !== 'lawyer');
    const statusUrl = @json(route('consultations.video.status', $consultation));
    let callWindow = null;
    let balanceRedirecting = false;

    function openCall() {
        const w = Math.min(1280, screen.availWidth  - 40);
        const h = Math.min(820,  screen.availHeight - 40);
        const l = Math.round((screen.availWidth  - w) / 2);
        const t = Math.round((screen.availHeight - h) / 2);

        callWindow = window.open(
            jitsiUrl,
            'jitsi-call',
            `width=${w},height=${h},left=${l},top=${t},toolbar=no,menubar=no,scrollbars=no,resizable=yes`
        );

        if (callWindow) {
            document.getElementById('vc-status').style.display = 'block';
            document.getElementById('joinBtn').innerHTML = '<i class="fas fa-circle" style="font-size:.5rem;color:#4ade80;vertical-align:middle;margin-right:4px;"></i> Call in progress…';
        } else {
            // Popup was blocked by browser — fall back to new tab with a notice
            alert('Your browser blocked the popup. Please allow popups for this site, or the call will open in a new tab.');
            window.open(jitsiUrl, '_blank', 'noopener,noreferrer');
        }
    }

    // Auto-open the call window when the page loads
    window.addEventListener('load', function () {
        // Small delay so the browser doesn't treat it as unwanted popup
        setTimeout(openCall, 500);

        if (isClient) {
            setInterval(checkSessionStatus, 5000);
            setTimeout(checkSessionStatus, 1500);
        }
    });

    function checkSessionStatus() {
        if (balanceRedirecting) return;

        fetch(statusUrl, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
            .then(function(response) {
                if (!response.ok) throw new Error('Could not check session status.');
                return response.json();
            })
            .then(function(data) {
                if (data.status === 'completed' && data.balance_checkout_url) {
                    balanceRedirecting = true;
                    var status = document.getElementById('balance-status');
                    if (status) status.style.display = 'block';
                    setTimeout(function() {
                        window.location.href = data.balance_checkout_url;
                    }, 900);
                }
            })
            .catch(function() {
                // Keep polling quietly; transient network errors should not interrupt the call.
            });
    }
</script>

</body>
</html>

