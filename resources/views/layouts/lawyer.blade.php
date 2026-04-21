<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LexConnect – @yield('title', 'Lawyer Portal')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    @stack('styles')
    @livewireStyles
</head>
<body class="lp-body">

<div class="lp-wrapper">
    {{-- SIDEBAR --}}
    <aside class="lp-sidebar">
        <div class="lp-logo">
            <div class="lp-logo-icon"><i class="fas fa-shield-alt"></i></div>
            <div>
                <div class="lp-logo-name">LexConnect</div>
                <div class="lp-logo-sub">Lawyer Portal</div>
            </div>
        </div>

        @auth
        @php
            $u = Auth::user();
            $profile = $u->lawyerProfile;
            $profileStatusClass = $profile?->currentStatusClass() ?? 'offline';
            $profileStatusLabel = $profile?->currentStatusLabel() ?? 'Offline';
        @endphp
        <div class="lp-profile-box">
            @if($u->avatar_url)
                <img src="{{ $u->avatar_url }}" class="lp-avatar" alt="{{ $u->name }}">
            @else
                <div class="lp-avatar-initial">{{ strtoupper(substr($u->name,0,1)) }}</div>
            @endif
            <div class="lp-profile-info">
                <div class="lp-profile-name">{{ $u->name }}</div>
                <div class="lp-profile-spec">{{ $profile->specialty ?? 'Attorney at Law' }}</div>
                <div class="lp-avail-badge-sm {{ $profileStatusClass }}">
                    <span class="dot"></span> {{ $profileStatusLabel }}
                </div>
            </div>
        </div>
        @endauth

        <nav class="lp-nav">
            <a href="{{ route('lawyer.dashboard') }}" class="lp-nav-link @if(request()->routeIs('lawyer.dashboard')) active @endif">
                <i class="fas fa-th-large"></i> <span>Dashboard</span>
            </a>
            <a href="{{ route('lawyer.consultations') }}" class="lp-nav-link @if(request()->routeIs('lawyer.consultations')) active @endif">
                <i class="fas fa-calendar-alt"></i> <span>Consultations</span>
                @php
                    $pendingNavCount = 0;
                    if(Auth::check()) {
                        $pendingNavCount = \App\Models\Consultation::where('lawyer_id', Auth::id())
                            ->where('status', 'pending')
                            ->whereHas('payment', function($q) {
                                $q->where('status', 'downpayment_paid');
                            })
                            ->count();
                    }
                @endphp
                @if($pendingNavCount > 0)<span class="lp-badge">{{ $pendingNavCount }}</span>@endif
            </a>
            <a href="{{ route('lawyer.messages') }}" class="lp-nav-link @if(request()->routeIs('lawyer.messages')) active @endif">
                <i class="fas fa-comment-dots"></i> <span>Messages</span>
                @php
                    $unreadLpCount = 0;
                    if(Auth::check()) {
                        $lpConvIds = \App\Models\Conversation::where('lawyer_id', Auth::id())->pluck('id');
                        $unreadLpCount = \App\Models\Message::whereIn('conversation_id', $lpConvIds)->where('sender_id','!=',Auth::id())->whereNull('read_at')->count();
                    }
                @endphp
                @if($unreadLpCount > 0)<span class="lp-badge" id="lawyerMessagesBadge">{{ $unreadLpCount }}</span>@else<span class="lp-badge" id="lawyerMessagesBadge" style="display: none;">0</span>@endif
            </a>
            <a href="{{ route('lawyer.earnings') }}" class="lp-nav-link @if(request()->routeIs('lawyer.earnings')) active @endif">
                <i class="fas fa-wallet"></i> <span>Earnings</span>
            </a>
            <a href="{{ route('lawyer.profile') }}" class="lp-nav-link @if(request()->routeIs('lawyer.profile')) active @endif">
                <i class="fas fa-user-edit"></i> <span>My Profile</span>
            </a>
            <a href="{{ route('lawyer.firms') }}" class="lp-nav-link @if(request()->routeIs('lawyer.firms')) active @endif">
                <i class="fas fa-building-columns"></i> <span>My Firm</span>
                @php
                    $inFirmNow = Auth::check() && Auth::user()->lawyerProfile && Auth::user()->lawyerProfile->law_firm_id;
                @endphp
                @if(!$inFirmNow)<span class="lp-badge" style="background:#6c757d;">Join</span>@endif
            </a>
        </nav>

        <div class="lp-sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="lp-logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <main class="lp-main">
        <div class="lp-content">
            @yield('content')
        </div>
    </main>
</div>

@stack('scripts')

@auth
<script>
// Join global presence channel so clients can see lawyer is online
setTimeout(function() {
    if (window.Echo) {
        window.Echo.join('presence-user.{{ Auth::id() }}');
    }
}, 500);

// Global real-time message notifications for lawyers
(function() {
    var currentUserId = {{ Auth::id() }};
    var messagesBadge = document.getElementById('lawyerMessagesBadge');
    
    function updateMessagesBadge(count) {
        console.log('Updating lawyer messages badge to:', count);
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
            console.log('Setting up global message notifications for lawyer:', currentUserId);
            
            // Get lawyer's conversation IDs
            @php
                $lawyerConversationIds = Auth::check() 
                    ? \App\Models\Conversation::where('lawyer_id', Auth::id())->pluck('id')->toArray()
                    : [];
            @endphp
            var conversationIds = @json($lawyerConversationIds);
            
            console.log('Lawyer conversations:', conversationIds);
            
            // Listen to all lawyer's conversations for new messages
            conversationIds.forEach(function(convId) {
                console.log('Setting up notification listener for conversation:', convId);
                
                window.Echo.private('conversation.' + convId)
                    .listen('.MessageSent', function(e) {
                        console.log('Global notification - received message:', e);
                        
                        // Only show notification if message is from someone else
                        if (e.sender_id !== currentUserId) {
                            console.log('New message from other user - updating badge');
                            
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

@livewireScripts
@vite(['resources/js/app.js'])
</body>
</html>
