<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LexConnect – @yield('title', 'Law Firm Portal')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    @stack('styles')
    @livewireStyles
</head>
<body class="lf-body">

<div class="lf-wrapper">
    {{-- SIDEBAR --}}
    <aside class="lf-sidebar">
        <div class="lf-logo">
            <div class="lf-logo-icon"><i class="fas fa-building-columns"></i></div>
            <div>
                <div class="lf-logo-name">LexConnect</div>
                <div class="lf-logo-sub">Law Firm Portal</div>
            </div>
        </div>

        @auth
        @php $u = Auth::user(); $firm = $u->lawFirmProfile; @endphp
        <div class="lf-profile-box">
            <div class="lf-firm-initial">{{ strtoupper(substr($firm->firm_name ?? $u->name, 0, 2)) }}</div>
            <div class="lf-profile-info">
                <div class="lf-profile-name">{{ $firm->firm_name ?? $u->name }}</div>
                <div class="lf-profile-meta">{{ $u->name }}</div>
                @if($firm && $firm->is_verified)
                    <div class="lf-verified-badge"><i class="fas fa-circle-check"></i> Verified</div>
                @endif
            </div>
        </div>
        @endauth

        <nav class="lf-nav">
            <a href="{{ route('lawfirm.dashboard') }}" class="lf-nav-link @if(request()->routeIs('lawfirm.dashboard')) active @endif">
                <i class="fas fa-th-large"></i> <span>Dashboard</span>
            </a>
            <a href="{{ route('lawfirm.lawyers') }}" class="lf-nav-link @if(request()->routeIs('lawfirm.lawyers')) active @endif">
                <i class="fas fa-users"></i> <span>Team & Applications</span>
                @php
                    $pendingAppCount = Auth::check() && Auth::user()->lawFirmProfile
                        ? \App\Models\FirmApplication::where('law_firm_id', Auth::user()->lawFirmProfile->id)->where('status','pending')->count()
                        : 0;
                @endphp
                @if($pendingAppCount > 0)<span class="lf-badge">{{ $pendingAppCount }}</span>@endif
            </a>
            <a href="{{ route('lawfirm.consultations') }}" class="lf-nav-link @if(request()->routeIs('lawfirm.consultations')) active @endif">
                <i class="fas fa-calendar-alt"></i> <span>Consultations</span>
            </a>
            <a href="{{ route('lawfirm.earnings') }}" class="lf-nav-link @if(request()->routeIs('lawfirm.earnings')) active @endif">
                <i class="fas fa-chart-line"></i> <span>Earnings</span>
            </a>
            <a href="{{ route('lawfirm.messages') }}" class="lf-nav-link @if(request()->routeIs('lawfirm.messages')) active @endif">
                <i class="fas fa-comments"></i> <span>Messages</span>
                @php
                    $unreadMsgCount = Auth::check()
                        ? \App\Models\Message::whereHas('conversation', fn($q) => $q->where('client_id', Auth::id()))
                            ->where('sender_id', '!=', Auth::id())
                            ->whereNull('read_at')
                            ->count()
                        : 0;
                @endphp
                @if($unreadMsgCount > 0)<span class="lf-badge" id="lawfirmMessagesBadge">{{ $unreadMsgCount }}</span>@else<span class="lf-badge" id="lawfirmMessagesBadge" style="display: none;">0</span>@endif
            </a>
            <a href="{{ route('lawfirm.profile') }}" class="lf-nav-link @if(request()->routeIs('lawfirm.profile')) active @endif">
                <i class="fas fa-building"></i> <span>Firm Profile</span>
            </a>
        </nav>

        <div class="lf-sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="lf-logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <main class="lf-main">
        <div class="lf-content">
            @yield('content')
        </div>
    </main>
</div>

@stack('scripts')

@auth
<script>
// Global real-time message notifications for law firms
(function() {
    var currentUserId = {{ Auth::id() }};
    var messagesBadge = document.getElementById('lawfirmMessagesBadge');
    
    function updateMessagesBadge(count) {
        console.log('📬 Updating law firm messages badge to:', count);
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
    
    // Wait for Echo to initialize, then set up global listeners
    setTimeout(function() {
        if (window.Echo) {
            console.log('🌐 Setting up global message notifications for law firm:', currentUserId);
            
            // Get law firm's conversation IDs (law firm is stored as client_id)
            @php
                $lawfirmConversationIds = Auth::check() 
                    ? \App\Models\Conversation::where('client_id', Auth::id())->pluck('id')->toArray()
                    : [];
            @endphp
            var conversationIds = @json($lawfirmConversationIds);
            
            console.log('👥 Law firm conversations:', conversationIds);
            
            // Listen to all law firm's conversations for new messages
            conversationIds.forEach(function(convId) {
                console.log('🔔 Setting up notification listener for conversation:', convId);
                
                window.Echo.private('conversation.' + convId)
                    .listen('.MessageSent', function(e) {
                        console.log('🔔 Global notification - received message:', e);
                        
                        // Only show notification if message is from someone else
                        if (e.sender_id !== currentUserId) {
                            console.log('📬 New message from other user - updating badge');
                            
                            // Only increment badge if we're NOT on the messages page
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

@livewireScripts
@vite(['resources/js/app.js'])
</body>
</html>
