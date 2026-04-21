<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LexConnect – @yield('title', 'Client Portal')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    @stack('styles')
    @livewireStyles
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="navbar-brand">
        <div class="brand-logo">
            <i class="fas fa-shield-alt"></i>
        </div>
        <div class="brand-text">
            <span class="brand-name">LexConnect</span>
            <span class="brand-sub">Client Portal</span>
        </div>
    </div>

    <div class="navbar-menu">
        <a href="{{ route('dashboard') }}" class="nav-link @if(request()->routeIs('dashboard')) active @endif">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <a href="{{ route('find-lawyers') }}" class="nav-link @if(request()->routeIs('find-lawyers')) active @endif">
            <i class="fas fa-search"></i> Find Lawyers
        </a>
        <a href="{{ route('consultations') }}" class="nav-link @if(request()->routeIs('consultations')) active @endif">
            <i class="fas fa-video"></i> Consultations
            @php $upcomingNavCount = Auth::check() ? \App\Models\Consultation::where('client_id',Auth::id())->where('status','upcoming')->count() : 0; @endphp
            @if($upcomingNavCount > 0)<span class="badge">{{ $upcomingNavCount }}</span>@endif
        </a>
        <a href="{{ route('messages') }}" class="nav-link @if(request()->routeIs('messages')) active @endif">
            <i class="fas fa-comment-dots"></i> Messages
            @php
                $unreadNavCount = 0;
                if (Auth::check()) {
                    $navConvs = \App\Models\Conversation::where('client_id', Auth::id())->orWhere('lawyer_id', Auth::id())->pluck('id');
                    $unreadNavCount = \App\Models\Message::whereIn('conversation_id', $navConvs)->where('sender_id','!=',Auth::id())->whereNull('read_at')->count();
                }
            @endphp
            @if($unreadNavCount > 0)<span class="badge" id="messagesBadge">{{ $unreadNavCount }}</span>@else<span class="badge" id="messagesBadge" style="display: none;">0</span>@endif
        </a>
        <a href="{{ route('payments') }}" class="nav-link @if(request()->routeIs('payments')) active @endif">
            <i class="fas fa-credit-card"></i> Payments
        </a>
    </div>

    @auth
    @php $authUser = Auth::user(); $initials = collect(explode(' ', $authUser->name))->map(fn($p)=>strtoupper($p[0]))->implode(''); @endphp
    <div class="nav-user-card" id="navUserCard" onclick="toggleUserMenu(event)">
        @if($authUser->avatar_url)
            <img src="{{ $authUser->avatar_url }}" alt="avatar" class="nav-user-avatar-img">
        @else
            <div class="nav-user-initials">{{ $initials }}</div>
        @endif
        <div class="nav-user-text">
            <span class="nav-user-name">{{ $authUser->name }}</span>
            <span class="nav-user-role">{{ ucfirst($authUser->role) }}</span>
        </div>

        <div class="nav-user-menu" id="navUserMenu">
            <a href="{{ route('client.profile') }}" class="nav-user-item"><i class="fas fa-user-circle"></i> Profile</a>
            <a href="{{ route('client.settings') }}" class="nav-user-item"><i class="fas fa-cog"></i> Settings</a>
            <a href="{{ route('client.help') }}" class="nav-user-item"><i class="fas fa-question-circle"></i> Help</a>
            <div class="nav-user-divider"></div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-user-item nav-user-signout">
                    <i class="fas fa-sign-out-alt"></i> Sign Out
                </button>
            </form>
        </div>
    </div>
    @endauth

    <style>
    .nav-user-card {
        display: flex; align-items: center; gap: 10px;
        padding: 5px 14px 5px 5px;
        border-radius: 50px; border: 1.5px solid #e5e7eb;
        cursor: pointer; position: relative;
        transition: border-color .15s, box-shadow .15s;
        user-select: none;
    }
    .nav-user-card:hover { border-color: #c7d0de; box-shadow: 0 2px 8px rgba(0,0,0,.07); }
    .nav-user-initials {
        width: 38px; height: 38px; border-radius: 50%;
        background: #e8edf6; color: #1e2d4d;
        display: flex; align-items: center; justify-content: center;
        font-size: .82rem; font-weight: 700; flex-shrink: 0;
        border: 1.5px solid #c7d0de;
    }
    .nav-user-avatar-img { width: 38px; height: 38px; border-radius: 50%; object-fit: cover; display: block; flex-shrink: 0; }
    .nav-user-text { display: flex; flex-direction: column; line-height: 1.25; }
    .nav-user-name { font-size: .88rem; font-weight: 600; color: #1e2d4d; white-space: nowrap; }
    .nav-user-role { font-size: .75rem; color: #6b7280; }
    .nav-user-menu {
        display: none; position: absolute; right: 0; top: calc(100% + 10px);
        background: #fff; border-radius: 12px;
        box-shadow: 0 8px 30px rgba(0,0,0,.13);
        min-width: 190px; z-index: 9999;
        padding: 8px 0; border: 1px solid #f0f0f0; text-align: left;
    }
    .nav-user-card.open .nav-user-menu { display: block; }
    .nav-user-item {
        display: flex; align-items: center; gap: 12px;
        padding: 11px 18px; font-size: .9rem; color: #374151;
        text-decoration: none; background: none; border: none;
        width: 100%; text-align: left; cursor: pointer; font-family: inherit;
        transition: background .12s;
    }
    .nav-user-item:hover { background: #f9fafb; color: #1e2d4d; text-decoration: none; }
    .nav-user-item i { width: 16px; text-align: center; color: #6b7280; }
    .nav-user-signout { color: #dc2626 !important; }
    .nav-user-signout i { color: #dc2626 !important; }
    .nav-user-divider { height: 1px; background: #f0f2f5; margin: 6px 0; }
    </style>

    <script>
    function toggleUserMenu(e) {
        e.stopPropagation();
        document.getElementById('navUserCard').classList.toggle('open');
    }
    document.addEventListener('click', function(e) {
        var card = document.getElementById('navUserCard');
        if (card && !card.contains(e.target)) card.classList.remove('open');
    });
    </script>
</nav>

<!-- Page Content -->
<main class="main-content @yield('body-class')">
    @yield('content')
</main>

<!-- Footer -->
@hasSection('hide-footer')
@else
<footer class="site-footer">
    <span>&copy; 2026 LexConnect. All rights reserved.</span>
    <div class="footer-badges">
        <span><i class="fas fa-shield-alt"></i> SSL Secured</span>
        <span><i class="fas fa-gavel"></i> Bar Certified</span>
    </div>
</footer>
@endif

@vite(['resources/js/app.js'])

@auth
<script>
// Global real-time message notifications
(function() {
    var currentUserId = {{ Auth::id() }};
    var messagesBadge = document.getElementById('messagesBadge');
    
    function updateMessagesBadge(count) {
        console.log('Updating messages badge to:', count);
        if (messagesBadge) {
            if (count > 0) {
                messagesBadge.textContent = count;
                messagesBadge.style.display = '';
            } else {
                messagesBadge.style.display = 'none';
            }
        }
    }
    
    function incrementMessagesBadge() {
        if (messagesBadge) {
            var currentCount = parseInt(messagesBadge.textContent) || 0;
            updateMessagesBadge(currentCount + 1);
        }
    }

    function decrementMessagesBadge(amount) {
        if (!messagesBadge) return;
        var currentCount = parseInt(messagesBadge.textContent) || 0;
        updateMessagesBadge(Math.max(0, currentCount - Math.max(0, amount || 0)));
    }
    
    // Wait for Echo to initialize, then set up global listeners
    setTimeout(function() {
        if (window.Echo) {
            console.log('Setting up global message notifications for user:', currentUserId);
            
            // Get user's conversation IDs
            @php
                $userConversationIds = Auth::check() 
                    ? \App\Models\Conversation::where('client_id', Auth::id())->orWhere('lawyer_id', Auth::id())->pluck('id')->toArray()
                    : [];
            @endphp
            var conversationIds = @json($userConversationIds);
            
            console.log('User conversations:', conversationIds);
            
            // Listen to all user's conversations for new messages
            conversationIds.forEach(function(convId) {
                console.log('Setting up notification listener for conversation:', convId);
                
                window.Echo.private('conversation.' + convId)
                    .listen('.MessageSent', function(e) {
                        console.log('Global notification - received message:', e);
                        
                        // Only show notification if message is from someone else
                        if (e.sender_id !== currentUserId) {
                            console.log('New message from other user - updating badge');
                            
                            // Only increment badge if we're NOT on the messages page
                            // (messages page handles its own real-time updates)
                            if (!window.location.pathname.includes('/messages')) {
                                incrementMessagesBadge();
                                
                                // Optional: Show browser notification
                                if ('Notification' in window && Notification.permission === 'granted') {
                                    new Notification('New Message', {
                                        body: 'You have a new message from ' + (e.sender_name || 'someone'),
                                        icon: '/favicon.ico'
                                    });
                                }
                            }
                        }
                    })
                    .listen('.MessageDeleted', function(e) {
                        if (!window.location.pathname.includes('/messages')) {
                            decrementMessagesBadge(parseInt(e.deleted_unread_count || 0, 10) || 0);
                        }
                    })
                    .error(function(error) {
                        console.error('❌ Error setting up global listener for conversation', convId, ':', error);
                    });
            });
            
            // Request notification permission if not already granted
            if ('Notification' in window && Notification.permission === 'default') {
                Notification.requestPermission();
            }
            
        } else {
            console.error('❌ Echo not available for global notifications');
        }
    }, 1500); // Wait a bit longer for Echo to fully initialize
})();
</script>
@endauth

@stack('scripts')
@livewireScripts
</body>
</html>
