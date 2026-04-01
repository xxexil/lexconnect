<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LexConnect – Smart Legal Services Platform</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --navy: #1e2d4d;
            --navy-dark: #162240;
            --navy-light: #253b63;
            --gold: #b5860d;
            --gold-light: #d4a017;
            --bg: #f4f6f9;
        }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; color: #1e293b; overflow-x: hidden; }
        a { text-decoration: none; color: inherit; }

        /* ── NAVBAR ── */
        .nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 999;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 60px; height: 70px;
            background: rgba(255,255,255,.95); backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0,0,0,.08);
            transition: box-shadow .3s;
        }
        .nav.scrolled { box-shadow: 0 4px 24px rgba(0,0,0,.1); }
        .nav-logo { display: flex; align-items: center; gap: 10px; }
        .nav-logo-icon {
            width: 36px; height: 36px; background: var(--navy); border-radius: 9px;
            display: flex; align-items: center; justify-content: center; color: #fff; font-size: .95rem;
        }
        .nav-logo-text { font-size: 1.25rem; font-weight: 800; color: var(--navy); letter-spacing: -.3px; }
        .nav-logo-text span { color: var(--gold); }
        .nav-links { display: flex; align-items: center; gap: 32px; }
        .nav-links a { font-size: .9rem; font-weight: 500; color: #475569; transition: color .2s; }
        .nav-links a:hover { color: var(--navy); }
        .nav-actions { display: flex; align-items: center; gap: 12px; }
        .btn-outline {
            padding: 8px 20px; border: 2px solid var(--navy); border-radius: 8px;
            font-size: .88rem; font-weight: 600; color: var(--navy); transition: all .2s;
        }
        .btn-outline:hover { background: var(--navy); color: #fff; }
        .btn-primary {
            padding: 9px 22px; background: var(--navy); border-radius: 8px;
            font-size: .88rem; font-weight: 600; color: #fff; transition: background .2s;
        }
        .btn-primary:hover { background: var(--navy-dark); }
        .btn-gold {
            padding: 9px 22px; background: var(--gold); border-radius: 8px;
            font-size: .88rem; font-weight: 600; color: #fff; transition: background .2s;
        }
        .btn-gold:hover { background: #9a7010; }

        /* ── HERO ── */
        .hero {
            min-height: 100vh; background: linear-gradient(135deg, var(--navy) 0%, #1a3d5c 60%, #0d2137 100%);
            display: flex; align-items: center; justify-content: center;
            position: relative; overflow: hidden; padding-top: 70px;
        }
        .hero-bg-shapes { position: absolute; inset: 0; pointer-events: none; overflow: hidden; }
        .hero-circle {
            position: absolute; border-radius: 50%;
            background: rgba(181,134,13,.08); border: 1px solid rgba(181,134,13,.12);
        }
        .hero-circle-1 { width: 600px; height: 600px; top: -200px; right: -200px; }
        .hero-circle-2 { width: 400px; height: 400px; bottom: -100px; left: -100px; }
        .hero-circle-3 { width: 200px; height: 200px; top: 30%; left: 20%; }

        .hero-content { position: relative; z-index: 2; text-align: center; max-width: 800px; padding: 40px 24px; }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(181,134,13,.2); border: 1px solid rgba(181,134,13,.4);
            color: #f0c040; font-size: .8rem; font-weight: 600; padding: 6px 16px;
            border-radius: 100px; margin-bottom: 28px; letter-spacing: .5px;
        }
        .hero-title {
            font-size: clamp(2.4rem, 6vw, 4rem); font-weight: 900; color: #fff;
            line-height: 1.1; letter-spacing: -.5px; margin-bottom: 24px;
        }
        .hero-title span { color: var(--gold-light); }
        .hero-subtitle {
            font-size: 1.15rem; color: rgba(255,255,255,.7); line-height: 1.7;
            max-width: 620px; margin: 0 auto 40px;
        }
        .hero-actions { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; margin-bottom: 56px; }
        .hero-btn-primary {
            padding: 14px 32px; background: var(--gold); border-radius: 10px;
            font-size: 1rem; font-weight: 700; color: #fff;
            transition: all .2s; box-shadow: 0 4px 20px rgba(181,134,13,.4);
        }
        .hero-btn-primary:hover { background: #9a7010; transform: translateY(-1px); box-shadow: 0 6px 28px rgba(181,134,13,.5); }
        .hero-btn-secondary {
            padding: 14px 32px; background: rgba(255,255,255,.1); border: 1.5px solid rgba(255,255,255,.3);
            border-radius: 10px; font-size: 1rem; font-weight: 600; color: #fff; transition: all .2s;
        }
        .hero-btn-secondary:hover { background: rgba(255,255,255,.2); }

        .hero-stats {
            display: flex; justify-content: center; gap: 48px; flex-wrap: wrap;
            border-top: 1px solid rgba(255,255,255,.1); padding-top: 40px;
        }
        .hero-stat-num { font-size: 2rem; font-weight: 900; color: #fff; }
        .hero-stat-num span { color: var(--gold-light); }
        .hero-stat-label { font-size: .82rem; color: rgba(255,255,255,.55); margin-top: 2px; letter-spacing: .5px; text-transform: uppercase; }

        /* ── TRUSTED BY ── */
        .trusted-bar {
            background: #fff; padding: 20px 60px;
            display: flex; align-items: center; justify-content: center; gap: 48px;
            border-bottom: 1px solid #e2e8f0; flex-wrap: wrap;
        }
        .trusted-label { font-size: .8rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; }
        .trusted-logo { font-size: .88rem; font-weight: 700; color: #94a3b8; letter-spacing: -.2px; }

        /* ── SECTIONS COMMON ── */
        .section { padding: 96px 60px; }
        .section-alt { background: #f8fafc; }
        .section-center { text-align: center; }
        .section-tag {
            display: inline-block; background: rgba(30,45,77,.08); color: var(--navy);
            font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
            padding: 5px 14px; border-radius: 100px; margin-bottom: 16px;
        }
        .section-title { font-size: clamp(1.8rem, 4vw, 2.6rem); font-weight: 800; color: var(--navy); line-height: 1.2; margin-bottom: 16px; }
        .section-sub { font-size: 1.05rem; color: #64748b; max-width: 580px; margin: 0 auto 56px; line-height: 1.7; }

        /* ── HOW IT WORKS ── */
        .steps-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 32px; max-width: 1100px; margin: 0 auto; }
        .step-card {
            background: #fff; border-radius: 16px; padding: 36px 28px;
            box-shadow: 0 2px 16px rgba(0,0,0,.06); position: relative; overflow: hidden;
            border: 1px solid #e8edf5; transition: transform .25s, box-shadow .25s;
        }
        .step-card:hover { transform: translateY(-4px); box-shadow: 0 8px 32px rgba(0,0,0,.1); }
        .step-number {
            position: absolute; top: 20px; right: 24px; font-size: 4rem; font-weight: 900;
            color: rgba(30,45,77,.05); line-height: 1;
        }
        .step-icon {
            width: 52px; height: 52px; border-radius: 14px; background: rgba(30,45,77,.08);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem; color: var(--navy); margin-bottom: 20px;
        }
        .step-icon.gold { background: rgba(181,134,13,.1); color: var(--gold); }
        .step-icon.green { background: rgba(22,163,74,.1); color: #16a34a; }
        .step-card h3 { font-size: 1.05rem; font-weight: 700; color: var(--navy); margin-bottom: 10px; }
        .step-card p { font-size: .88rem; color: #64748b; line-height: 1.65; }

        /* ── FEATURES ── */
        .features-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; max-width: 1100px; margin: 0 auto; }
        .feature-card {
            background: #fff; border-radius: 16px; padding: 32px;
            border: 1px solid #e8edf5; display: flex; gap: 20px;
            transition: transform .25s, box-shadow .25s;
        }
        .feature-card:hover { transform: translateY(-3px); box-shadow: 0 6px 28px rgba(0,0,0,.08); }
        .feature-icon {
            width: 48px; height: 48px; border-radius: 12px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center; font-size: 1.2rem;
        }
        .fi-navy  { background: rgba(30,45,77,.1);   color: var(--navy); }
        .fi-gold  { background: rgba(181,134,13,.1); color: var(--gold); }
        .fi-green { background: rgba(22,163,74,.1);  color: #16a34a; }
        .fi-blue  { background: rgba(59,130,246,.1); color: #3b82f6; }
        .fi-red   { background: rgba(220,38,38,.1);  color: #dc2626; }
        .fi-purple{ background: rgba(124,58,237,.1); color: #7c3aed; }
        .feature-card h3 { font-size: .98rem; font-weight: 700; color: var(--navy); margin-bottom: 7px; }
        .feature-card p { font-size: .85rem; color: #64748b; line-height: 1.65; }

        /* ── FOR WHO CARDS ── */
        .who-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 28px; max-width: 1100px; margin: 0 auto; }
        .who-card {
            border-radius: 20px; padding: 40px 32px; text-align: center; position: relative; overflow: hidden;
            transition: transform .25s;
        }
        .who-card:hover { transform: translateY(-5px); }
        .who-card-client { background: linear-gradient(135deg, #1e2d4d 0%, #253b63 100%); color: #fff; }
        .who-card-lawyer { background: linear-gradient(135deg, #1a3d2b 0%, #2d6642 100%); color: #fff; }
        .who-card-firm   { background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%); color: #fff; }
        .who-card-bg {
            position: absolute; bottom: -30px; right: -30px; font-size: 8rem;
            opacity: .06; pointer-events: none;
        }
        .who-icon {
            width: 64px; height: 64px; border-radius: 18px; background: rgba(255,255,255,.15);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.6rem; margin: 0 auto 20px;
        }
        .who-card h3 { font-size: 1.25rem; font-weight: 800; margin-bottom: 12px; }
        .who-card p { font-size: .88rem; opacity: .8; line-height: 1.7; margin-bottom: 24px; }
        .who-list { list-style: none; text-align: left; margin-bottom: 28px; }
        .who-list li { font-size: .85rem; opacity: .85; padding: 5px 0; display: flex; align-items: center; gap: 9px; }
        .who-list li i { font-size: .7rem; opacity: .7; }
        .who-btn {
            display: inline-block; padding: 11px 24px; border-radius: 9px;
            font-size: .88rem; font-weight: 700; transition: all .2s;
        }
        .who-btn-light { background: rgba(255,255,255,.2); color: #fff; border: 1.5px solid rgba(255,255,255,.4); }
        .who-btn-light:hover { background: rgba(255,255,255,.35); }

        /* ── TESTIMONIALS ── */
        .testimonials-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px; max-width: 1100px; margin: 0 auto; }
        .testi-card {
            background: #fff; border-radius: 16px; padding: 32px;
            border: 1px solid #e8edf5; transition: transform .25s, box-shadow .25s;
        }
        .testi-card:hover { transform: translateY(-4px); box-shadow: 0 8px 28px rgba(0,0,0,.09); }
        .testi-stars { color: #f59e0b; font-size: .85rem; margin-bottom: 16px; letter-spacing: 2px; }
        .testi-text { font-size: .9rem; color: #475569; line-height: 1.75; margin-bottom: 20px; font-style: italic; }
        .testi-author { display: flex; align-items: center; gap: 12px; }
        .testi-avatar {
            width: 42px; height: 42px; border-radius: 50%; background: var(--navy);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; color: #fff; font-size: .9rem; flex-shrink: 0;
        }
        .testi-name { font-weight: 700; font-size: .88rem; color: var(--navy); }
        .testi-role { font-size: .78rem; color: #94a3b8; margin-top: 2px; }

        /* ── CTA ── */
        .cta-section {
            background: linear-gradient(135deg, var(--navy) 0%, #1a3d5c 100%);
            padding: 96px 60px; text-align: center; position: relative; overflow: hidden;
        }
        .cta-section::before {
            content: ''; position: absolute; top: -100px; right: -100px;
            width: 400px; height: 400px; border-radius: 50%;
            background: rgba(181,134,13,.1); pointer-events: none;
        }
        .cta-section::after {
            content: ''; position: absolute; bottom: -80px; left: -80px;
            width: 300px; height: 300px; border-radius: 50%;
            background: rgba(181,134,13,.07); pointer-events: none;
        }
        .cta-inner { position: relative; z-index: 2; }
        .cta-title { font-size: clamp(1.8rem, 4vw, 2.8rem); font-weight: 900; color: #fff; margin-bottom: 16px; }
        .cta-sub { font-size: 1.05rem; color: rgba(255,255,255,.7); max-width: 520px; margin: 0 auto 40px; line-height: 1.7; }
        .cta-buttons { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }
        .cta-btn-gold {
            padding: 14px 36px; background: var(--gold); border-radius: 10px;
            font-size: .98rem; font-weight: 700; color: #fff; transition: all .2s;
            box-shadow: 0 4px 20px rgba(181,134,13,.4);
        }
        .cta-btn-gold:hover { background: #9a7010; transform: translateY(-1px); }
        .cta-btn-white {
            padding: 14px 36px; background: rgba(255,255,255,.12); border: 1.5px solid rgba(255,255,255,.3);
            border-radius: 10px; font-size: .98rem; font-weight: 600; color: #fff; transition: all .2s;
        }
        .cta-btn-white:hover { background: rgba(255,255,255,.22); }

        /* ── FOOTER ── */
        .footer { background: #0f1c31; color: rgba(255,255,255,.7); padding: 64px 60px 32px; }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 48px; margin-bottom: 48px; }
        .footer-brand-logo { display: flex; align-items: center; gap: 10px; margin-bottom: 16px; }
        .footer-icon {
            width: 34px; height: 34px; background: var(--navy); border-radius: 8px;
            display: flex; align-items: center; justify-content: center; color: #fff; font-size: .85rem;
        }
        .footer-brand-name { font-size: 1.15rem; font-weight: 800; color: #fff; }
        .footer-brand-name span { color: var(--gold-light); }
        .footer-desc { font-size: .85rem; line-height: 1.7; max-width: 260px; }
        .footer-socials { display: flex; gap: 10px; margin-top: 20px; }
        .footer-social {
            width: 34px; height: 34px; background: rgba(255,255,255,.08); border-radius: 8px;
            display: flex; align-items: center; justify-content: center; font-size: .85rem;
            color: rgba(255,255,255,.6); transition: all .2s;
        }
        .footer-social:hover { background: var(--gold); color: #fff; }
        .footer-col h4 { font-size: .88rem; font-weight: 700; color: #fff; margin-bottom: 16px; text-transform: uppercase; letter-spacing: .8px; }
        .footer-col a { display: block; font-size: .85rem; color: rgba(255,255,255,.55); margin-bottom: 10px; transition: color .2s; }
        .footer-col a:hover { color: var(--gold-light); }
        .footer-bottom { border-top: 1px solid rgba(255,255,255,.08); padding-top: 28px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; font-size: .82rem; }
        .footer-bottom-links { display: flex; gap: 20px; }
        .footer-bottom-links a { color: rgba(255,255,255,.4); transition: color .2s; }
        .footer-bottom-links a:hover { color: rgba(255,255,255,.7); }

        /* ── RESPONSIVE ── */
        @media (max-width: 1024px) {
            .nav { padding: 0 30px; }
            .section { padding: 72px 30px; }
            .features-grid { grid-template-columns: 1fr; }
            .who-grid { grid-template-columns: 1fr; }
            .footer-grid { grid-template-columns: 1fr 1fr; }
            .cta-section { padding: 72px 30px; }
        }
        @media (max-width: 768px) {
            .nav-links { display: none; }
            .hero-stats { gap: 28px; }
            .steps-grid { grid-template-columns: 1fr; }
            .footer { padding: 48px 24px 24px; }
            .footer-grid { grid-template-columns: 1fr; gap: 32px; }
            .trusted-bar { gap: 24px; padding: 18px 24px; }
            .section { padding: 64px 24px; }
            .cta-section { padding: 64px 24px; }
        }

        /* ── SCROLL ANIMATIONS ── */
        .reveal {
            opacity: 0; transform: translateY(28px); transition: opacity .6s ease, transform .6s ease;
        }
        .reveal.visible { opacity: 1; transform: none; }
    </style>
</head>
<body>

{{-- ═══════════════════════════════════════════ NAVBAR ══ --}}
<nav class="nav" id="mainNav">
    <div class="nav-logo">
        <div class="nav-logo-icon"><i class="fas fa-shield-alt"></i></div>
        <div class="nav-logo-text">Lex<span>Connect</span></div>
    </div>
    <div class="nav-links">
        <a href="#how-it-works">How It Works</a>
        <a href="#features">Features</a>
        <a href="#for-who">For Who</a>
        <a href="#testimonials">Testimonials</a>
    </div>
    <div class="nav-actions">
        <a href="{{ route('login') }}" class="btn-outline">Sign In</a>
        <a href="{{ route('register') }}" class="btn-primary">Get Started</a>
    </div>
</nav>

{{-- ═══════════════════════════════════════════ HERO ══ --}}
<section class="hero">
    <div class="hero-bg-shapes">
        <div class="hero-circle hero-circle-1"></div>
        <div class="hero-circle hero-circle-2"></div>
        <div class="hero-circle hero-circle-3"></div>
    </div>
    <div class="hero-content">
        <div class="hero-badge">
            <i class="fas fa-bolt"></i> Smart Legal Services Platform
        </div>
        <h1 class="hero-title">
            Legal Help,<br>
            <span>Connected & Simplified</span>
        </h1>
        <p class="hero-subtitle">
            LexConnect bridges the gap between clients who need legal guidance, verified lawyers ready to help, and law firms growing their teams — all in one powerful platform.
        </p>
        <div class="hero-actions">
            <a href="{{ route('register') }}" class="hero-btn-primary">
                <i class="fas fa-rocket" style="margin-right:8px;"></i> Get Started Free
            </a>
            <a href="#how-it-works" class="hero-btn-secondary">
                <i class="fas fa-play-circle" style="margin-right:8px;"></i> See How It Works
            </a>
        </div>
        <div class="hero-stats">
            <div>
                <div class="hero-stat-num"><span>500</span>+</div>
                <div class="hero-stat-label">Verified Lawyers</div>
            </div>
            <div>
                <div class="hero-stat-num"><span>10K</span>+</div>
                <div class="hero-stat-label">Cases Handled</div>
            </div>
            <div>
                <div class="hero-stat-num"><span>98</span>%</div>
                <div class="hero-stat-label">Client Satisfaction</div>
            </div>
            <div>
                <div class="hero-stat-num"><span>50</span>+</div>
                <div class="hero-stat-label">Law Firms</div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════ TRUSTED ══ --}}
<div class="trusted-bar">
    <div class="trusted-label">Trusted by</div>
    <div class="trusted-logo">Morrison & Associates</div>
    <div class="trusted-logo">Hartwell Legal Group</div>
    <div class="trusted-logo">Pacific Law Partners</div>
    <div class="trusted-logo">Cruz & Co. Attorneys</div>
    <div class="trusted-logo">Summit Legal LLP</div>
</div>

{{-- ═══════════════════════════════════════════ HOW IT WORKS ══ --}}
<section class="section section-center" id="how-it-works">
    <div class="reveal">
        <div class="section-tag">How It Works</div>
        <h2 class="section-title">Simple. Transparent. Effective.</h2>
        <p class="section-sub">From booking to completion — LexConnect makes every step of the legal process clear and hassle-free.</p>
    </div>
    <div class="steps-grid reveal">
        <div class="step-card">
            <div class="step-number">1</div>
            <div class="step-icon"><i class="fas fa-search"></i></div>
            <h3>Find Your Lawyer</h3>
            <p>Browse verified lawyers by specialty, location, experience, and hourly rate. Filter to find the perfect fit for your legal needs.</p>
        </div>
        <div class="step-card">
            <div class="step-number">2</div>
            <div class="step-icon gold"><i class="fas fa-calendar-check"></i></div>
            <h3>Book a Consultation</h3>
            <p>Schedule a video call, phone call, or in-person meeting at your preferred time. Pay securely online — no hidden fees.</p>
        </div>
        <div class="step-card">
            <div class="step-number">3</div>
            <div class="step-icon green"><i class="fas fa-handshake"></i></div>
            <h3>Get Legal Guidance</h3>
            <p>Your lawyer reviews, confirms, and conducts the session. Chat securely, share documents, and get the advice you need.</p>
        </div>
        <div class="step-card">
            <div class="step-number">4</div>
            <div class="step-icon" style="background:rgba(124,58,237,.1);color:#7c3aed;"><i class="fas fa-star"></i></div>
            <h3>Rate & Review</h3>
            <p>After your session, leave a review to help others find the right lawyer. Build trust across the community.</p>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════ FEATURES ══ --}}
<section class="section section-alt section-center" id="features">
    <div class="reveal">
        <div class="section-tag">Platform Features</div>
        <h2 class="section-title">Everything You Need in One Place</h2>
        <p class="section-sub">Powerful tools for clients, lawyers, and law firms — designed to make legal services accessible and efficient.</p>
    </div>
    <div class="features-grid reveal">
        <div class="feature-card">
            <div class="feature-icon fi-navy"><i class="fas fa-user-shield"></i></div>
            <div>
                <h3>Verified Lawyer Profiles</h3>
                <p>Every lawyer is vetted and verified. View detailed profiles with specialties, experience, ratings, and hourly rates before booking.</p>
            </div>
        </div>
        <div class="feature-card">
            <div class="feature-icon fi-gold"><i class="fas fa-video"></i></div>
            <div>
                <h3>Multi-Mode Consultations</h3>
                <p>Book video, phone, or in-person consultations. Choose the duration and format that fits your schedule and budget.</p>
            </div>
        </div>
        <div class="feature-card">
            <div class="feature-icon fi-green"><i class="fas fa-comments"></i></div>
            <div>
                <h3>Secure Messaging</h3>
                <p>Communicate directly with your lawyer through an encrypted messaging system. Share questions and updates anytime.</p>
            </div>
        </div>
        <div class="feature-card">
            <div class="feature-icon fi-blue"><i class="fas fa-credit-card"></i></div>
            <div>
                <h3>Secure Online Payments</h3>
                <p>Pay for consultations safely with automatic refunds on declines. Full transaction history and payment tracking built in.</p>
            </div>
        </div>
        <div class="feature-card">
            <div class="feature-icon fi-purple"><i class="fas fa-building-columns"></i></div>
            <div>
                <h3>Law Firm Management</h3>
                <p>Law firms can build teams by accepting lawyer applications, manage firm-wide consultations, and track revenue dashboards.</p>
            </div>
        </div>
        <div class="feature-card">
            <div class="feature-icon fi-red"><i class="fas fa-chart-line"></i></div>
            <div>
                <h3>Earnings & Analytics</h3>
                <p>Lawyers and firms get detailed earnings dashboards — monthly revenue, per-case breakdown, and full payment history.</p>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════ FOR WHO ══ --}}
<section class="section section-center" id="for-who">
    <div class="reveal">
        <div class="section-tag">Made For Everyone</div>
        <h2 class="section-title">One Platform, Three Portals</h2>
        <p class="section-sub">Whether you need legal help, offer legal services, or manage a legal team — LexConnect has a dedicated experience for you.</p>
    </div>
    <div class="who-grid reveal">
        {{-- CLIENT --}}
        <div class="who-card who-card-client">
            <div class="who-card-bg"><i class="fas fa-user"></i></div>
            <div class="who-icon"><i class="fas fa-user"></i></div>
            <h3>For Clients</h3>
            <p>Need legal help? Find and book a verified lawyer in minutes, from anywhere.</p>
            <ul class="who-list">
                <li><i class="fas fa-check-circle"></i> Search by specialty & location</li>
                <li><i class="fas fa-check-circle"></i> Book & pay online securely</li>
                <li><i class="fas fa-check-circle"></i> Video, phone, or in-person</li>
                <li><i class="fas fa-check-circle"></i> Full consultation history</li>
                <li><i class="fas fa-check-circle"></i> Direct lawyer messaging</li>
            </ul>
            <a href="{{ route('register') }}" class="who-btn who-btn-light">Get Legal Help →</a>
        </div>
        {{-- LAWYER --}}
        <div class="who-card who-card-lawyer">
            <div class="who-card-bg"><i class="fas fa-gavel"></i></div>
            <div class="who-icon"><i class="fas fa-gavel"></i></div>
            <h3>For Lawyers</h3>
            <p>Grow your practice, manage your schedule, and connect with a law firm.</p>
            <ul class="who-list">
                <li><i class="fas fa-check-circle"></i> Accept or decline bookings</li>
                <li><i class="fas fa-check-circle"></i> Manage availability status</li>
                <li><i class="fas fa-check-circle"></i> Track earnings & payouts</li>
                <li><i class="fas fa-check-circle"></i> Join a law firm network</li>
                <li><i class="fas fa-check-circle"></i> Professional profile page</li>
            </ul>
            <a href="{{ route('register') }}" class="who-btn who-btn-light">Join as a Lawyer →</a>
        </div>
        {{-- LAW FIRM --}}
        <div class="who-card who-card-firm">
            <div class="who-card-bg"><i class="fas fa-building-columns"></i></div>
            <div class="who-icon"><i class="fas fa-building-columns"></i></div>
            <h3>For Law Firms</h3>
            <p>Build your team, manage lawyers, and oversee all firm-wide performance.</p>
            <ul class="who-list">
                <li><i class="fas fa-check-circle"></i> Review lawyer applications</li>
                <li><i class="fas fa-check-circle"></i> Firm-wide consultation overview</li>
                <li><i class="fas fa-check-circle"></i> Revenue & earnings dashboard</li>
                <li><i class="fas fa-check-circle"></i> Verified firm badge</li>
                <li><i class="fas fa-check-circle"></i> Full team management</li>
            </ul>
            <a href="{{ route('register') }}" class="who-btn who-btn-light">Register Your Firm →</a>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════ TESTIMONIALS ══ --}}
<section class="section section-alt section-center" id="testimonials">
    <div class="reveal">
        <div class="section-tag">Testimonials</div>
        <h2 class="section-title">Trusted by Thousands</h2>
        <p class="section-sub">Real stories from clients, lawyers, and firms who transformed their legal experience with LexConnect.</p>
    </div>
    <div class="testimonials-grid reveal">
        <div class="testi-card">
            <div class="testi-stars">★★★★★</div>
            <p class="testi-text">"I needed urgent legal advice for a contract dispute. Found a lawyer, booked a video call, and had my questions answered within 2 hours. Incredible platform."</p>
            <div class="testi-author">
                <div class="testi-avatar" style="background:#1e2d4d;">J</div>
                <div>
                    <div class="testi-name">James R.</div>
                    <div class="testi-role">Small Business Owner — Client</div>
                </div>
            </div>
        </div>
        <div class="testi-card">
            <div class="testi-stars">★★★★★</div>
            <p class="testi-text">"LexConnect helped me grow my solo practice by 3x in just 6 months. The booking system is seamless and the earnings dashboard keeps me organized."</p>
            <div class="testi-author">
                <div class="testi-avatar" style="background:#1a3d2b;">S</div>
                <div>
                    <div class="testi-name">Sarah M., Esq.</div>
                    <div class="testi-role">Family Law Attorney</div>
                </div>
            </div>
        </div>
        <div class="testi-card">
            <div class="testi-stars">★★★★★</div>
            <p class="testi-text">"Our firm has onboarded 12 new lawyers through the platform. The application management and team dashboard has saved us countless administrative hours."</p>
            <div class="testi-author">
                <div class="testi-avatar" style="background:#7c3aed;">M</div>
                <div>
                    <div class="testi-name">Morrison & Associates</div>
                    <div class="testi-role">Law Firm — New York</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════ PRACTICE AREAS ══ --}}
<section class="section section-center">
    <div class="reveal">
        <div class="section-tag">Practice Areas</div>
        <h2 class="section-title">Lawyers for Every Legal Need</h2>
        <p class="section-sub">Find specialized attorneys across a wide range of practice areas.</p>
    </div>
    <div class="reveal" style="display:flex;flex-wrap:wrap;gap:12px;justify-content:center;max-width:800px;margin:0 auto;">
        @foreach(['Corporate Law','Family Law','Criminal Defense','Immigration Law','Real Estate','Personal Injury','Employment Law','Tax Law','Intellectual Property','Estate Planning'] as $area)
        <a href="{{ route('register') }}" style="padding:10px 20px;background:#fff;border:1.5px solid #e2e8f0;border-radius:100px;font-size:.88rem;font-weight:600;color:#1e2d4d;transition:all .2s;box-shadow:0 1px 4px rgba(0,0,0,.05);" onmouseover="this.style.borderColor='#b5860d';this.style.color='#b5860d';" onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#1e2d4d';">
            {{ $area }}
        </a>
        @endforeach
    </div>
</section>

{{-- ═══════════════════════════════════════════ CTA ══ --}}
<section class="cta-section">
    <div class="cta-inner reveal">
        <div class="section-tag" style="background:rgba(181,134,13,.25);color:#f0c040;margin-bottom:20px;">Join Today — It's Free</div>
        <h2 class="cta-title">Ready to Connect with the<br>Right Legal Expert?</h2>
        <p class="cta-sub">Join thousands of clients, lawyers, and firms already using LexConnect to simplify legal services.</p>
        <div class="cta-buttons">
            <a href="{{ route('register') }}" class="cta-btn-gold">
                <i class="fas fa-rocket" style="margin-right:8px;"></i> Create Free Account
            </a>
            <a href="{{ route('login') }}" class="cta-btn-white">
                <i class="fas fa-sign-in-alt" style="margin-right:8px;"></i> Sign In
            </a>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════ FOOTER ══ --}}
<footer class="footer">
    <div class="footer-grid">
        <div>
            <div class="footer-brand-logo">
                <div class="footer-icon"><i class="fas fa-shield-alt"></i></div>
                <div class="footer-brand-name">Lex<span>Connect</span></div>
            </div>
            <p class="footer-desc">Bridging the gap between people who need legal help and the professionals who provide it. Smart, simple, secure.</p>
            <div class="footer-socials">
                <a href="#" class="footer-social"><i class="fab fa-twitter"></i></a>
                <a href="#" class="footer-social"><i class="fab fa-linkedin"></i></a>
                <a href="#" class="footer-social"><i class="fab fa-facebook"></i></a>
                <a href="#" class="footer-social"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
        <div class="footer-col">
            <h4>Platform</h4>
            <a href="#how-it-works">How It Works</a>
            <a href="#features">Features</a>
            <a href="#for-who">For Clients</a>
            <a href="#for-who">For Lawyers</a>
            <a href="#for-who">For Law Firms</a>
        </div>
        <div class="footer-col">
            <h4>Practice Areas</h4>
            <a href="{{ route('register') }}">Corporate Law</a>
            <a href="{{ route('register') }}">Family Law</a>
            <a href="{{ route('register') }}">Criminal Defense</a>
            <a href="{{ route('register') }}">Immigration Law</a>
            <a href="{{ route('register') }}">Real Estate</a>
        </div>
        <div class="footer-col">
            <h4>Account</h4>
            <a href="{{ route('login') }}">Sign In</a>
            <a href="{{ route('register') }}">Create Account</a>
            <a href="{{ route('register') }}">Register as Lawyer</a>
            <a href="{{ route('register') }}">Register a Firm</a>
        </div>
    </div>
    <div class="footer-bottom">
        <div>&copy; {{ date('Y') }} LexConnect. All rights reserved.</div>
        <div class="footer-bottom-links">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
            <a href="#">Cookie Policy</a>
        </div>
    </div>
</footer>

<script>
    // Navbar scroll shadow
    const nav = document.getElementById('mainNav');
    window.addEventListener('scroll', () => {
        nav.classList.toggle('scrolled', window.scrollY > 20);
    });

    // Scroll reveal animation
    const reveals = document.querySelectorAll('.reveal');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.1 });
    reveals.forEach(el => observer.observe(el));
</script>
</body>
</html>
