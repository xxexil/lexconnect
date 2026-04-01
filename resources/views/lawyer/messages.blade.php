@extends('layouts.lawyer')
@section('title', 'Messages')
@section('content')
{{-- Fallback for when JavaScript is disabled: auto-refresh every 5 seconds --}}
<noscript>
    <meta http-equiv="refresh" content="5">
</noscript>

<div class="lp-page-header">
    <div>
        <h1 class="lp-page-title">Messages</h1>
        <p class="lp-page-sub">Client conversations</p>
    </div>
</div>

<div class="msg-layout">
    {{-- Conversations --}}
    <div class="msg-sidebar">
        <div class="msg-sidebar-header">
            <span><i class="fas fa-comment-dots"></i> Conversations</span>
            <span class="msg-count">{{ $conversations->count() }}</span>
        </div>
        <div style="padding:10px 14px 8px;border-bottom:1px solid #f0f2f5;">
            <style>#convSearch::placeholder{color:#aaa;}</style>
            <div style="display:flex;align-items:center;gap:8px;background:#f5f7fa;border:1px solid #e3e7ed;border-radius:8px;padding:7px 12px;">
                <i class="fas fa-search" style="color:#aaa;font-size:.8rem;"></i>
                <input type="text" id="convSearch" placeholder="Search clients..." autocomplete="off"
                    style="border:none;background:transparent;outline:none;font-size:.87rem;color:#333;width:100%;">
            </div>
        </div>
        @forelse($conversations as $conv)
        @php
            $other = $conv->client;
            $latest = $conv->latestMessage;
            $isActive = $activeConv && $activeConv->id === $conv->id;
            $unreadCount = $conv->messages->filter(fn($m) => $m->sender_id !== $user->id && !$m->read_at)->count();
        @endphp
        <a href="{{ route('lawyer.messages', ['conversation' => $conv->id]) }}"
           class="msg-conv-item {{ $isActive ? 'active' : '' }}">
            <div class="msg-conv-avatar">{{ strtoupper(substr($other->name,0,1)) }}</div>
            <div class="msg-conv-info">
                <div class="msg-conv-name">{{ $other->name }}</div>
                <div class="msg-conv-preview">{{ $latest ? Str::limit($latest->body, 38) : 'No messages yet' }}</div>
            </div>
            <div class="msg-conv-meta">
                @if($latest)<div class="msg-conv-time">{{ $latest->created_at->diffForHumans(null, true) }}</div>@endif
                @if($unreadCount > 0)<div class="msg-unread-badge">{{ $unreadCount }}</div>@endif
            </div>
        </a>
        @empty
        <div style="padding:24px;text-align:center;color:#999;font-size:.88rem;">
            <i class="fas fa-comment-slash" style="font-size:2rem;margin-bottom:8px;display:block;"></i>
            No conversations yet
        </div>
        @endforelse
        <div id="convNoResults" style="display:none;padding:24px;text-align:center;color:rgba(255,255,255,.45);font-size:.85rem;">
            <i class="fas fa-search" style="display:block;font-size:1.4rem;margin-bottom:6px;"></i>
            No results found
        </div>
    </div>

    {{-- Chat window --}}
    <div class="msg-chat">
        @if($activeConv)
        @php $other = $activeConv->client; @endphp
        <div class="msg-chat-header">
            <div class="msg-chat-avatar">{{ strtoupper(substr($other->name,0,1)) }}</div>
            <div>
                <div class="msg-chat-name">{{ $other->name }}</div>
                <div class="msg-chat-status">
                    <i class="fas fa-circle" id="presenceDot" style="font-size:.45rem;color:#d1d5db;"></i>
                    <span id="presenceText">Offline</span>
                </div>
            </div>

        </div>
        <div class="msg-bubbles" id="bubbles">
            @foreach($messages as $m)
            @php $mine = $m->sender_id === $user->id; @endphp
            <div class="msg-bubble-wrap {{ $mine ? 'mine' : 'theirs' }}">
                <div class="msg-bubble {{ $mine ? 'mine' : 'theirs' }}">
                    <div class="msg-bubble-text">{{ $m->body }}</div>
                    <div class="msg-bubble-time">{{ $m->created_at->format('g:i A') }}</div>
                </div>
            </div>
            @endforeach
        </div>
        <form id="sendForm" class="msg-form">
            @csrf
            <input type="hidden" name="conversation_id" id="convId" value="{{ $activeConv->id }}">
            <input type="text" name="body" id="msgInput" class="msg-input" placeholder="Type a message..." autocomplete="off" required>
            <button type="submit" class="msg-send-btn"><i class="fas fa-paper-plane"></i></button>
        </form>
        @else
        <div class="msg-chat-empty">
            <i class="fas fa-comment-dots"></i>
            <h3>Select a conversation</h3>
            <p>Choose a client to start chatting</p>
        </div>
        @endif
    </div>
</div>

<script>
(function(){
    var bubbles = document.getElementById('bubbles');
    if (bubbles) bubbles.scrollTop = bubbles.scrollHeight;

    var input = document.getElementById('convSearch');
    var noResults = document.getElementById('convNoResults');
    if (input) {
        input.addEventListener('input', function(){
            var q = this.value.toLowerCase();
            var items = document.querySelectorAll('.msg-conv-item');
            var visible = 0;
            items.forEach(function(el){
                var name    = (el.querySelector('.msg-conv-name')    || {}).textContent || '';
                var preview = (el.querySelector('.msg-conv-preview') || {}).textContent || '';
                var show    = (name + preview).toLowerCase().indexOf(q) !== -1;
                el.style.display = show ? '' : 'none';
                if (show) visible++;
            });
            if (noResults) noResults.style.display = (visible === 0 && q.length > 0) ? 'block' : 'none';
        });
    }

    // ── Real-time messaging ──────────────────────────────────────────────────
    var currentUserId = {{ auth()->id() }};
    var convId        = document.getElementById('convId') ? parseInt(document.getElementById('convId').value) : null;
    var sendForm      = document.getElementById('sendForm');
    var msgInput      = document.getElementById('msgInput');
    var csrfToken     = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';

    function appendBubble(body, time, mine) {
        if (!bubbles) return;
        var wrap = document.createElement('div');
        wrap.className = 'msg-bubble-wrap ' + (mine ? 'mine' : 'theirs');
        var bubble = document.createElement('div');
        bubble.className = 'msg-bubble ' + (mine ? 'mine' : 'theirs');
        bubble.innerHTML = '<div class="msg-bubble-text">' + escHtml(body) + '</div>'
                         + '<div class="msg-bubble-time">' + time + '</div>';
        wrap.appendChild(bubble);
        bubbles.appendChild(wrap);
        bubbles.scrollTop = bubbles.scrollHeight;
    }

    function escHtml(text) {
        return text.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
                   .replace(/"/g,'&quot;').replace(/'/g,'&#39;');
    }

    // Clean up any duplicate conversations that might exist
    function removeDuplicateConversations() {
        var seenConversations = new Set();
        var conversationItems = document.querySelectorAll('.msg-conv-item');
        var duplicatesRemoved = 0;
        
        console.log('🔍 Checking for duplicate conversations, found', conversationItems.length, 'items');
        
        conversationItems.forEach(function(item) {
            var href = item.getAttribute('href');
            if (href) {
                var match = href.match(/conversation=(\d+)/);
                if (match) {
                    var conversationId = match[1];
                    if (seenConversations.has(conversationId)) {
                        console.log('🗑️ Removing duplicate conversation:', conversationId);
                        item.remove();
                        duplicatesRemoved++;
                    } else {
                        seenConversations.add(conversationId);
                    }
                }
            }
        });
        
        console.log('✅ Duplicate cleanup complete. Removed', duplicatesRemoved, 'duplicates');
    }

    // Clean up duplicates on page load
    removeDuplicateConversations();
    
    // Also clean up duplicates periodically to catch any created by real-time updates
    setInterval(removeDuplicateConversations, 5000); // Every 5 seconds

    // Move conversation item to top of the sidebar list via simple DOM reorder
    function moveConversationToTop(conversationId) {
        var sidebar = document.querySelector('.msg-sidebar');
        if (!sidebar) return;

        var items = sidebar.querySelectorAll('.msg-conv-item');
        var target = null;
        items.forEach(function(item) {
            var href = item.getAttribute('href') || '';
            var match = href.match(/[?&]conversation=(\d+)/);
            if (match && parseInt(match[1]) === parseInt(conversationId)) {
                target = item;
            }
        });

        if (!target) return;

        // Find the first conv item anchor and insert before it
        var firstItem = sidebar.querySelector('.msg-conv-item');
        if (firstItem && firstItem !== target) {
            sidebar.insertBefore(target, firstItem);
        }
    }

    // AJAX send
    if (sendForm && convId) {
        sendForm.addEventListener('submit', function(e) {
            e.preventDefault();
            var body = msgInput.value.trim();
            if (!body) return;

            // Optimistically clear the input and show the bubble immediately
            msgInput.value = '';
            appendBubble(body, new Date().toLocaleTimeString([], {hour:'numeric',minute:'2-digit'}), true);

            fetch('{{ route("lawyer.messages.send") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ conversation_id: convId, body: body }),
            })
            .then(function(r){
                if (!r.ok) throw new Error('Server error');
                return r.json();
            })
            .catch(function(){ /* message already shown, silently ignore */ });
        });

        // Echo: private channel for messages + presence channel for online status
        // Wait a moment for Echo to initialize before checking
        setTimeout(function() {
            if (window.Echo) {
                console.log('Setting up WebSocket listeners for conversation:', convId);
                
                var dot  = document.getElementById('presenceDot');
                var text = document.getElementById('presenceText');

                function setOnline(isOnline) {
                    console.log('Setting online status:', isOnline);
                    if (!dot || !text) return;
                    dot.style.color  = isOnline ? '#22c55e' : '#d1d5db';
                    text.textContent = isOnline ? 'Online' : 'Offline';
                }

                // Listen for messages on private channel
                console.log('Subscribing to private channel: conversation.' + convId);
                var privateChannel = window.Echo.private('conversation.' + convId);
                
                privateChannel.listen('.MessageSent', function(e) {
                    console.log('🎉 RECEIVED MessageSent event:', e);
                    if (e.sender_id !== currentUserId) {
                        console.log('✅ Displaying message from other user');
                        appendBubble(e.body, e.time, false);
                        
                        // Don't move the active conversation - it's already at the top and active
                        console.log('⏭️ Skipping move for active conversation');
                    } else {
                        console.log('⏭️ Ignoring message from self');
                    }
                })
                .subscribed(function() {
                    console.log('✅ Successfully subscribed to private channel: conversation.' + convId);
                })
                .error(function(error) {
                    console.error('❌ Error with private channel:', error);
                });

                // Track online presence on presence channel
                console.log('Joining presence channel: presence-conversation.' + convId);
                window.Echo.join('presence-conversation.' + convId)
                    .here(function(users) {
                        console.log('Users currently here:', users);
                        setOnline(users.some(function(u){ return u.id !== currentUserId; }));
                    })
                    .joining(function(user) {
                        console.log('User joined:', user);
                        if (user.id !== currentUserId) setOnline(true);
                    })
                    .leaving(function(user) {
                        console.log('User left:', user);
                        if (user.id !== currentUserId) setOnline(false);
                    })
                    .error(function(error) {
                        console.error('Error joining presence channel:', error);
                    });
            } else {
                console.error('Echo still not available after delay');
            }
        }, 1000); // Wait 1 second for Echo to initialize
    }

    // Global real-time message notifications (EXACT COPY from working client implementation)
    setTimeout(function() {
        if (window.Echo) {
            console.log('🌐 Setting up global lawyer message notifications for user:', currentUserId);
            
            // Get lawyer's conversation IDs (only where user is the LAWYER)
            @php
                $lawyerConversationIds = Auth::check() 
                    ? \App\Models\Conversation::where('lawyer_id', Auth::id())->pluck('id')->toArray()
                    : [];
            @endphp
            var conversationIds = @json($lawyerConversationIds);
            
            console.log('👥 Lawyer conversations:', conversationIds);
            
            // Listen to all lawyer's conversations for new messages
            // NOTE: use a separate variable name (cid) to avoid shadowing the outer convId
            conversationIds.forEach(function(cid) {
                // Skip the active conversation — it already has its own dedicated listener above
                if (convId && cid === convId) return;

                window.Echo.private('conversation.' + cid)
                    .listen('.MessageSent', function(e) {
                        if (e.sender_id === currentUserId) return;

                        var currentActiveConvId = document.getElementById('convId')
                            ? parseInt(document.getElementById('convId').value) : 0;

                        if (parseInt(e.conversation_id) !== currentActiveConvId) {
                            updateLawyerConversationPreview(e.conversation_id, e.body, e.time);
                            moveConversationToTop(e.conversation_id);
                        }
                    });
            });
            
        } else {
            console.error('❌ Echo not available for global lawyer notifications');
        }
    }, 1500); // Wait a bit longer for Echo to fully initialize

    // Function to safely update conversation preview without moving elements
    function updateLawyerConversationPreview(conversationId, messageBody, messageTime) {
        console.log('📝 updateLawyerConversationPreview called for conversation:', conversationId);
        var conversationItems = document.querySelectorAll('.msg-conv-item');
        var updated = false;
        
        console.log('Found', conversationItems.length, 'conversation items to check');
        
        conversationItems.forEach(function(item) {
            var href = item.getAttribute('href');
            var match = href ? href.match(/[?&]conversation=(\d+)/) : null;
            var itemConvId = match ? parseInt(match[1]) : null;
            if (itemConvId === parseInt(conversationId)) {
                console.log('📝 Found matching conversation item, updating preview for conversation:', conversationId);
                
                // Update the preview text
                var previewElement = item.querySelector('.msg-conv-preview');
                if (previewElement) {
                    var oldText = previewElement.textContent;
                    previewElement.textContent = messageBody.length > 38 ? messageBody.substring(0, 38) + '...' : messageBody;
                    console.log('Updated preview text from "' + oldText + '" to "' + previewElement.textContent + '"');
                }
                
                // Update the time
                var timeElement = item.querySelector('.msg-conv-time');
                if (timeElement) {
                    var oldTime = timeElement.textContent;
                    timeElement.textContent = 'now';
                    console.log('Updated time from "' + oldTime + '" to "now"');
                }
                
                // Add or update unread badge
                var unreadBadge = item.querySelector('.msg-unread-badge');
                if (unreadBadge) {
                    var currentCount = parseInt(unreadBadge.textContent) || 0;
                    unreadBadge.textContent = currentCount + 1;
                    unreadBadge.style.display = '';
                    console.log('Updated unread badge to:', currentCount + 1);
                } else {
                    // Create new unread badge
                    var metaDiv = item.querySelector('.msg-conv-meta');
                    if (metaDiv) {
                        var newBadge = document.createElement('div');
                        newBadge.className = 'msg-unread-badge';
                        newBadge.textContent = '1';
                        metaDiv.appendChild(newBadge);
                        console.log('Created new unread badge with count: 1');
                    }
                }
                
                updated = true;
                return; // Exit forEach early since we found the conversation
            }
        });
        
        if (!updated) {
            console.log('⚠️ Could not find conversation', conversationId, 'to update preview');
        } else {
            console.log('✅ Successfully updated preview for conversation', conversationId);
        }
    }
})();
</script>

@endsection