<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — LexConnect Admin</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: #f1f3f8; display: flex; min-height: 100vh; }

        /* ── Admin Sidebar ── */
        .ad-sidebar {
            width: 260px; min-width: 260px;
            background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%);
            color: #fff;
            display: flex; flex-direction: column;
            position: fixed; top: 0; left: 0;
            height: 100vh; overflow-y: auto; z-index: 100;
        }

        .ad-logo {
            display: flex; align-items: center; gap: 12px;
            padding: 24px 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }
        .ad-logo-icon {
            width: 40px; height: 40px; border-radius: 12px;
            background: linear-gradient(135deg, #7c3aed, #5b21b6);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; flex-shrink: 0;
        }
        .ad-logo-name { font-weight: 800; font-size: 1.05rem; letter-spacing: -.3px; }
        .ad-logo-sub  { font-size: .7rem; color: rgba(255,255,255,.45); margin-top: 2px; letter-spacing: .5px; text-transform: uppercase; }

        .ad-admin-chip {
            margin: 14px 20px;
            background: rgba(124,58,237,.2);
            border: 1px solid rgba(124,58,237,.4);
            border-radius: 8px;
            padding: 10px 14px;
            display: flex; align-items: center; gap: 10px;
        }
        .ad-admin-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: linear-gradient(135deg, #7c3aed, #5b21b6);
            display: flex; align-items: center; justify-content: center;
            font-size: .85rem; font-weight: 700; flex-shrink: 0;
        }
        .ad-admin-name { font-size: .88rem; font-weight: 600; }
        .ad-admin-role { font-size: .7rem; color: rgba(255,255,255,.5); margin-top: 1px; }

        /* Nav sections */
        .ad-nav { flex: 1; padding: 8px 0 12px; }
        .ad-nav-section-label {
            font-size: .65rem; font-weight: 700; color: rgba(255,255,255,.3);
            text-transform: uppercase; letter-spacing: 1px;
            padding: 16px 20px 6px;
        }
        .ad-nav-link {
            display: flex; align-items: center; gap: 11px;
            padding: 10px 20px; color: rgba(255,255,255,.6);
            text-decoration: none; font-size: .875rem; font-weight: 500;
            transition: all .18s; position: relative; border-radius: 0;
        }
        .ad-nav-link i { width: 18px; text-align: center; font-size: .875rem; flex-shrink: 0; }
        .ad-nav-link:hover { background: rgba(255,255,255,.07); color: #fff; text-decoration: none; }
        .ad-nav-link.active {
            background: rgba(124,58,237,.2); color: #fff;
            border-right: 3px solid #7c3aed;
        }
        .ad-nav-link.active i { color: #a78bfa; }
        .ad-badge {
            margin-left: auto; background: #7c3aed; color: #fff;
            font-size: .65rem; font-weight: 700; padding: 2px 7px;
            border-radius: 20px;
        }

        /* Sidebar footer */
        .ad-sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid rgba(255,255,255,.08);
        }
        .ad-logout-btn {
            width: 100%; padding: 10px; background: rgba(255,255,255,.07);
            color: rgba(255,255,255,.7); border: 1px solid rgba(255,255,255,.12);
            border-radius: 8px; font-size: .875rem; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            font-family: inherit; transition: background .2s;
        }
        .ad-logout-btn:hover { background: rgba(220,38,38,.25); color: #fff; }

        /* Main content */
        .ad-main { margin-left: 260px; flex: 1; min-height: 100vh; }
        .ad-topbar {
            background: #fff; border-bottom: 1px solid #e8edf5;
            padding: 14px 32px; display: flex; align-items: center;
            justify-content: space-between; position: sticky; top: 0; z-index: 50;
            box-shadow: 0 1px 4px rgba(0,0,0,.04);
        }
        .ad-topbar-title { font-size: 1rem; font-weight: 700; color: #1a1a2e; }
        .ad-topbar-meta  { font-size: .8rem; color: #9ca3af; margin-top: 1px; }
        .ad-topbar-right { display: flex; align-items: center; gap: 14px; }
        .ad-topbar-badge {
            background: linear-gradient(135deg, #7c3aed, #5b21b6);
            color: #fff; font-size: .75rem; font-weight: 700;
            padding: 5px 12px; border-radius: 20px; letter-spacing: .3px;
        }
        .ad-content { padding: 32px; max-width: 1300px; }

        @media (max-width: 900px) {
            body {
                display: block;
            }

            .ad-sidebar {
                position: static;
                width: 100%;
                min-width: 0;
                height: auto;
            }

            .ad-main {
                margin-left: 0;
            }

            .ad-topbar {
                padding: 14px 20px;
                flex-wrap: wrap;
                gap: 10px;
            }

            .ad-content {
                padding: 22px 20px;
            }
        }
    </style>
</head>
<body>
    <aside class="ad-sidebar">
        <div class="ad-logo">
            <div class="ad-logo-icon"><i class="fas fa-shield-halved"></i></div>
            <div>
                <div class="ad-logo-name">LexConnect</div>
                <div class="ad-logo-sub">Admin Portal</div>
            </div>
        </div>

        @auth
        <div class="ad-admin-chip">
            <div class="ad-admin-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
            <div>
                <div class="ad-admin-name">{{ Auth::user()->name }}</div>
                <div class="ad-admin-role">Super Admin</div>
            </div>
        </div>
        @endauth

        <nav class="ad-nav">
            <div class="ad-nav-section-label">Overview</div>
            <a href="{{ route('admin.dashboard') }}" class="ad-nav-link @if(request()->routeIs('admin.dashboard')) active @endif">
                <i class="fas fa-chart-pie"></i> Dashboard
            </a>

            <div class="ad-nav-section-label">Users</div>
            <a href="{{ route('admin.users') }}" class="ad-nav-link @if(request()->routeIs('admin.users')) active @endif">
                <i class="fas fa-users"></i> All Users
            </a>
            <a href="{{ route('admin.lawyers') }}" class="ad-nav-link @if(request()->routeIs('admin.lawyers')) active @endif">
                <i class="fas fa-gavel"></i> Lawyers
            </a>
            <a href="{{ route('admin.law-firms') }}" class="ad-nav-link @if(request()->routeIs('admin.law-firms')) active @endif">
                <i class="fas fa-building-columns"></i> Law Firms
            </a>

            <div class="ad-nav-section-label">Activity</div>
            <a href="{{ route('admin.consultations') }}" class="ad-nav-link @if(request()->routeIs('admin.consultations')) active @endif">
                <i class="fas fa-calendar-check"></i> Consultations
            </a>
            <a href="{{ route('admin.risk-events') }}" class="ad-nav-link @if(request()->routeIs('admin.risk-events')) active @endif">
                <i class="fas fa-shield-virus"></i> Fraud Review
                @php
                    $fraudReviewCount = \App\Models\PaymentRiskEvent::whereIn('recommendation', ['review', 'block'])->count();
                @endphp
                @if($fraudReviewCount > 0)<span class="ad-badge">{{ $fraudReviewCount }}</span>@endif
            </a>
        </nav>

        <div class="ad-sidebar-footer">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="ad-logout-btn">
                    <i class="fas fa-right-from-bracket"></i> Sign Out
                </button>
            </form>
        </div>
    </aside>

    <div class="ad-main">
        <div class="ad-topbar">
            <div>
                <div class="ad-topbar-title">@yield('page-title', 'Admin Dashboard')</div>
                <div class="ad-topbar-meta">{{ now()->format('l, F j, Y') }}</div>
            </div>
            <div class="ad-topbar-right">
                <span class="ad-topbar-badge"><i class="fas fa-shield-halved"></i> Admin</span>
            </div>
        </div>
        <div class="ad-content">
            @yield('content')
        </div>
    </div>
</body>
</html>
