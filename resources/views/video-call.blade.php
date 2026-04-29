<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LexConnect - Video Call</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/js/app.js'])
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background:
                radial-gradient(circle at top left, rgba(37, 99, 235, 0.2), transparent 32%),
                radial-gradient(circle at bottom right, rgba(14, 165, 233, 0.12), transparent 28%),
                #081120;
            color: #e5eefc;
            min-height: 100vh;
        }
        .page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            gap: 20px;
            padding: 20px;
        }
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 16px 20px;
            border: 1px solid rgba(148, 163, 184, 0.18);
            border-radius: 18px;
            background: rgba(8, 17, 32, 0.82);
            backdrop-filter: blur(14px);
        }
        .brand-block {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .brand-mark {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #2563eb, #0ea5e9);
            color: #fff;
            box-shadow: 0 12px 30px rgba(37, 99, 235, 0.28);
        }
        .brand-title {
            font-size: 1rem;
            font-weight: 700;
        }
        .brand-sub {
            margin-top: 3px;
            color: #94a3b8;
            font-size: 0.82rem;
        }
        .topbar-meta {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }
        .meta-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.92);
            border: 1px solid rgba(148, 163, 184, 0.18);
            color: #cbd5e1;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(8, 145, 178, 0.16);
            color: #67e8f9;
            border: 1px solid rgba(34, 211, 238, 0.24);
            font-size: 0.8rem;
            font-weight: 700;
        }
        .status-pill.offline {
            background: rgba(217, 119, 6, 0.16);
            color: #fcd34d;
            border-color: rgba(251, 191, 36, 0.24);
        }
        .layout {
            display: grid;
            grid-template-columns: minmax(0, 1.7fr) minmax(300px, 360px);
            gap: 20px;
            flex: 1;
        }
        .call-shell,
        .side-panel {
            border: 1px solid rgba(148, 163, 184, 0.16);
            border-radius: 24px;
            background: rgba(8, 17, 32, 0.82);
            backdrop-filter: blur(14px);
            overflow: hidden;
        }
        .call-shell {
            display: flex;
            flex-direction: column;
            min-height: 0;
        }
        .video-stage {
            position: relative;
            flex: 1;
            min-height: 440px;
            background:
                linear-gradient(180deg, rgba(15, 23, 42, 0.28), rgba(2, 6, 23, 0.88)),
                #020617;
            overflow: hidden;
        }
        .remote-video,
        .local-video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            background: #020617;
        }
        .remote-placeholder {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            text-align: center;
            background:
                radial-gradient(circle at center, rgba(37, 99, 235, 0.18), transparent 42%),
                rgba(2, 6, 23, 0.82);
        }
        .placeholder-card {
            max-width: 420px;
        }
        .placeholder-icon {
            width: 82px;
            height: 82px;
            margin: 0 auto 18px;
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(37, 99, 235, 0.16);
            border: 1px solid rgba(96, 165, 250, 0.24);
            color: #93c5fd;
            font-size: 1.8rem;
        }
        .placeholder-title {
            font-size: 1.18rem;
            font-weight: 700;
            color: #f8fafc;
        }
        .placeholder-copy {
            margin-top: 10px;
            color: #94a3b8;
            line-height: 1.6;
            font-size: 0.92rem;
        }
        .local-tile {
            position: absolute;
            right: 18px;
            bottom: 18px;
            width: min(24vw, 220px);
            aspect-ratio: 4 / 3;
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid rgba(148, 163, 184, 0.22);
            background: #0f172a;
            box-shadow: 0 20px 40px rgba(2, 6, 23, 0.38);
        }
        .local-label {
            position: absolute;
            left: 12px;
            top: 12px;
            padding: 5px 10px;
            border-radius: 999px;
            background: rgba(2, 6, 23, 0.72);
            color: #f8fafc;
            font-size: 0.72rem;
            font-weight: 700;
            z-index: 2;
        }
        .video-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 18px 20px;
            border-top: 1px solid rgba(148, 163, 184, 0.16);
            background: rgba(2, 6, 23, 0.54);
        }
        .footer-status {
            min-width: 0;
        }
        .footer-status-title {
            font-size: 0.94rem;
            font-weight: 700;
            color: #f8fafc;
        }
        .footer-status-copy {
            margin-top: 6px;
            color: #94a3b8;
            font-size: 0.82rem;
            line-height: 1.5;
        }
        .controls {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }
        .ctrl-btn,
        .leave-link,
        .end-btn {
            border: none;
            border-radius: 14px;
            font-family: inherit;
            font-size: 0.88rem;
            font-weight: 700;
            cursor: pointer;
            padding: 12px 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            transition: transform 0.16s ease, background 0.16s ease, border-color 0.16s ease;
        }
        .ctrl-btn {
            background: rgba(15, 23, 42, 0.92);
            color: #e2e8f0;
            border: 1px solid rgba(148, 163, 184, 0.18);
        }
        .ctrl-btn:hover,
        .leave-link:hover,
        .end-btn:hover {
            transform: translateY(-1px);
        }
        .ctrl-btn.off {
            background: rgba(127, 29, 29, 0.8);
            color: #fee2e2;
            border-color: rgba(248, 113, 113, 0.28);
        }
        .leave-link {
            background: rgba(15, 23, 42, 0.92);
            color: #e2e8f0;
            border: 1px solid rgba(148, 163, 184, 0.18);
        }
        .end-btn {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: #fff;
            box-shadow: 0 14px 28px rgba(185, 28, 28, 0.28);
        }
        .side-panel {
            display: flex;
            flex-direction: column;
        }
        .panel-section {
            padding: 22px 22px 20px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.14);
        }
        .panel-section:last-child {
            border-bottom: none;
        }
        .eyebrow {
            color: #60a5fa;
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .panel-title {
            margin-top: 8px;
            font-size: 1.24rem;
            font-weight: 700;
            color: #f8fafc;
        }
        .panel-copy {
            margin-top: 10px;
            color: #94a3b8;
            line-height: 1.7;
            font-size: 0.9rem;
        }
        .panel-grid {
            display: grid;
            gap: 10px;
            margin-top: 18px;
        }
        .info-card {
            padding: 14px 15px;
            border-radius: 16px;
            background: rgba(15, 23, 42, 0.82);
            border: 1px solid rgba(148, 163, 184, 0.16);
        }
        .info-label {
            color: #60a5fa;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .info-value {
            margin-top: 7px;
            color: #f8fafc;
            font-size: 0.92rem;
            font-weight: 600;
        }
        .signal-box {
            padding: 14px 15px;
            border-radius: 16px;
            background: rgba(8, 145, 178, 0.12);
            border: 1px solid rgba(34, 211, 238, 0.18);
        }
        .signal-title {
            font-size: 0.78rem;
            font-weight: 700;
            color: #67e8f9;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .signal-body {
            margin-top: 8px;
            color: #dbeafe;
            font-size: 0.88rem;
            line-height: 1.55;
        }
        .checklist {
            margin: 16px 0 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: 10px;
        }
        .checklist li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            color: #cbd5e1;
            font-size: 0.88rem;
            line-height: 1.55;
        }
        .checklist i {
            margin-top: 3px;
            color: #38bdf8;
        }
        .hidden {
            display: none !important;
        }
        @media (max-width: 1120px) {
            .layout {
                grid-template-columns: 1fr;
            }
            .local-tile {
                width: min(34vw, 220px);
            }
        }
        @media (max-width: 720px) {
            .page {
                padding: 14px;
            }
            .topbar,
            .video-footer {
                flex-direction: column;
                align-items: stretch;
            }
            .topbar-meta,
            .controls {
                justify-content: flex-start;
            }
            .video-stage {
                min-height: 320px;
            }
            .local-tile {
                width: 42vw;
                min-width: 120px;
                right: 12px;
                bottom: 12px;
                border-radius: 14px;
            }
        }
    </style>
</head>
<body>
@php
    $scheduledAt = \Carbon\Carbon::parse($consultation->scheduled_at);
@endphp
<div class="page">
    <div class="topbar">
        <div class="brand-block">
            <div class="brand-mark"><i class="fas fa-scale-balanced"></i></div>
            <div>
                <div class="brand-title">LexConnect Consultation Call</div>
                <div class="brand-sub">Private one-to-one session with {{ $peerName ?? 'your consultation partner' }}</div>
            </div>
        </div>
        <div class="topbar-meta">
            <div class="status-pill offline" id="peerPresencePill">
                <i class="fas fa-user-clock"></i>
                <span id="peerPresenceText">{{ $peerName ?? 'Peer' }} not connected yet</span>
            </div>
            <div class="meta-pill"><i class="fas fa-hashtag"></i> {{ $consultation->code }}</div>
            <div class="meta-pill"><i class="fas fa-clock"></i> {{ $consultation->duration_minutes }} min</div>
            <div class="meta-pill"><i class="fas fa-calendar"></i> {{ $scheduledAt->format('M d, Y g:i A') }}</div>
        </div>
    </div>

    <div class="layout">
        <div class="call-shell">
            <div class="video-stage">
                <video id="remoteVideo" class="remote-video" autoplay playsinline></video>
                <div class="remote-placeholder" id="remotePlaceholder">
                    <div class="placeholder-card">
                        <div class="placeholder-icon"><i class="fas fa-video"></i></div>
                        <div class="placeholder-title">Waiting for {{ $peerName ?? 'the other participant' }}</div>
                        <div class="placeholder-copy" id="remotePlaceholderCopy">
                            We are preparing your camera and microphone now. The call will connect as soon as both participants are on this page.
                        </div>
                    </div>
                </div>

                <div class="local-tile">
                    <div class="local-label">You</div>
                    <video id="localVideo" class="local-video" autoplay muted playsinline></video>
                </div>
            </div>

            <div class="video-footer">
                <div class="footer-status">
                    <div class="footer-status-title" id="callStateTitle">Starting secure video session</div>
                    <div class="footer-status-copy" id="callStateCopy">
                        Camera and microphone permission will be requested on this page. Keep this tab open until the consultation is finished.
                    </div>
                </div>

                <div class="controls">
                    <button type="button" class="ctrl-btn" id="muteBtn">
                        <i class="fas fa-microphone"></i> Mute
                    </button>
                    <button type="button" class="ctrl-btn" id="cameraBtn">
                        <i class="fas fa-video"></i> Camera On
                    </button>
                    <button type="button" class="ctrl-btn" id="reconnectBtn">
                        <i class="fas fa-rotate-right"></i> Reconnect
                    </button>

                    @if(Auth::user()->role === 'lawyer')
                        <form method="POST" action="{{ route('consultations.video.end', $consultation) }}" id="endSessionForm" style="margin:0;">
                            @csrf
                            <button type="submit" class="end-btn" id="endSessionBtn">
                                <i class="fas fa-phone-slash"></i> End Session
                            </button>
                        </form>
                    @else
                        <a href="{{ $returnRoute }}" class="leave-link" id="leaveSessionLink">
                            <i class="fas fa-arrow-left"></i> Leave Session
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <aside class="side-panel">
            <div class="panel-section">
                <div class="eyebrow">Connection</div>
                <div class="panel-title">Built for direct consultation calls</div>
                <div class="panel-copy">
                    This session now runs through WebRTC with your own application signaling. No external Jitsi room is used here.
                </div>

                <div class="panel-grid">
                    <div class="info-card">
                        <div class="info-label">Participant</div>
                        <div class="info-value">{{ $displayName }}</div>
                    </div>
                    <div class="info-card">
                        <div class="info-label">Other Participant</div>
                        <div class="info-value">{{ $peerName ?? 'Not available' }}</div>
                    </div>
                    <div class="info-card">
                        <div class="info-label">Consultation Type</div>
                        <div class="info-value">{{ ucfirst($consultation->type) }} consultation</div>
                    </div>
                </div>
            </div>

            <div class="panel-section">
                <div class="signal-box">
                    <div class="signal-title">Session Status</div>
                    <div class="signal-body" id="sessionSignalText">
                        Waiting for media permissions and consultation presence channel.
                    </div>
                </div>

                @if(Auth::user()->role !== 'lawyer')
                    <div class="signal-box hidden" id="balanceSignalBox" style="margin-top:12px;background:rgba(22, 163, 74, 0.12);border-color:rgba(74, 222, 128, 0.18);">
                        <div class="signal-title" style="color:#86efac;">Balance Payment</div>
                        <div class="signal-body" id="balanceSignalText">
                            The lawyer has ended the consultation. Redirecting to your remaining balance payment.
                        </div>
                    </div>
                @endif
            </div>

            <div class="panel-section">
                <div class="eyebrow">Checklist</div>
                <ul class="checklist">
                    <li><i class="fas fa-check-circle"></i><span>Allow camera and microphone access when the browser asks.</span></li>
                    <li><i class="fas fa-check-circle"></i><span>Stay on this page while the session is active so the consultation status stays in sync.</span></li>
                    <li><i class="fas fa-check-circle"></i><span>If the other participant reconnects, the call will renegotiate automatically.</span></li>
                </ul>
            </div>
        </aside>
    </div>
</div>

<script>
    const consultationId = @json($consultation->id);
    const currentUserId = @json($currentUserId);
    const currentUserRole = @json($currentUserRole);
    const displayName = @json($displayName);
    const peerId = @json($peerId);
    const peerName = @json($peerName);
    const signalingChannelName = @json($echoSignalingChannel);
    const presenceSignalingChannelName = @json($presenceSignalingChannel);
    const statusUrl = @json(route('consultations.video.status', $consultation));
    const iceServers = @json($iceServers);
    const isClient = @json(Auth::user()->role !== 'lawyer');

    const localVideo = document.getElementById('localVideo');
    const remoteVideo = document.getElementById('remoteVideo');
    const remotePlaceholder = document.getElementById('remotePlaceholder');
    const remotePlaceholderCopy = document.getElementById('remotePlaceholderCopy');
    const callStateTitle = document.getElementById('callStateTitle');
    const callStateCopy = document.getElementById('callStateCopy');
    const sessionSignalText = document.getElementById('sessionSignalText');
    const peerPresencePill = document.getElementById('peerPresencePill');
    const peerPresenceText = document.getElementById('peerPresenceText');
    const muteBtn = document.getElementById('muteBtn');
    const cameraBtn = document.getElementById('cameraBtn');
    const reconnectBtn = document.getElementById('reconnectBtn');
    const balanceSignalBox = document.getElementById('balanceSignalBox');
    const balanceSignalText = document.getElementById('balanceSignalText');

    let localStream = null;
    let remoteStream = null;
    let peerConnection = null;
    let presenceChannel = null;
    let pendingIceCandidates = [];
    let peerOnline = false;
    let isMuted = false;
    let isCameraOff = false;
    let balanceRedirecting = false;
    let isStarting = false;

    function setCallState(title, copy) {
        callStateTitle.textContent = title;
        callStateCopy.textContent = copy;
        sessionSignalText.textContent = copy;
    }

    function updatePeerPresence(online) {
        peerOnline = online;
        peerPresencePill.classList.toggle('offline', !online);
        peerPresenceText.textContent = online
            ? (peerName || 'Peer') + ' is on this consultation page'
            : (peerName || 'Peer') + ' not connected yet';

        if (!online) {
            remotePlaceholder.classList.remove('hidden');
            remotePlaceholderCopy.textContent = 'We will connect as soon as both participants are on this page.';
        }
    }

    async function ensureLocalMedia() {
        if (localStream) {
            return localStream;
        }

        localStream = await navigator.mediaDevices.getUserMedia({
            audio: true,
            video: {
                width: { ideal: 1280 },
                height: { ideal: 720 },
                facingMode: 'user',
            },
        });

        localVideo.srcObject = localStream;
        setCallState(
            'Local media ready',
            'Your camera and microphone are active. Waiting for the secure peer connection to finish.'
        );

        return localStream;
    }

    function attachLocalTracks() {
        if (!peerConnection || !localStream) {
            return;
        }

        const existingTrackIds = peerConnection.getSenders()
            .map((sender) => sender.track && sender.track.id)
            .filter(Boolean);

        localStream.getTracks().forEach((track) => {
            if (!existingTrackIds.includes(track.id)) {
                peerConnection.addTrack(track, localStream);
            }
        });
    }

    function clearRemoteMedia() {
        remoteStream = null;
        remoteVideo.srcObject = null;
        remotePlaceholder.classList.remove('hidden');
    }

    function teardownPeerConnection() {
        pendingIceCandidates = [];

        if (peerConnection) {
            peerConnection.ontrack = null;
            peerConnection.onicecandidate = null;
            peerConnection.onconnectionstatechange = null;
            peerConnection.oniceconnectionstatechange = null;
            peerConnection.close();
            peerConnection = null;
        }
        clearRemoteMedia();
    }

    async function flushPendingIceCandidates(pc) {
        if (!pc || !pc.remoteDescription) {
            return;
        }

        const queuedCandidates = pendingIceCandidates.slice();
        pendingIceCandidates = [];

        for (const candidate of queuedCandidates) {
            try {
                await pc.addIceCandidate(new RTCIceCandidate(candidate));
            } catch (error) {
                console.warn('Ignored stale ICE candidate:', error);
            }
        }
    }

    function createPeerConnection() {
        if (peerConnection) {
            return peerConnection;
        }

        peerConnection = new RTCPeerConnection({ iceServers });

        peerConnection.onicecandidate = function(event) {
            if (event.candidate) {
                whisperSignal({
                    type: 'ice-candidate',
                    candidate: event.candidate,
                });
            }
        };

        peerConnection.ontrack = function(event) {
            if (!remoteStream) {
                remoteStream = new MediaStream();
            }

            event.streams[0].getTracks().forEach((track) => {
                if (!remoteStream.getTracks().find((existing) => existing.id === track.id)) {
                    remoteStream.addTrack(track);
                }
            });

            remoteVideo.srcObject = remoteStream;
            remotePlaceholder.classList.add('hidden');
            setCallState(
                'Call connected',
                'Secure media is flowing between both consultation participants.'
            );
        };

        peerConnection.onconnectionstatechange = function() {
            const state = peerConnection.connectionState;

            if (state === 'connected') {
                remotePlaceholder.classList.add('hidden');
                setCallState(
                    'Call connected',
                    'You and ' + (peerName || 'the other participant') + ' are now in the consultation.'
                );
            } else if (state === 'connecting') {
                setCallState(
                    'Connecting call',
                    'Negotiating the direct media connection now.'
                );
            } else if (state === 'disconnected') {
                remotePlaceholder.classList.remove('hidden');
                remotePlaceholderCopy.textContent = (peerName || 'The other participant') + ' disconnected. We will reconnect if they return.';
                setCallState(
                    'Peer disconnected',
                    'The direct connection was interrupted. You can wait or use reconnect once both participants are back.'
                );
            } else if (state === 'failed') {
                setCallState(
                    'Connection failed',
                    'The direct connection failed. Try reconnecting after confirming the TURN server is configured.'
                );
            } else if (state === 'closed') {
                remotePlaceholder.classList.remove('hidden');
            }
        };

        peerConnection.oniceconnectionstatechange = function() {
            if (!peerConnection) {
                return;
            }

            if (peerConnection.iceConnectionState === 'failed') {
                setCallState(
                    'ICE connection failed',
                    'The browser could not finish NAT traversal. A TURN server may be required for this network.'
                );
            }
        };

        attachLocalTracks();
        return peerConnection;
    }

    function whisperSignal(payload) {
        if (!presenceChannel || !peerId) {
            return;
        }

        presenceChannel.whisper('signal', {
            ...payload,
            consultationId,
            fromUserId: currentUserId,
            fromRole: currentUserRole,
            targetUserId: peerId,
            sentAt: Date.now(),
        });
    }

    async function createOffer() {
        if (currentUserRole !== 'lawyer' || !peerOnline) {
            return;
        }

        await ensureLocalMedia();
        const pc = createPeerConnection();

        if (pc.signalingState !== 'stable') {
            return;
        }

        const offer = await pc.createOffer();
        await pc.setLocalDescription(offer);

        whisperSignal({
            type: 'offer',
            sdp: offer,
        });

        setCallState(
            'Offer sent',
            'Waiting for ' + (peerName || 'the other participant') + ' to answer the consultation call.'
        );
    }

    async function handleSignal(payload) {
        if (!payload || payload.fromUserId === currentUserId || payload.targetUserId !== currentUserId) {
            return;
        }

        try {
            if (payload.type === 'peer-ready') {
                if (currentUserRole === 'lawyer') {
                    await createOffer();
                }
                return;
            }

            if (payload.type === 'hangup') {
                teardownPeerConnection();
                setCallState(
                    'Peer left the session',
                    (peerName || 'The other participant') + ' left the consultation page.'
                );
                return;
            }

            await ensureLocalMedia();
            const pc = createPeerConnection();

            if (payload.type === 'offer') {
                if (pc.signalingState !== 'stable') {
                    teardownPeerConnection();
                }

                const refreshedPc = createPeerConnection();
                await refreshedPc.setRemoteDescription(new RTCSessionDescription(payload.sdp));
                await flushPendingIceCandidates(refreshedPc);
                const answer = await refreshedPc.createAnswer();
                await refreshedPc.setLocalDescription(answer);

                whisperSignal({
                    type: 'answer',
                    sdp: answer,
                });

                setCallState(
                    'Answer sent',
                    'The call answer was sent. Finishing the direct media connection now.'
                );
            } else if (payload.type === 'answer') {
                await pc.setRemoteDescription(new RTCSessionDescription(payload.sdp));
                await flushPendingIceCandidates(pc);
                setCallState(
                    'Answer received',
                    'Secure media is finalizing. The remote video should appear shortly.'
                );
            } else if (payload.type === 'ice-candidate' && payload.candidate) {
                if (pc.remoteDescription) {
                    await pc.addIceCandidate(new RTCIceCandidate(payload.candidate));
                } else {
                    pendingIceCandidates.push(payload.candidate);
                }
            }
        } catch (error) {
            console.error('WebRTC signaling error:', error);
            setCallState(
                'Call setup error',
                'A signaling error interrupted the consultation call. Use reconnect to retry.'
            );
        }
    }

    function setupPresenceChannel() {
        if (!window.Echo) {
            setCallState(
                'Realtime connection unavailable',
                'The consultation signaling channel is not ready yet. Refresh if this does not recover.'
            );
            return;
        }

        presenceChannel = window.Echo.join(signalingChannelName)
            .here(function(users) {
                const online = users.some(function(user) {
                    return Number(user.id) === Number(peerId);
                });

                updatePeerPresence(online);

                if (online) {
                    if (currentUserRole === 'lawyer') {
                        createOffer();
                    } else {
                        whisperSignal({ type: 'peer-ready' });
                    }
                }
            })
            .joining(function(user) {
                if (Number(user.id) !== Number(peerId)) {
                    return;
                }

                updatePeerPresence(true);

                if (currentUserRole === 'lawyer') {
                    createOffer();
                } else {
                    whisperSignal({ type: 'peer-ready' });
                }
            })
            .leaving(function(user) {
                if (Number(user.id) !== Number(peerId)) {
                    return;
                }

                updatePeerPresence(false);
                teardownPeerConnection();
                setCallState(
                    'Peer disconnected',
                    (peerName || 'The other participant') + ' left the consultation page. The call will resume if they come back.'
                );
            })
            .listenForWhisper('signal', handleSignal);
    }

    function updateMuteButton() {
        muteBtn.classList.toggle('off', isMuted);
        muteBtn.innerHTML = isMuted
            ? '<i class="fas fa-microphone-slash"></i> Unmute'
            : '<i class="fas fa-microphone"></i> Mute';
    }

    function updateCameraButton() {
        cameraBtn.classList.toggle('off', isCameraOff);
        cameraBtn.innerHTML = isCameraOff
            ? '<i class="fas fa-video-slash"></i> Camera Off'
            : '<i class="fas fa-video"></i> Camera On';
    }

    async function startConsultationCall() {
        if (isStarting) {
            return;
        }

        isStarting = true;

        try {
            await ensureLocalMedia();
            setupPresenceChannel();

            if (isClient) {
                setInterval(checkSessionStatus, 5000);
                setTimeout(checkSessionStatus, 1200);
            }
        } catch (error) {
            console.error('Failed to start call page:', error);
            remotePlaceholder.classList.remove('hidden');
            remotePlaceholderCopy.textContent = 'Camera or microphone access was blocked. Allow permissions and use reconnect to try again.';
            setCallState(
                'Permissions required',
                'We could not access your camera or microphone. Please allow both permissions in the browser.'
            );
        } finally {
            isStarting = false;
        }
    }

    async function reconnectCall() {
        teardownPeerConnection();
        clearRemoteMedia();

        try {
            await ensureLocalMedia();

            if (!presenceChannel) {
                setupPresenceChannel();
            }

            if (currentUserRole === 'lawyer') {
                await createOffer();
            } else {
                whisperSignal({ type: 'peer-ready' });
                setCallState(
                    'Reconnect requested',
                    'Waiting for ' + (peerName || 'the other participant') + ' to renegotiate the consultation call.'
                );
            }
        } catch (error) {
            console.error('Reconnect failed:', error);
            setCallState(
                'Reconnect failed',
                'We could not restart local media. Please refresh the page after checking permissions.'
            );
        }
    }

    function cleanupBeforeExit() {
        whisperSignal({ type: 'hangup' });

        if (presenceChannel && window.Echo) {
            window.Echo.leave(signalingChannelName);
            presenceChannel = null;
        }

        teardownPeerConnection();

        if (localStream) {
            localStream.getTracks().forEach(function(track) {
                track.stop();
            });
            localStream = null;
        }
    }

    function checkSessionStatus() {
        if (balanceRedirecting) {
            return;
        }

        fetch(statusUrl, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        })
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('Could not fetch session status.');
                }
                return response.json();
            })
            .then(function(data) {
                if (data.status === 'completed' && data.balance_checkout_url) {
                    balanceRedirecting = true;
                    if (balanceSignalBox) {
                        balanceSignalBox.classList.remove('hidden');
                    }
                    if (balanceSignalText) {
                        balanceSignalText.textContent = 'The lawyer ended the consultation. Redirecting to the remaining balance checkout now.';
                    }
                    setTimeout(function() {
                        window.location.href = data.balance_checkout_url;
                    }, 900);
                }
            })
            .catch(function() {
                // Keep polling quietly.
            });
    }

    muteBtn.addEventListener('click', function() {
        if (!localStream) {
            return;
        }

        isMuted = !isMuted;
        localStream.getAudioTracks().forEach(function(track) {
            track.enabled = !isMuted;
        });
        updateMuteButton();
    });

    cameraBtn.addEventListener('click', function() {
        if (!localStream) {
            return;
        }

        isCameraOff = !isCameraOff;
        localStream.getVideoTracks().forEach(function(track) {
            track.enabled = !isCameraOff;
        });
        updateCameraButton();
    });

    reconnectBtn.addEventListener('click', reconnectCall);

    const leaveSessionLink = document.getElementById('leaveSessionLink');
    if (leaveSessionLink) {
        leaveSessionLink.addEventListener('click', cleanupBeforeExit);
    }

    const endSessionForm = document.getElementById('endSessionForm');
    if (endSessionForm) {
        endSessionForm.addEventListener('submit', function(event) {
            const confirmed = window.confirm('End this consultation and mark it as completed?');
            if (!confirmed) {
                event.preventDefault();
                return;
            }
            cleanupBeforeExit();
        });
    }

    window.addEventListener('beforeunload', cleanupBeforeExit);
    updateMuteButton();
    updateCameraButton();

    window.addEventListener('load', function() {
        setTimeout(startConsultationCall, 350);
    });
</script>
</body>
</html>
