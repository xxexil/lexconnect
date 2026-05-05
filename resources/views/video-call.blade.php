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
            width: 100%;
            max-width: 1180px;
            margin: 0 auto;
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
            align-items: start;
            width: 100%;
            max-width: 1180px;
            margin: 0 auto;
            flex: 0 1 auto;
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
        .call-shell:fullscreen {
            width: 100vw;
            height: 100vh;
            border-radius: 0;
            background: #020617;
        }
        .call-shell:fullscreen .video-stage {
            flex: 1 1 auto;
            max-height: none;
        }
        .video-stage {
            position: relative;
            flex: 0 0 auto;
            aspect-ratio: 16 / 9;
            min-height: 360px;
            max-height: 620px;
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
        .local-video {
            transform: scaleX(-1);
        }
        .remote-video.screen-share-video,
        .local-video.screen-share-video {
            object-fit: contain;
            background: #020617;
        }
        .local-video.screen-share-video {
            transform: none;
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
        .remote-muted-badge {
            position: absolute;
            left: 18px;
            top: 18px;
            z-index: 5;
            display: none;
            align-items: center;
            gap: 8px;
            padding: 9px 12px;
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.88);
            border: 1px solid rgba(248, 113, 113, 0.24);
            color: #fecaca;
            font-size: 0.82rem;
            font-weight: 800;
            box-shadow: 0 16px 34px rgba(2, 6, 23, 0.28);
        }
        .remote-muted-badge.visible {
            display: inline-flex;
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
            cursor: grab;
            touch-action: none;
            user-select: none;
            z-index: 6;
            transition: left 0.22s cubic-bezier(.2, 1.2, .32, 1), top 0.16s ease;
        }
        .local-tile.dragging {
            cursor: grabbing;
            transition: none;
        }
        .local-tile.hidden-tile {
            display: none;
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
        .local-hide-btn {
            position: absolute;
            right: 10px;
            top: 10px;
            width: 30px;
            height: 30px;
            border-radius: 999px;
            border: 1px solid rgba(226, 232, 240, 0.18);
            background: rgba(2, 6, 23, 0.72);
            color: #f8fafc;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 3;
        }
        .local-hide-btn:hover {
            background: rgba(15, 23, 42, 0.92);
        }
        .local-restore-tab {
            position: absolute;
            top: 50%;
            width: 38px;
            height: 74px;
            border: 1px solid rgba(148, 163, 184, 0.24);
            background: rgba(15, 23, 42, 0.92);
            color: #f8fafc;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 7;
            box-shadow: 0 16px 34px rgba(2, 6, 23, 0.34);
        }
        .local-restore-tab.visible {
            display: inline-flex;
        }
        .local-restore-tab.left {
            left: 0;
            border-left: none;
            border-radius: 0 16px 16px 0;
        }
        .local-restore-tab.right {
            right: 0;
            border-right: none;
            border-radius: 16px 0 0 16px;
        }
        .local-restore-tab:hover {
            background: rgba(30, 41, 59, 0.96);
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
            gap: 8px;
            flex-wrap: wrap;
            justify-content: flex-end;
            align-items: center;
        }
        .ctrl-btn,
        .leave-link,
        .end-btn {
            min-height: 52px;
            border: 1px solid rgba(148, 163, 184, 0.22);
            border-radius: 14px;
            font-family: inherit;
            font-size: 0.88rem;
            font-weight: 700;
            cursor: pointer;
            padding: 0 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            background: rgba(15, 23, 42, 0.88);
            color: #e2e8f0;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04);
            transition: transform 0.16s ease, background 0.16s ease, border-color 0.16s ease, color 0.16s ease;
        }
        .ctrl-btn:hover,
        .leave-link:hover,
        .end-btn:hover {
            transform: translateY(-1px);
            background: rgba(30, 41, 59, 0.94);
            border-color: rgba(148, 163, 184, 0.34);
        }
        .ctrl-btn.off {
            background: rgba(127, 29, 29, 0.8);
            color: #fee2e2;
            border-color: rgba(248, 113, 113, 0.28);
        }
        .mic-control,
        .camera-control {
            position: relative;
            display: inline-flex;
            align-items: center;
            height: 52px;
            border-radius: 14px;
            background: rgba(15, 23, 42, 0.88);
            border: 1px solid rgba(148, 163, 184, 0.22);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04);
            overflow: visible;
        }
        .mic-control:hover,
        .camera-control:hover {
            background: rgba(30, 41, 59, 0.94);
            border-color: rgba(148, 163, 184, 0.34);
        }
        .device-select-wrap {
            position: relative;
            width: 42px;
            height: 100%;
            flex: 0 0 42px;
            border-radius: 14px 0 0 14px;
            cursor: pointer;
            border-right: 1px solid rgba(148, 163, 184, 0.16);
            transition: background 0.16s ease;
        }
        .device-select-wrap:hover,
        .device-select-wrap.open {
            background: rgba(51, 65, 85, 0.72);
        }
        .device-select-wrap .device-select-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            z-index: 1;
            left: 16px;
            font-size: 0.76rem;
            color: #a7b0bf;
        }
        .device-select-wrap.open .device-select-arrow {
            transform: translateY(-50%) rotate(180deg);
        }
        .device-select {
            position: absolute;
            width: 1px;
            height: 1px;
            opacity: 0;
            pointer-events: none;
        }
        .device-select:disabled {
            cursor: not-allowed;
            opacity: 0.55;
        }
        .device-menu {
            position: absolute;
            left: 0;
            bottom: calc(100% + 10px);
            width: min(360px, 82vw);
            max-height: 270px;
            overflow-y: auto;
            display: none;
            padding: 8px;
            border-radius: 16px;
            background: rgba(15, 23, 42, 0.98);
            border: 1px solid rgba(148, 163, 184, 0.2);
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.42);
            z-index: 25;
        }
        .device-menu.open {
            display: grid;
            gap: 5px;
        }
        .camera-control .device-menu {
            left: auto;
            right: 0;
        }
        .device-menu-option {
            width: 100%;
            border: none;
            border-radius: 12px;
            background: transparent;
            color: #cbd5e1;
            display: grid;
            grid-template-columns: 24px minmax(0, 1fr) 18px;
            align-items: center;
            gap: 10px;
            padding: 10px 11px;
            font: inherit;
            font-size: 0.84rem;
            font-weight: 700;
            text-align: left;
            cursor: pointer;
        }
        .device-menu-option:hover,
        .device-menu-option.active {
            background: rgba(37, 99, 235, 0.18);
            color: #f8fafc;
        }
        .device-menu-option .device-option-icon {
            color: #93c5fd;
            text-align: center;
        }
        .device-menu-option .device-option-name {
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .device-menu-option .device-option-check {
            color: #38bdf8;
            opacity: 0;
        }
        .device-menu-option.active .device-option-check {
            opacity: 1;
        }
        .device-menu-empty {
            padding: 12px;
            color: #94a3b8;
            font-size: 0.84rem;
            font-weight: 700;
        }
        .mic-toggle-btn,
        .camera-toggle-btn {
            position: relative;
            width: 52px;
            height: 52px;
            border: none;
            border-radius: 0 14px 14px 0;
            background: transparent;
            color: #e5e7eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.05rem;
            cursor: pointer;
            transition: background 0.16s ease, color 0.16s ease;
        }
        .mic-toggle-btn:hover,
        .camera-toggle-btn:hover {
            background: rgba(51, 65, 85, 0.72);
        }
        .mic-toggle-btn.off,
        .camera-toggle-btn.off {
            color: #fecaca;
            background: rgba(127, 29, 29, 0.72);
        }
        .mic-permission-badge,
        .camera-permission-badge {
            position: absolute;
            right: 0;
            top: -3px;
            width: 16px;
            height: 19px;
            border-radius: 999px;
            background: #facc15;
            color: #1f2937;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 0.72rem;
            font-weight: 900;
            line-height: 1;
            z-index: 3;
            box-shadow: 0 8px 18px rgba(250, 204, 21, 0.22);
        }
        .mic-control.permission-needed .mic-permission-badge,
        .camera-control.permission-needed .camera-permission-badge {
            display: inline-flex;
        }
        .leave-link {
            color: #e2e8f0;
        }
        .end-btn {
            background: rgba(185, 28, 28, 0.92);
            border-color: rgba(248, 113, 113, 0.34);
            color: #fff;
        }
        .end-btn:hover {
            background: rgba(220, 38, 38, 0.96);
            border-color: rgba(252, 165, 165, 0.46);
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
        .call-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(2, 6, 23, 0.72);
            backdrop-filter: blur(8px);
        }
        .call-modal-backdrop.visible {
            display: flex;
        }
        .call-modal {
            width: min(430px, 100%);
            border: 1px solid rgba(248, 113, 113, 0.24);
            border-radius: 22px;
            background: #0f172a;
            box-shadow: 0 28px 70px rgba(0, 0, 0, 0.42);
            overflow: hidden;
        }
        .call-modal-body {
            padding: 24px 24px 18px;
        }
        .call-modal-icon {
            width: 54px;
            height: 54px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(239, 68, 68, 0.16);
            border: 1px solid rgba(248, 113, 113, 0.22);
            color: #fca5a5;
            font-size: 1.25rem;
            margin-bottom: 16px;
        }
        .call-modal-title {
            margin: 0;
            color: #f8fafc;
            font-size: 1.1rem;
            font-weight: 800;
        }
        .call-modal-copy {
            margin: 9px 0 0;
            color: #cbd5e1;
            line-height: 1.6;
            font-size: 0.92rem;
        }
        .call-modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 16px 20px 20px;
        }
        .call-modal-btn {
            border: 1px solid rgba(148, 163, 184, 0.2);
            border-radius: 12px;
            background: rgba(15, 23, 42, 0.92);
            color: #e2e8f0;
            padding: 11px 14px;
            font: inherit;
            font-size: 0.86rem;
            font-weight: 800;
            cursor: pointer;
        }
        .call-modal-btn.primary {
            border-color: transparent;
            background: #2563eb;
            color: #fff;
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
                max-width: 900px;
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
                aspect-ratio: auto;
                min-height: 320px;
                max-height: none;
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
            <div class="meta-pill"><i class="fas fa-calendar"></i> {{ $scheduledAt->format('M d, Y g:i A') }}</div>
        </div>
    </div>

    <div class="layout">
        <div class="call-shell">
            <div class="video-stage">
                <video id="remoteVideo" class="remote-video" autoplay playsinline></video>
                <div class="remote-muted-badge" id="remoteMutedBadge">
                    <i class="fas fa-microphone-slash"></i>
                    <span>{{ $peerName ?? 'Participant' }} muted</span>
                </div>
                <div class="remote-placeholder" id="remotePlaceholder">
                    <div class="placeholder-card">
                        <div class="placeholder-icon"><i class="fas fa-video"></i></div>
                        <div class="placeholder-title">Waiting for {{ $peerName ?? 'the other participant' }}</div>
                        <div class="placeholder-copy" id="remotePlaceholderCopy">
                            We are preparing your camera and microphone now. The call will connect as soon as both participants are on this page.
                        </div>
                    </div>
                </div>

                <div class="local-tile" id="localTile">
                    <div class="local-label">You</div>
                    <button type="button" class="local-hide-btn" id="hideLocalTileBtn" title="Hide camera preview" aria-label="Hide camera preview">
                        <i class="fas fa-eye-slash"></i>
                    </button>
                    <video id="localVideo" class="local-video" autoplay muted playsinline></video>
                </div>
                <button type="button" class="local-restore-tab right" id="restoreLocalTileTab" title="Show camera preview" aria-label="Show camera preview">
                    <i class="fas fa-eye"></i>
                </button>
            </div>

            <div class="video-footer">
                <div class="footer-status">
                    <div class="footer-status-title" id="callStateTitle">Starting secure video session</div>
                    <div class="footer-status-copy" id="callStateCopy">
                        Camera and microphone permission will be requested on this page. Keep this tab open until the consultation is finished.
                    </div>
                </div>

                <div class="controls">
                    <div class="mic-control permission-needed" id="micControl" title="Permission needed">
                        <div class="device-select-wrap" id="microphonePicker" role="button" tabindex="0" aria-label="Choose microphone" aria-haspopup="listbox" aria-expanded="false" title="Choose microphone">
                            <select class="device-select" id="microphoneSelect" aria-label="Choose microphone" disabled>
                                <option value="">Default microphone</option>
                            </select>
                            <i class="fas fa-chevron-up device-select-arrow"></i>
                            <div class="device-menu" id="microphoneMenu" role="listbox" aria-label="Microphones"></div>
                        </div>
                        <button type="button" class="mic-toggle-btn" id="muteBtn" aria-label="Mute microphone" title="Mute microphone">
                            <i class="fas fa-microphone"></i>
                        </button>
                        <span class="mic-permission-badge" id="micPermissionBadge" title="Permission needed">!</span>
                    </div>
                    <div class="camera-control permission-needed" id="cameraControl" title="Permission needed">
                        <div class="device-select-wrap" id="cameraPicker" role="button" tabindex="0" aria-label="Choose camera" aria-haspopup="listbox" aria-expanded="false" title="Choose camera">
                            <select class="device-select" id="cameraSelect" aria-label="Choose camera" disabled>
                                <option value="">Default camera</option>
                            </select>
                            <i class="fas fa-chevron-up device-select-arrow"></i>
                            <div class="device-menu" id="cameraMenu" role="listbox" aria-label="Cameras"></div>
                        </div>
                        <button type="button" class="camera-toggle-btn" id="cameraBtn" aria-label="Turn camera off" title="Turn camera off">
                            <i class="fas fa-video"></i>
                        </button>
                        <span class="camera-permission-badge" id="cameraPermissionBadge" title="Permission needed">!</span>
                    </div>
                    <button type="button" class="ctrl-btn" id="shareScreenBtn">
                        <i class="fas fa-display"></i> Share Screen
                    </button>
                    <button type="button" class="ctrl-btn" id="fullscreenBtn">
                        <i class="fas fa-expand"></i> Full Screen
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
                <div class="eyebrow">Call Details</div>
                <div class="panel-title">{{ $peerName ?? 'Consultation partner' }}</div>
                <div class="panel-grid">
                    <div class="info-card">
                        <div class="info-label">Calling</div>
                        <div class="info-value">{{ $peerName ?? 'Not available' }}</div>
                    </div>
                    <div class="info-card">
                        <div class="info-label">Date & Time</div>
                        <div class="info-value">{{ $scheduledAt->format('M d, Y g:i A') }}</div>
                    </div>
                </div>
            </div>

            @if(Auth::user()->role !== 'lawyer')
                <div class="signal-box hidden" id="balanceSignalBox" style="display:none;">
                    <div class="signal-body" id="balanceSignalText"></div>
                </div>
            @endif
        </aside>
    </div>
</div>

<div class="call-modal-backdrop" id="cameraWarningModal" role="dialog" aria-modal="true" aria-labelledby="cameraWarningTitle" aria-hidden="true">
    <div class="call-modal">
        <div class="call-modal-body">
            <div class="call-modal-icon"><i class="fas fa-video-slash"></i></div>
            <h2 class="call-modal-title" id="cameraWarningTitle">No camera connected</h2>
            <p class="call-modal-copy" id="cameraWarningCopy">
                We could not find a camera connected to this device. Connect a camera or enable your built-in camera, then try reconnecting.
            </p>
        </div>
        <div class="call-modal-actions">
            <button type="button" class="call-modal-btn" id="cameraWarningCloseBtn">Close</button>
            <button type="button" class="call-modal-btn primary" id="cameraWarningReconnectBtn">
                <i class="fas fa-rotate-right"></i> Reconnect
            </button>
        </div>
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
    const callPresenceChannelName = (presenceSignalingChannelName || signalingChannelName).replace(/^presence-/, '');
    const statusUrl = @json(route('consultations.video.status', $consultation));
    const heartbeatUrl = @json(route('consultations.video.heartbeat', $consultation));
    const signalUrl = @json(route('consultations.video.signal', $consultation));
    const signalsUrl = @json(route('consultations.video.signals', $consultation));
    const returnUrl = @json($returnRoute);
    const iceServers = @json($iceServers);
    const isClient = @json(Auth::user()->role !== 'lawyer');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const localVideo = document.getElementById('localVideo');
    const remoteVideo = document.getElementById('remoteVideo');
    const remoteMutedBadge = document.getElementById('remoteMutedBadge');
    const callShell = document.querySelector('.call-shell');
    const videoStage = document.querySelector('.video-stage');
    const localTile = document.getElementById('localTile');
    const hideLocalTileBtn = document.getElementById('hideLocalTileBtn');
    const restoreLocalTileTab = document.getElementById('restoreLocalTileTab');
    const remotePlaceholder = document.getElementById('remotePlaceholder');
    const remotePlaceholderCopy = document.getElementById('remotePlaceholderCopy');
    const callStateTitle = document.getElementById('callStateTitle');
    const callStateCopy = document.getElementById('callStateCopy');
    const sessionSignalText = document.getElementById('sessionSignalText');
    const peerPresencePill = document.getElementById('peerPresencePill');
    const peerPresenceText = document.getElementById('peerPresenceText');
    const cameraWarningModal = document.getElementById('cameraWarningModal');
    const cameraWarningCopy = document.getElementById('cameraWarningCopy');
    const cameraWarningCloseBtn = document.getElementById('cameraWarningCloseBtn');
    const cameraWarningReconnectBtn = document.getElementById('cameraWarningReconnectBtn');
    const micControl = document.getElementById('micControl');
    const micPermissionBadge = document.getElementById('micPermissionBadge');
    const microphonePicker = document.getElementById('microphonePicker');
    const microphoneMenu = document.getElementById('microphoneMenu');
    const microphoneSelect = document.getElementById('microphoneSelect');
    const cameraControl = document.getElementById('cameraControl');
    const cameraPermissionBadge = document.getElementById('cameraPermissionBadge');
    const cameraPicker = document.getElementById('cameraPicker');
    const cameraMenu = document.getElementById('cameraMenu');
    const cameraSelect = document.getElementById('cameraSelect');
    const muteBtn = document.getElementById('muteBtn');
    const cameraBtn = document.getElementById('cameraBtn');
    const shareScreenBtn = document.getElementById('shareScreenBtn');
    const fullscreenBtn = document.getElementById('fullscreenBtn');
    const reconnectBtn = document.getElementById('reconnectBtn');
    const balanceSignalBox = document.getElementById('balanceSignalBox');
    const balanceSignalText = document.getElementById('balanceSignalText');

    let localStream = null;
    let cameraVideoTrack = null;
    let microphoneAudioTrack = null;
    let selectedMicrophoneId = '';
    let selectedCameraId = '';
    let screenStream = null;
    let remoteStream = null;
    let peerConnection = null;
    let controlDataChannel = null;
    let presenceChannel = null;
    let pendingIceCandidates = [];
    let peerOnline = false;
    let isMuted = false;
    let isCameraOff = false;
    let isScreenSharing = false;
    let isFullscreen = false;
    let isLocalPreviewHidden = false;
    let localTileDockSide = 'right';
    let localTileDrag = null;
    let balanceRedirecting = false;
    let isStarting = false;
    let negotiationTimer = null;
    let autoReconnectTimer = null;
    let iceWatchdogTimer = null;
    let heartbeatTimer = null;
    let signalPollTimer = null;
    let sessionEndStatusTimer = null;
    let isMakingOffer = false;
    let pendingControlMessages = [];
    let lastRemoteMuteSignalAt = 0;
    const processedSignalIds = new Set();
    const fastSignalIntervalMs = 650;
    const fastHeartbeatIntervalMs = 1200;
    const negotiationWatchdogMs = 8000;

    function setCallState(title, copy) {
        callStateTitle.textContent = title;
        callStateCopy.textContent = copy;
        if (sessionSignalText) {
            sessionSignalText.textContent = copy;
        }
    }

    function updatePeerPresence(online) {
        const becameOnline = online && !peerOnline;
        peerOnline = online;
        peerPresencePill.classList.toggle('offline', !online);
        peerPresenceText.textContent = online
            ? (peerName || 'Peer') + ' is on this consultation page'
            : (peerName || 'Peer') + ' not connected yet';

        if (!online) {
            lastRemoteMuteSignalAt = 0;
            updateRemoteMuteIndicator(false);
            remotePlaceholder.classList.remove('hidden');
            remotePlaceholderCopy.textContent = 'We will connect as soon as both participants are on this page.';
        } else if (becameOnline && !remoteStream) {
            remotePlaceholderCopy.textContent = 'Both participants are online. Starting the secure video connection now.';
        }

        if (becameOnline && !remoteStream) {
            sendControlMessage({ type: 'audio-muted', muted: isMuted });
            sendSignal({ type: 'audio-muted', muted: isMuted });
            if (currentUserRole === 'lawyer') {
                createOffer(true);
            } else {
                sendSignal({ type: 'peer-ready' });
            }
        }
    }

    function stopAutoReconnect() {
        if (autoReconnectTimer) {
            clearInterval(autoReconnectTimer);
            autoReconnectTimer = null;
        }
    }

    function stopIceWatchdog() {
        if (iceWatchdogTimer) {
            clearTimeout(iceWatchdogTimer);
            iceWatchdogTimer = null;
        }
    }

    function stopFallbackSignaling() {
        if (heartbeatTimer) {
            clearInterval(heartbeatTimer);
            heartbeatTimer = null;
        }
        if (signalPollTimer) {
            clearInterval(signalPollTimer);
            signalPollTimer = null;
        }
    }

    function startIceWatchdog() {
        stopIceWatchdog();

        iceWatchdogTimer = setTimeout(function() {
            iceWatchdogTimer = null;

            if (!peerConnection || remoteStream || !peerOnline) {
                return;
            }

            if (peerConnection.connectionState === 'connected' || peerConnection.iceConnectionState === 'connected' || peerConnection.iceConnectionState === 'completed') {
                return;
            }

            setCallState(
                'Retrying connection path',
                'The first media route is taking too long, so we are trying a fresh WebRTC route now.'
            );

            if (currentUserRole === 'lawyer') {
                createOffer(true, true);
            } else {
                sendSignal({ type: 'peer-ready' });
            }
        }, negotiationWatchdogMs);
    }

    function startAutoReconnect() {
        stopAutoReconnect();

        let attempts = 0;
        autoReconnectTimer = setInterval(function() {
            if (remoteStream || attempts >= 12) {
                stopAutoReconnect();
                return;
            }

            attempts += 1;
            sendHeartbeat();
            sendSignal({ type: 'peer-ready' });

            if (currentUserRole === 'lawyer' && peerOnline) {
                scheduleOfferFallback(700);
            }
        }, 1500);
    }

    function getAudioConstraints() {
        if (selectedMicrophoneId) {
            return {
                deviceId: { exact: selectedMicrophoneId },
                echoCancellation: true,
                noiseSuppression: true,
                autoGainControl: true,
            };
        }

        return {
            echoCancellation: true,
            noiseSuppression: true,
            autoGainControl: true,
        };
    }

    function getVideoConstraints() {
        const baseConstraints = {
            width: { ideal: 960, max: 1280 },
            height: { ideal: 540, max: 720 },
            frameRate: { ideal: 24, max: 30 },
        };

        if (selectedCameraId) {
            return {
                ...baseConstraints,
                deviceId: { exact: selectedCameraId },
            };
        }

        return {
            ...baseConstraints,
            facingMode: 'user',
        };
    }

    function setMicPermissionNeeded(isNeeded, message = 'Permission needed') {
        micControl.classList.toggle('permission-needed', isNeeded);
        micControl.title = isNeeded ? message : 'Choose microphone';
        micPermissionBadge.title = message;
    }

    function setCameraPermissionNeeded(isNeeded, message = 'Permission needed') {
        cameraControl.classList.toggle('permission-needed', isNeeded);
        cameraControl.title = isNeeded ? message : 'Choose camera';
        cameraPermissionBadge.title = message;
    }

    function closeDeviceMenus() {
        microphoneMenu.classList.remove('open');
        cameraMenu.classList.remove('open');
        microphonePicker.classList.remove('open');
        cameraPicker.classList.remove('open');
        microphonePicker.setAttribute('aria-expanded', 'false');
        cameraPicker.setAttribute('aria-expanded', 'false');
    }

    function toggleDeviceMenu(kind) {
        const isMicrophone = kind === 'microphone';
        const menu = isMicrophone ? microphoneMenu : cameraMenu;
        const picker = isMicrophone ? microphonePicker : cameraPicker;
        const select = isMicrophone ? microphoneSelect : cameraSelect;

        if (select.disabled) {
            return;
        }

        const shouldOpen = !menu.classList.contains('open');
        closeDeviceMenus();

        if (shouldOpen) {
            menu.classList.add('open');
            picker.classList.add('open');
            picker.setAttribute('aria-expanded', 'true');
        }
    }

    function renderDeviceMenu(kind, devices, activeDeviceId, defaultLabel) {
        const isMicrophone = kind === 'microphone';
        const menu = isMicrophone ? microphoneMenu : cameraMenu;
        const picker = isMicrophone ? microphonePicker : cameraPicker;
        const iconClass = isMicrophone ? 'fa-microphone-lines' : 'fa-video';
        const emptyLabel = isMicrophone ? 'No microphone found' : 'No camera found';

        menu.innerHTML = '';

        if (!devices.length) {
            const empty = document.createElement('div');
            empty.className = 'device-menu-empty';
            empty.textContent = emptyLabel;
            menu.appendChild(empty);
            picker.title = emptyLabel;
            return;
        }

        const options = [{ deviceId: '', label: defaultLabel }].concat(devices.map(function(device, index) {
            return {
                deviceId: device.deviceId,
                label: device.label || (isMicrophone ? 'Microphone ' : 'Camera ') + (index + 1),
            };
        }));

        options.forEach(function(device) {
            const isActive = (activeDeviceId || '') === device.deviceId;
            const option = document.createElement('button');
            option.type = 'button';
            option.className = 'device-menu-option' + (isActive ? ' active' : '');
            option.setAttribute('role', 'option');
            option.setAttribute('aria-selected', isActive ? 'true' : 'false');
            option.dataset.deviceId = device.deviceId;
            option.innerHTML = '<span class="device-option-icon"><i class="fas ' + iconClass + '"></i></span>' +
                '<span class="device-option-name"></span>' +
                '<span class="device-option-check"><i class="fas fa-check"></i></span>';
            option.querySelector('.device-option-name').textContent = device.label;
            option.addEventListener('click', function(event) {
                event.stopPropagation();
                closeDeviceMenus();
                if (isMicrophone) {
                    microphoneSelect.value = device.deviceId;
                    switchMicrophone(device.deviceId);
                } else {
                    cameraSelect.value = device.deviceId;
                    switchCamera(device.deviceId);
                }
            });
            menu.appendChild(option);
        });

        const selected = options.find(function(device) {
            return device.deviceId === (activeDeviceId || '');
        }) || options[0];
        picker.title = selected.label;
    }

    async function populateMicrophoneOptions() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.enumerateDevices) {
            microphoneSelect.disabled = true;
            setMicPermissionNeeded(true, 'Permission needed');
            renderDeviceMenu('microphone', [], '', 'Default microphone');
            return;
        }

        const devices = await navigator.mediaDevices.enumerateDevices();
        const microphones = devices.filter(function(device) {
            return device.kind === 'audioinput';
        });
        const activeDeviceId = selectedMicrophoneId || (microphoneAudioTrack && microphoneAudioTrack.getSettings ? microphoneAudioTrack.getSettings().deviceId : '');

        microphoneSelect.innerHTML = '';

        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = microphones.length ? 'Default microphone' : 'No microphone found';
        microphoneSelect.appendChild(defaultOption);

        microphones.forEach(function(device, index) {
            const option = document.createElement('option');
            option.value = device.deviceId;
            option.textContent = device.label || 'Microphone ' + (index + 1);
            microphoneSelect.appendChild(option);
        });

        microphoneSelect.disabled = microphones.length === 0;
        microphoneSelect.value = activeDeviceId && microphones.some(function(device) {
            return device.deviceId === activeDeviceId;
        }) ? activeDeviceId : '';
        const selectedOption = microphoneSelect.options[microphoneSelect.selectedIndex];
        const selectedLabel = selectedOption ? selectedOption.textContent : 'Choose microphone';
        microphonePicker.title = selectedLabel;
        renderDeviceMenu('microphone', microphones, microphoneSelect.value, 'Default microphone');
        if (microphones.length === 0) {
            setMicPermissionNeeded(true, 'Permission needed');
        }
    }

    async function populateCameraOptions() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.enumerateDevices) {
            cameraSelect.disabled = true;
            setCameraPermissionNeeded(true, 'Permission needed');
            renderDeviceMenu('camera', [], '', 'Default camera');
            return;
        }

        const devices = await navigator.mediaDevices.enumerateDevices();
        const cameras = devices.filter(function(device) {
            return device.kind === 'videoinput';
        });
        const activeDeviceId = selectedCameraId || (cameraVideoTrack && cameraVideoTrack.getSettings ? cameraVideoTrack.getSettings().deviceId : '');

        cameraSelect.innerHTML = '';

        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = cameras.length ? 'Default camera' : 'No camera found';
        cameraSelect.appendChild(defaultOption);

        cameras.forEach(function(device, index) {
            const option = document.createElement('option');
            option.value = device.deviceId;
            option.textContent = device.label || 'Camera ' + (index + 1);
            cameraSelect.appendChild(option);
        });

        cameraSelect.disabled = cameras.length === 0;
        cameraSelect.value = activeDeviceId && cameras.some(function(device) {
            return device.deviceId === activeDeviceId;
        }) ? activeDeviceId : '';
        const selectedOption = cameraSelect.options[cameraSelect.selectedIndex];
        const selectedLabel = selectedOption ? selectedOption.textContent : 'Choose camera';
        cameraPicker.title = selectedLabel;
        renderDeviceMenu('camera', cameras, cameraSelect.value, 'Default camera');
        if (cameras.length === 0) {
            setCameraPermissionNeeded(true, 'Permission needed');
        }
    }

    async function ensureLocalMedia() {
        if (localStream) {
            await populateMicrophoneOptions();
            await populateCameraOptions();
            return localStream;
        }

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            const error = new Error('Media devices are not available in this browser context.');
            error.name = 'MediaDevicesUnavailableError';
            throw error;
        }

        if (navigator.mediaDevices.enumerateDevices) {
            try {
                const devices = await navigator.mediaDevices.enumerateDevices();
                const hasCamera = devices.some(function(device) {
                    return device.kind === 'videoinput';
                });
                const hasMicrophone = devices.some(function(device) {
                    return device.kind === 'audioinput';
                });

                if (!hasCamera) {
                    const error = new Error('No camera device was found.');
                    error.name = 'NoCameraConnectedError';
                    throw error;
                }

                if (!hasMicrophone) {
                    const error = new Error('No microphone device was found.');
                    error.name = 'NoMicrophoneConnectedError';
                    throw error;
                }
            } catch (error) {
                if (error.name === 'NoCameraConnectedError' || error.name === 'NoMicrophoneConnectedError') {
                    throw error;
                }
            }
        }

        localStream = await navigator.mediaDevices.getUserMedia({
            audio: getAudioConstraints(),
            video: getVideoConstraints(),
        });
        cameraVideoTrack = localStream.getVideoTracks()[0] || null;
        microphoneAudioTrack = localStream.getAudioTracks()[0] || null;
        if (cameraVideoTrack) {
            cameraVideoTrack.enabled = !isCameraOff;
            selectedCameraId = cameraVideoTrack.getSettings ? (cameraVideoTrack.getSettings().deviceId || selectedCameraId) : selectedCameraId;
        }
        if (microphoneAudioTrack) {
            microphoneAudioTrack.enabled = !isMuted;
            selectedMicrophoneId = microphoneAudioTrack.getSettings ? (microphoneAudioTrack.getSettings().deviceId || selectedMicrophoneId) : selectedMicrophoneId;
        }
        await populateMicrophoneOptions();
        await populateCameraOptions();
        setMicPermissionNeeded(false, 'Microphone permission granted');
        setCameraPermissionNeeded(false, 'Camera permission granted');

        localVideo.srcObject = localStream;
        setCallState(
            'Local media ready',
            'Your camera and microphone are active. Waiting for the secure peer connection to finish.'
        );

        return localStream;
    }

    function getLocalMediaErrorDetails(error) {
        const errorName = error && error.name ? error.name : '';

        if (errorName === 'NoCameraConnectedError' || errorName === 'NotFoundError' || errorName === 'DevicesNotFoundError') {
            return {
                title: 'No camera connected',
                copy: 'We could not find a camera connected to this device. Connect a camera or enable your built-in camera, then press reconnect to try again.',
                placeholder: 'No camera was found on this device. Connect or enable a camera, then press reconnect to try again.',
            };
        }

        if (errorName === 'NoMicrophoneConnectedError') {
            return {
                title: 'No microphone connected',
                copy: 'We could not find a microphone connected to this device. Connect or enable a microphone, then press reconnect to try again.',
                placeholder: 'No microphone was found on this device. Connect or enable a microphone, then press reconnect to try again.',
            };
        }

        if (errorName === 'NotAllowedError' || errorName === 'PermissionDeniedError' || errorName === 'SecurityError') {
            return {
                title: 'Camera permission required',
                copy: 'Camera or microphone access was blocked. Allow both permissions in the browser, then press reconnect to try again.',
                placeholder: 'Camera or microphone access was blocked. Allow permissions and press reconnect to try again.',
            };
        }

        if (errorName === 'NotReadableError' || errorName === 'TrackStartError') {
            return {
                title: 'Camera is unavailable',
                copy: 'The camera was found, but another app or the system is blocking access. Close other camera apps, then press reconnect.',
                placeholder: 'The camera is connected but unavailable. Close other camera apps and press reconnect to try again.',
            };
        }

        if (errorName === 'OverconstrainedError' || errorName === 'ConstraintNotSatisfiedError') {
            return {
                title: 'Camera settings unavailable',
                copy: 'This camera does not support the requested video settings. Use another camera or reconnect after checking camera settings.',
                placeholder: 'The selected camera cannot start with the requested settings. Check camera settings and press reconnect.',
            };
        }

        if (errorName === 'MediaDevicesUnavailableError') {
            return {
                title: 'Camera access unavailable',
                copy: 'This browser cannot access camera devices on the current connection. Open the call using HTTPS or a supported browser.',
                placeholder: 'Camera access is unavailable in this browser context. Use HTTPS or a supported browser.',
            };
        }

        return {
            title: 'Permissions required',
            copy: 'We could not access your camera or microphone. Please check your device and browser permissions, then use reconnect.',
            placeholder: 'We could not access your camera or microphone. Check your device and permissions, then use reconnect.',
        };
    }

    function showLocalMediaError(error) {
        const details = getLocalMediaErrorDetails(error);
        remotePlaceholder.classList.remove('hidden');
        remotePlaceholderCopy.textContent = details.placeholder;
        setCallState(details.title, details.copy);

        if (details.title === 'No microphone connected') {
            setMicPermissionNeeded(true, 'Permission needed');
        } else if (details.title === 'No camera connected' || details.title === 'Camera is unavailable' || details.title === 'Camera settings unavailable') {
            setCameraPermissionNeeded(true, 'Permission needed');
        } else {
            setMicPermissionNeeded(true, 'Permission needed');
            setCameraPermissionNeeded(true, 'Permission needed');
        }

        if (details.title === 'No camera connected') {
            showCameraWarningModal(details.copy);
        }
    }

    function showCameraWarningModal(message) {
        cameraWarningCopy.textContent = message;
        cameraWarningModal.classList.add('visible');
        cameraWarningModal.setAttribute('aria-hidden', 'false');
        cameraWarningReconnectBtn.focus();
    }

    function hideCameraWarningModal() {
        cameraWarningModal.classList.remove('visible');
        cameraWarningModal.setAttribute('aria-hidden', 'true');
    }

    function returnToConsultations() {
        cleanupBeforeExit();
        window.location.href = returnUrl;
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

    function getVideoSender() {
        if (!peerConnection) {
            return null;
        }

        return peerConnection.getSenders().find(function(sender) {
            return sender.track && sender.track.kind === 'video';
        }) || null;
    }

    function getAudioSender() {
        if (!peerConnection) {
            return null;
        }

        return peerConnection.getSenders().find(function(sender) {
            return sender.track && sender.track.kind === 'audio';
        }) || null;
    }

    async function replaceOutgoingVideoTrack(track) {
        const sender = getVideoSender();

        if (sender) {
            await sender.replaceTrack(track);
            return true;
        }

        return false;
    }

    async function replaceOutgoingAudioTrack(track) {
        const sender = getAudioSender();

        if (sender) {
            await sender.replaceTrack(track);
            return true;
        }

        return false;
    }

    async function switchMicrophone(deviceId) {
        selectedMicrophoneId = deviceId || '';

        if (!localStream) {
            return;
        }

        try {
            const audioStream = await navigator.mediaDevices.getUserMedia({
                audio: getAudioConstraints(),
                video: false,
            });
            const nextAudioTrack = audioStream.getAudioTracks()[0];

            if (!nextAudioTrack) {
                const error = new Error('No microphone audio track was returned.');
                error.name = 'NoMicrophoneConnectedError';
                throw error;
            }

            nextAudioTrack.enabled = !isMuted;

            if (microphoneAudioTrack) {
                localStream.removeTrack(microphoneAudioTrack);
                microphoneAudioTrack.stop();
            }

            microphoneAudioTrack = nextAudioTrack;
            localStream.addTrack(nextAudioTrack);

            const replaced = await replaceOutgoingAudioTrack(nextAudioTrack);
            if (!replaced && peerConnection) {
                peerConnection.addTrack(nextAudioTrack, localStream);
            }

            selectedMicrophoneId = nextAudioTrack.getSettings ? (nextAudioTrack.getSettings().deviceId || selectedMicrophoneId) : selectedMicrophoneId;
            await populateMicrophoneOptions();
            setMicPermissionNeeded(false, 'Microphone permission granted');
            updateMuteButton();
            setCallState(
                'Microphone changed',
                'Your selected microphone is now being used for this consultation.'
            );
        } catch (error) {
            console.error('Microphone switch failed:', error);
            if (error && (error.name === 'NotFoundError' || error.name === 'DevicesNotFoundError')) {
                error.name = 'NoMicrophoneConnectedError';
            }
            await populateMicrophoneOptions();
            showLocalMediaError(error);
        }
    }

    async function switchCamera(deviceId) {
        selectedCameraId = deviceId || '';

        if (!localStream) {
            return;
        }

        try {
            const videoStream = await navigator.mediaDevices.getUserMedia({
                audio: false,
                video: getVideoConstraints(),
            });
            const nextVideoTrack = videoStream.getVideoTracks()[0];

            if (!nextVideoTrack) {
                const error = new Error('No camera video track was returned.');
                error.name = 'NoCameraConnectedError';
                throw error;
            }

            nextVideoTrack.enabled = !isCameraOff;

            if (cameraVideoTrack) {
                localStream.removeTrack(cameraVideoTrack);
                cameraVideoTrack.stop();
            }

            cameraVideoTrack = nextVideoTrack;
            localStream.addTrack(nextVideoTrack);

            if (!isScreenSharing) {
                const replaced = await replaceOutgoingVideoTrack(nextVideoTrack);
                if (!replaced && peerConnection) {
                    peerConnection.addTrack(nextVideoTrack, localStream);
                }
                localVideo.srcObject = localStream;
                localVideo.classList.remove('screen-share-video');
            }

            selectedCameraId = nextVideoTrack.getSettings ? (nextVideoTrack.getSettings().deviceId || selectedCameraId) : selectedCameraId;
            await populateCameraOptions();
            setCameraPermissionNeeded(false, 'Camera permission granted');
            updateCameraButton();
            setCallState(
                'Camera changed',
                'Your selected camera is now being used for this consultation.'
            );
        } catch (error) {
            console.error('Camera switch failed:', error);
            if (error && (error.name === 'NotFoundError' || error.name === 'DevicesNotFoundError')) {
                error.name = 'NoCameraConnectedError';
            }
            await populateCameraOptions();
            showLocalMediaError(error);
        }
    }

    function clearRemoteMedia() {
        remoteStream = null;
        remoteVideo.srcObject = null;
        remotePlaceholder.classList.remove('hidden');
    }

    function handleControlMessage(payload) {
        if (!payload || payload.fromUserId === currentUserId || payload.targetUserId !== currentUserId) {
            return;
        }

        if (payload.type === 'audio-muted') {
            handleRemoteMuteSignal(payload);
        }
    }

    function setupControlDataChannel(channel) {
        controlDataChannel = channel;

        controlDataChannel.onopen = function() {
            const messages = pendingControlMessages.splice(0);
            messages.forEach(function(payload) {
                sendControlMessage(payload, false);
            });
        };

        controlDataChannel.onmessage = function(event) {
            try {
                handleControlMessage(JSON.parse(event.data));
            } catch (error) {
                console.warn('Could not parse control data channel message:', error);
            }
        };

        controlDataChannel.onclose = function() {
            if (controlDataChannel === channel) {
                controlDataChannel = null;
            }
        };
    }

    function sendControlMessage(payload, queueIfClosed = true) {
        if (!peerId) {
            return false;
        }

        const controlPayload = makeSignalPayload(payload);

        if (controlDataChannel && controlDataChannel.readyState === 'open') {
            controlDataChannel.send(JSON.stringify(controlPayload));
            return true;
        }

        if (queueIfClosed) {
            pendingControlMessages = pendingControlMessages.concat(controlPayload).slice(-10);
        }

        return false;
    }

    function teardownPeerConnection() {
        if (controlDataChannel) {
            controlDataChannel.close();
            controlDataChannel = null;
        }
        pendingControlMessages = [];

        pendingIceCandidates = [];
        if (negotiationTimer) {
            clearTimeout(negotiationTimer);
            negotiationTimer = null;
        }

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

        peerConnection = new RTCPeerConnection({
            iceServers,
            iceCandidatePoolSize: 8,
            bundlePolicy: 'max-bundle',
            rtcpMuxPolicy: 'require',
        });

        peerConnection.ondatachannel = function(event) {
            if (event.channel && event.channel.label === 'call-control') {
                setupControlDataChannel(event.channel);
            }
        };

        if (currentUserRole === 'lawyer') {
            setupControlDataChannel(peerConnection.createDataChannel('call-control', {
                ordered: false,
                maxRetransmits: 0,
            }));
        }

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
            if (negotiationTimer) {
                clearTimeout(negotiationTimer);
                negotiationTimer = null;
            }
            stopIceWatchdog();
            stopAutoReconnect();
            setCallState(
                'Call connected',
                'Secure media is flowing between both consultation participants.'
            );
        };

        peerConnection.onconnectionstatechange = function() {
            const state = peerConnection.connectionState;

            if (state === 'connected') {
                remotePlaceholder.classList.add('hidden');
                stopIceWatchdog();
                setCallState(
                    'Call connected',
                    'You and ' + (peerName || 'the other participant') + ' are now in the consultation.'
                );
            } else if (state === 'connecting') {
                startIceWatchdog();
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
                if (currentUserRole === 'lawyer') {
                    createOffer(true, true);
                } else {
                    sendSignal({ type: 'peer-ready' });
                }
            } else if (peerConnection.iceConnectionState === 'connected' || peerConnection.iceConnectionState === 'completed') {
                stopIceWatchdog();
            }
        };

        attachLocalTracks();
        return peerConnection;
    }

    function makeSignalPayload(payload) {
        return {
            ...payload,
            signalId: payload.signalId || (currentUserId + '-' + Date.now() + '-' + Math.random().toString(36).slice(2)),
            consultationId,
            fromUserId: currentUserId,
            fromRole: currentUserRole,
            targetUserId: peerId,
            sentAt: Date.now(),
        };
    }

    function postSignal(payload) {
        return fetch(signalUrl, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            body: JSON.stringify(payload),
        }).catch(function(error) {
            console.warn('HTTP signaling failed:', error);
        });
    }

    function sendSignal(payload) {
        if (!peerId) {
            return;
        }

        const signalPayload = makeSignalPayload(payload);

        if (presenceChannel) {
            presenceChannel.whisper('signal', signalPayload);
        }

        postSignal(signalPayload);
    }

    function whisperSignal(payload) {
        sendSignal(payload);
    }

    function serializeSessionDescription(description) {
        return {
            type: description.type,
            sdp: description.sdp,
        };
    }

    function normalizeSessionDescription(description) {
        if (!description) {
            return null;
        }

        const sdp = typeof description.sdp === 'string'
            ? description.sdp.replace(/\r?\n/g, '\r\n').trim() + '\r\n'
            : '';

        return {
            type: description.type,
            sdp,
        };
    }

    function scheduleOfferFallback(delay = 1800) {
        if (currentUserRole !== 'lawyer') {
            return;
        }

        if (negotiationTimer) {
            clearTimeout(negotiationTimer);
        }

        negotiationTimer = setTimeout(function() {
            negotiationTimer = null;

            if (!peerOnline || remoteStream) {
                return;
            }

            createOffer(true);
        }, delay);
    }

    async function createOffer(force = false, restartIce = false) {
        if (currentUserRole !== 'lawyer' || !peerOnline) {
            return;
        }

        if (isMakingOffer) {
            return;
        }

        await ensureLocalMedia();
        const pc = createPeerConnection();

        if (pc.signalingState !== 'stable') {
            return;
        }

        isMakingOffer = true;

        try {
            const offer = await pc.createOffer({ iceRestart: restartIce });
            if (pc.signalingState !== 'stable') {
                return;
            }
            await pc.setLocalDescription(offer);
            startIceWatchdog();

            whisperSignal({
                type: 'offer',
                sdp: serializeSessionDescription(pc.localDescription || offer),
            });

            setCallState(
                'Offer sent',
                'Waiting for ' + (peerName || 'the other participant') + ' to answer the consultation call.'
            );
        } finally {
            isMakingOffer = false;
        }
    }

    async function handleSignal(payload) {
        if (!payload || payload.fromUserId === currentUserId || payload.targetUserId !== currentUserId) {
            return;
        }

        if (payload.signalId) {
            if (processedSignalIds.has(payload.signalId)) {
                return;
            }
            processedSignalIds.add(payload.signalId);
        }

        try {
            if (payload.type === 'peer-ready') {
                updatePeerPresence(true);
                sendControlMessage({ type: 'audio-muted', muted: isMuted });
                sendSignal({ type: 'audio-muted', muted: isMuted });
                if (currentUserRole === 'lawyer') {
                    await createOffer(true);
                }
                return;
            }

            if (payload.type === 'hangup') {
                lastRemoteMuteSignalAt = 0;
                updateRemoteMuteIndicator(false);
                teardownPeerConnection();
                setCallState(
                    'Peer left the session',
                    (peerName || 'The other participant') + ' left the consultation page.'
                );
                return;
            }

            if (payload.type === 'consultation-ended') {
                handleConsultationEnded(payload);
                return;
            }

            if (payload.type === 'screen-share-start') {
                remoteVideo.classList.add('screen-share-video');
                setCallState(
                    'Screen sharing',
                    (peerName || 'The other participant') + ' is sharing their screen.'
                );
                return;
            }

            if (payload.type === 'screen-share-stop') {
                remoteVideo.classList.remove('screen-share-video');
                setCallState(
                    'Camera restored',
                    (peerName || 'The other participant') + ' stopped sharing their screen.'
                );
                return;
            }

            if (payload.type === 'audio-muted') {
                handleRemoteMuteSignal(payload);
                return;
            }

            await ensureLocalMedia();
            const pc = createPeerConnection();

            if (payload.type === 'offer') {
                if (pc.signalingState !== 'stable') {
                    if (currentUserRole === 'lawyer') {
                        console.warn('Ignored colliding WebRTC offer while making or holding a local offer.');
                        return;
                    }
                    teardownPeerConnection();
                }

                const refreshedPc = createPeerConnection();
                const remoteOffer = normalizeSessionDescription(payload.sdp);
                if (!remoteOffer || remoteOffer.type !== 'offer' || !remoteOffer.sdp) {
                    throw new Error('Received an invalid WebRTC offer.');
                }
                await refreshedPc.setRemoteDescription(new RTCSessionDescription(remoteOffer));
                await flushPendingIceCandidates(refreshedPc);
                const answer = await refreshedPc.createAnswer();
                if (refreshedPc.signalingState !== 'have-remote-offer') {
                    return;
                }
                await refreshedPc.setLocalDescription(answer);
                startIceWatchdog();

                whisperSignal({
                    type: 'answer',
                    sdp: serializeSessionDescription(refreshedPc.localDescription || answer),
                });

                setCallState(
                    'Answer sent',
                    'The call answer was sent. Finishing the direct media connection now.'
                );
            } else if (payload.type === 'answer') {
                if (pc.signalingState !== 'have-local-offer') {
                    console.warn('Ignored stale WebRTC answer while in state:', pc.signalingState);
                    return;
                }

                const remoteAnswer = normalizeSessionDescription(payload.sdp);
                if (!remoteAnswer || remoteAnswer.type !== 'answer' || !remoteAnswer.sdp) {
                    throw new Error('Received an invalid WebRTC answer.');
                }
                await pc.setRemoteDescription(new RTCSessionDescription(remoteAnswer));
                await flushPendingIceCandidates(pc);
                startIceWatchdog();
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

        presenceChannel = window.Echo.join(callPresenceChannelName)
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

    function sendHeartbeat() {
        fetch(heartbeatUrl, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            body: JSON.stringify({ at: Date.now() }),
        })
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('Heartbeat failed.');
                }
                return response.json();
            })
            .then(function(data) {
                updatePeerPresence(Boolean(data.peer_online));
            })
            .catch(function(error) {
                console.warn('Video heartbeat failed:', error);
            });
    }

    function pollSignals() {
        fetch(signalsUrl, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        })
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('Signal poll failed.');
                }
                return response.json();
            })
            .then(function(data) {
                (data.signals || []).forEach(function(signal) {
                    handleSignal(signal);
                });
            })
            .catch(function(error) {
                console.warn('Video signal polling failed:', error);
            });
    }

    function startFallbackSignaling() {
        sendHeartbeat();
        pollSignals();
        if (!heartbeatTimer) {
            heartbeatTimer = setInterval(sendHeartbeat, fastHeartbeatIntervalMs);
        }
        if (!signalPollTimer) {
            signalPollTimer = setInterval(pollSignals, fastSignalIntervalMs);
        }
    }

    function stopSessionEndWatcher() {
        if (sessionEndStatusTimer) {
            clearInterval(sessionEndStatusTimer);
            sessionEndStatusTimer = null;
        }
    }

    function updateMuteButton() {
        muteBtn.classList.toggle('off', isMuted);
        muteBtn.setAttribute('aria-label', isMuted ? 'Unmute microphone' : 'Mute microphone');
        muteBtn.title = isMuted ? 'Unmute microphone' : 'Mute microphone';
        muteBtn.innerHTML = isMuted
            ? '<i class="fas fa-microphone-slash"></i>'
            : '<i class="fas fa-microphone"></i>';
    }

    function updateRemoteMuteIndicator(isPeerMuted) {
        remoteMutedBadge.classList.toggle('visible', Boolean(isPeerMuted));
    }

    function handleRemoteMuteSignal(payload) {
        const signalTime = Number(payload.sentAt || 0);

        if (signalTime && signalTime < lastRemoteMuteSignalAt) {
            return;
        }

        lastRemoteMuteSignalAt = signalTime || Date.now();
        updateRemoteMuteIndicator(Boolean(payload.muted));
    }

    function updateCameraButton() {
        cameraBtn.classList.toggle('off', isCameraOff);
        cameraBtn.setAttribute('aria-label', isCameraOff ? 'Turn camera on' : 'Turn camera off');
        cameraBtn.title = isCameraOff ? 'Turn camera on' : 'Turn camera off';
        cameraBtn.innerHTML = isCameraOff
            ? '<i class="fas fa-video-slash"></i>'
            : '<i class="fas fa-video"></i>';
    }

    function updateShareScreenButton() {
        shareScreenBtn.classList.toggle('off', isScreenSharing);
        shareScreenBtn.innerHTML = isScreenSharing
            ? '<i class="fas fa-display"></i> Stop Sharing'
            : '<i class="fas fa-display"></i> Share Screen';
    }

    function updateFullscreenButton() {
        fullscreenBtn.innerHTML = isFullscreen
            ? '<i class="fas fa-compress"></i> Exit Full Screen'
            : '<i class="fas fa-expand"></i> Full Screen';
    }

    function clampLocalTile(left, top) {
        const stageRect = videoStage.getBoundingClientRect();
        const tileRect = localTile.getBoundingClientRect();
        const margin = 12;
        const maxLeft = Math.max(margin, stageRect.width - tileRect.width - margin);
        const maxTop = Math.max(margin, stageRect.height - tileRect.height - margin);

        return {
            left: Math.min(Math.max(left, margin), maxLeft),
            top: Math.min(Math.max(top, margin), maxTop),
        };
    }

    function getLocalTileDockLeft(left) {
        const stageRect = videoStage.getBoundingClientRect();
        const tileRect = localTile.getBoundingClientRect();
        const margin = 12;
        const maxLeft = Math.max(margin, stageRect.width - tileRect.width - margin);
        const tileCenter = left + (tileRect.width / 2);

        localTileDockSide = tileCenter < (stageRect.width / 2) ? 'left' : 'right';

        return localTileDockSide === 'left' ? margin : maxLeft;
    }

    function setLocalTilePosition(left, top, dockToSide = false) {
        const position = clampLocalTile(left, top);
        const finalLeft = dockToSide ? getLocalTileDockLeft(position.left) : position.left;
        localTile.style.left = finalLeft + 'px';
        localTile.style.top = position.top + 'px';
        localTile.style.right = 'auto';
        localTile.style.bottom = 'auto';
    }

    function dockLocalTileToSide() {
        if (isLocalPreviewHidden) {
            return;
        }

        const stageRect = videoStage.getBoundingClientRect();
        const tileRect = localTile.getBoundingClientRect();
        setLocalTilePosition(
            tileRect.left - stageRect.left,
            tileRect.top - stageRect.top,
            true
        );
    }

    function keepLocalTileInBounds() {
        if (isLocalPreviewHidden) {
            keepRestoreTabInBounds();
            return;
        }

        const stageRect = videoStage.getBoundingClientRect();
        const tileRect = localTile.getBoundingClientRect();
        setLocalTilePosition(tileRect.left - stageRect.left, tileRect.top - stageRect.top, true);
    }

    function keepRestoreTabInBounds() {
        if (!isLocalPreviewHidden) {
            return;
        }

        const stageRect = videoStage.getBoundingClientRect();
        const tabRect = restoreLocalTileTab.getBoundingClientRect();
        const currentTop = parseFloat(restoreLocalTileTab.style.top || '0');
        restoreLocalTileTab.style.top = Math.min(
            Math.max(currentTop, 12),
            Math.max(12, stageRect.height - tabRect.height - 12)
        ) + 'px';
    }

    function hideLocalPreview() {
        const stageRect = videoStage.getBoundingClientRect();
        const tileRect = localTile.getBoundingClientRect();
        const tileLeft = tileRect.left - stageRect.left;
        const tileTop = tileRect.top - stageRect.top;
        getLocalTileDockLeft(tileLeft);

        isLocalPreviewHidden = true;
        localTile.classList.add('hidden-tile');
        restoreLocalTileTab.classList.remove('left', 'right');
        restoreLocalTileTab.classList.add(localTileDockSide, 'visible');
        restoreLocalTileTab.style.top = Math.min(
            Math.max(tileTop + (tileRect.height / 2) - 37, 12),
            Math.max(12, stageRect.height - 86)
        ) + 'px';
    }

    function showLocalPreview() {
        isLocalPreviewHidden = false;
        localTile.classList.remove('hidden-tile');
        restoreLocalTileTab.classList.remove('visible');
        requestAnimationFrame(keepLocalTileInBounds);
    }

    function startLocalTileDrag(event) {
        if (event.target.closest('button')) {
            return;
        }

        event.preventDefault();
        const stageRect = videoStage.getBoundingClientRect();
        const tileRect = localTile.getBoundingClientRect();

        localTileDrag = {
            pointerId: event.pointerId,
            offsetX: event.clientX - tileRect.left,
            offsetY: event.clientY - tileRect.top,
            stageLeft: stageRect.left,
            stageTop: stageRect.top,
        };

        localTile.classList.add('dragging');
        localTile.setPointerCapture(event.pointerId);
    }

    function moveLocalTile(event) {
        if (!localTileDrag || event.pointerId !== localTileDrag.pointerId) {
            return;
        }

        event.preventDefault();
        setLocalTilePosition(
            event.clientX - localTileDrag.stageLeft - localTileDrag.offsetX,
            event.clientY - localTileDrag.stageTop - localTileDrag.offsetY
        );
    }

    function stopLocalTileDrag(event) {
        if (!localTileDrag || event.pointerId !== localTileDrag.pointerId) {
            return;
        }

        localTile.classList.remove('dragging');
        localTile.releasePointerCapture(event.pointerId);
        localTileDrag = null;
        dockLocalTileToSide();
    }

    async function stopScreenShare() {
        if (!isScreenSharing) {
            return;
        }

        if (screenStream) {
            screenStream.getTracks().forEach(function(track) {
                track.onended = null;
                track.stop();
            });
            screenStream = null;
        }

        isScreenSharing = false;

        if (cameraVideoTrack) {
            cameraVideoTrack.enabled = !isCameraOff;
            const replaced = await replaceOutgoingVideoTrack(cameraVideoTrack);
            if (!replaced && peerConnection) {
                peerConnection.addTrack(cameraVideoTrack, localStream);
            }
            localVideo.srcObject = localStream;
            localVideo.classList.remove('screen-share-video');
        }

        sendSignal({ type: 'screen-share-stop' });
        updateShareScreenButton();
        setCallState(
            'Camera restored',
            'Screen sharing has stopped. Your camera is now being sent to the consultation.'
        );
    }

    async function startScreenShare() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getDisplayMedia) {
            setCallState(
                'Screen sharing unavailable',
                'This browser does not support screen sharing on the current connection.'
            );
            return;
        }

        await ensureLocalMedia();
        createPeerConnection();

        try {
            screenStream = await navigator.mediaDevices.getDisplayMedia({
                video: {
                    displaySurface: 'monitor',
                    frameRate: { ideal: 15, max: 30 },
                },
                audio: false,
                monitorTypeSurfaces: 'include',
                preferCurrentTab: false,
                selfBrowserSurface: 'exclude',
            });

            const screenTrack = screenStream.getVideoTracks()[0];
            if (!screenTrack) {
                throw new Error('No screen video track was selected.');
            }

            screenTrack.onended = function() {
                stopScreenShare();
            };

            const replaced = await replaceOutgoingVideoTrack(screenTrack);
            if (!replaced && peerConnection) {
                peerConnection.addTrack(screenTrack, screenStream);
            }
            localVideo.srcObject = screenStream;
            localVideo.classList.add('screen-share-video');
            isScreenSharing = true;
            sendSignal({ type: 'screen-share-start' });
            updateShareScreenButton();
            setCallState(
                'Screen sharing',
                'Your selected screen is now being shared with ' + (peerName || 'the other participant') + '.'
            );
        } catch (error) {
            if (screenStream) {
                screenStream.getTracks().forEach(function(track) {
                    track.onended = null;
                    track.stop();
                });
            }
            screenStream = null;
            localVideo.classList.remove('screen-share-video');
            if (error && error.name !== 'NotAllowedError') {
                console.error('Screen sharing failed:', error);
                setCallState(
                    'Screen sharing failed',
                    'We could not start screen sharing. Please try again and choose a screen, window, or tab.'
                );
            }
            isScreenSharing = false;
            updateShareScreenButton();
        }
    }

    async function toggleScreenShare() {
        if (isScreenSharing) {
            await stopScreenShare();
        } else {
            await startScreenShare();
        }
    }

    async function toggleFullscreen() {
        try {
            if (!document.fullscreenElement) {
                await callShell.requestFullscreen();
            } else {
                await document.exitFullscreen();
            }
        } catch (error) {
            console.error('Fullscreen toggle failed:', error);
            setCallState(
                'Full screen unavailable',
                'The browser could not switch the call into full screen mode.'
            );
        }
    }

    async function startConsultationCall() {
        if (isStarting) {
            return;
        }

        isStarting = true;

        try {
            await ensureLocalMedia();
            createPeerConnection();
            setupPresenceChannel();
            startFallbackSignaling();
            startAutoReconnect();

            if (isClient) {
                setInterval(checkSessionStatus, 1000);
                setTimeout(checkSessionStatus, 500);
            }
        } catch (error) {
            console.error('Failed to start call page:', error);
            showLocalMediaError(error);
        } finally {
            isStarting = false;
        }
    }

    async function reconnectCall() {
        teardownPeerConnection();
        clearRemoteMedia();

        try {
            await ensureLocalMedia();

            if (presenceChannel && window.Echo) {
                window.Echo.leave(callPresenceChannelName);
                presenceChannel = null;
            }

            setupPresenceChannel();

            sendHeartbeat();

            if (currentUserRole === 'lawyer' && peerOnline) {
                await createOffer(true);
            } else {
                sendSignal({ type: 'peer-ready' });
                setCallState(
                    'Reconnect requested',
                    'Waiting for ' + (peerName || 'the other participant') + ' to renegotiate the consultation call.'
                );
            }
        } catch (error) {
            console.error('Reconnect failed:', error);
            showLocalMediaError(error);
        }
    }

    function cleanupBeforeExit() {
        whisperSignal({ type: 'hangup' });

        if (presenceChannel && window.Echo) {
            window.Echo.leave(callPresenceChannelName);
            presenceChannel = null;
        }

        teardownPeerConnection();
        stopIceWatchdog();
        stopAutoReconnect();
        stopFallbackSignaling();
        stopSessionEndWatcher();

        if (screenStream) {
            screenStream.getTracks().forEach(function(track) {
                track.onended = null;
                track.stop();
            });
            screenStream = null;
        }

        if (localStream) {
            localStream.getTracks().forEach(function(track) {
                track.stop();
            });
            localStream = null;
            cameraVideoTrack = null;
            microphoneAudioTrack = null;
        }
    }

    function redirectToBalanceCheckout(url) {
        if (balanceRedirecting) {
            return;
        }

        balanceRedirecting = true;
        stopSessionEndWatcher();

        if (balanceSignalBox) {
            balanceSignalBox.classList.remove('hidden');
        }
        if (balanceSignalText) {
            balanceSignalText.textContent = 'The lawyer ended the consultation. Redirecting to the remaining balance checkout now.';
        }

        cleanupBeforeExit();
        setTimeout(function() {
            window.location.href = url;
        }, 250);
    }

    function handleConsultationEnded(payload) {
        if (!isClient) {
            return;
        }

        setCallState(
            'Consultation ended',
            'The lawyer ended this consultation. Preparing the remaining balance payment.'
        );

        if (payload.balance_checkout_url) {
            redirectToBalanceCheckout(payload.balance_checkout_url);
            return;
        }

        checkSessionStatus();
        if (!sessionEndStatusTimer) {
            sessionEndStatusTimer = setInterval(checkSessionStatus, 700);
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
                if (data.status === 'completed') {
                    if (data.balance_checkout_url) {
                        redirectToBalanceCheckout(data.balance_checkout_url);
                        return;
                    }

                    balanceRedirecting = true;
                    stopSessionEndWatcher();
                    cleanupBeforeExit();
                    window.location.href = returnUrl;
                }
            })
            .catch(function() {
                // Keep polling quietly.
            });
    }

    cameraWarningCloseBtn.addEventListener('click', returnToConsultations);

    cameraWarningReconnectBtn.addEventListener('click', function() {
        hideCameraWarningModal();
        reconnectCall();
    });

    cameraWarningModal.addEventListener('click', function(event) {
        if (event.target === cameraWarningModal) {
            hideCameraWarningModal();
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && cameraWarningModal.classList.contains('visible')) {
            hideCameraWarningModal();
        }

        if (event.key === 'Escape') {
            closeDeviceMenus();
        }
    });

    muteBtn.addEventListener('click', function() {
        if (!localStream) {
            return;
        }

        isMuted = !isMuted;
        if (microphoneAudioTrack) {
            microphoneAudioTrack.enabled = !isMuted;
        }
        updateMuteButton();
        sendControlMessage({ type: 'audio-muted', muted: isMuted });
        sendSignal({ type: 'audio-muted', muted: isMuted });
    });

    microphonePicker.addEventListener('click', function(event) {
        event.stopPropagation();
        toggleDeviceMenu('microphone');
    });

    microphonePicker.addEventListener('keydown', function(event) {
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            toggleDeviceMenu('microphone');
        }
    });

    cameraPicker.addEventListener('click', function(event) {
        event.stopPropagation();
        toggleDeviceMenu('camera');
    });

    cameraPicker.addEventListener('keydown', function(event) {
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            toggleDeviceMenu('camera');
        }
    });

    document.addEventListener('click', function(event) {
        if (!event.target.closest('.mic-control') && !event.target.closest('.camera-control')) {
            closeDeviceMenus();
        }
    });

    cameraBtn.addEventListener('click', function() {
        if (!localStream) {
            showCameraWarningModal('We cannot toggle the camera because no local camera is active. Connect or enable a camera, then use reconnect.');
            setCallState(
                'No camera connected',
                'We cannot toggle the camera because no local camera is active. Connect or enable a camera, then use reconnect.'
            );
            return;
        }

        isCameraOff = !isCameraOff;
        if (cameraVideoTrack) {
            cameraVideoTrack.enabled = !isCameraOff;
        }
        updateCameraButton();
    });

    shareScreenBtn.addEventListener('click', toggleScreenShare);
    fullscreenBtn.addEventListener('click', toggleFullscreen);
    reconnectBtn.addEventListener('click', reconnectCall);
    hideLocalTileBtn.addEventListener('click', hideLocalPreview);
    restoreLocalTileTab.addEventListener('click', showLocalPreview);
    localTile.addEventListener('pointerdown', startLocalTileDrag);
    localTile.addEventListener('pointermove', moveLocalTile);
    localTile.addEventListener('pointerup', stopLocalTileDrag);
    localTile.addEventListener('pointercancel', stopLocalTileDrag);

    document.addEventListener('fullscreenchange', function() {
        isFullscreen = Boolean(document.fullscreenElement);
        updateFullscreenButton();
        setTimeout(keepLocalTileInBounds, 50);
    });

    window.addEventListener('resize', function() {
        setTimeout(keepLocalTileInBounds, 50);
    });

    if (navigator.mediaDevices && navigator.mediaDevices.addEventListener) {
        navigator.mediaDevices.addEventListener('devicechange', function() {
            populateMicrophoneOptions().catch(function(error) {
                console.warn('Could not refresh microphone list:', error);
            });
            populateCameraOptions().catch(function(error) {
                console.warn('Could not refresh camera list:', error);
            });
        });
    }

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
            event.preventDefault();

            var endButton = document.getElementById('endSessionBtn');
            if (endButton) {
                endButton.disabled = true;
                endButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Ending';
            }

            const endingSignal = makeSignalPayload({ type: 'consultation-ended' });
            if (presenceChannel) {
                presenceChannel.whisper('signal', endingSignal);
            }
            postSignal(endingSignal);
            cleanupBeforeExit();

            fetch(endSessionForm.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                },
                credentials: 'same-origin',
            })
                .then(function(response) {
                    if (!response.ok) {
                        throw new Error('Could not end consultation.');
                    }
                    return response.json();
                })
                .then(function(data) {
                    window.location.href = data.redirect_url || returnUrl;
                })
                .catch(function(error) {
                    console.error('End session failed:', error);
                    endSessionForm.submit();
                });
        });
    }

    window.addEventListener('beforeunload', cleanupBeforeExit);
    updateMuteButton();
    updateCameraButton();
    updateShareScreenButton();
    updateFullscreenButton();
    populateMicrophoneOptions().catch(function(error) {
        console.warn('Could not load microphone list:', error);
    });
    populateCameraOptions().catch(function(error) {
        console.warn('Could not load camera list:', error);
    });

    window.addEventListener('load', function() {
        setTimeout(startConsultationCall, 350);
    });
</script>
</body>
</html>
