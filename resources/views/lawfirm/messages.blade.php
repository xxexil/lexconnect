@extends('layouts.lawfirm')
@section('title', 'Messages')
@section('content')

<div class="lp-page-header">
    <div>
        <h1 class="lp-page-title">Messages</h1>
        <p class="lp-page-sub">Communicate with your lawyers</p>
    </div>
</div>

<div class="msg-layout">
    {{-- Conversations Sidebar --}}
    <div class="msg-sidebar">
        <div class="msg-sidebar-header">
            <span><i class="fas fa-comment-dots"></i> Conversations</span>
            <span class="msg-count">{{ $conversations->count() }}</span>
        </div>
        <div style="padding:10px 14px 8px;border-bottom:1px solid rgba(255,255,255,.08);">
            <div style="display:flex;align-items:center;gap:8px;background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);border-radius:8px;padding:7px 12px;">
                <i class="fas fa-search" style="color:rgba(255,255,255,.35);font-size:.8rem;"></i>
                <input type="text" id="convSearch" placeholder="Search lawyers…" autocomplete="off"
                    style="border:none;background:transparent;outline:none;font-size:.87rem;color:#fff;width:100%;">
                <style>#convSearch::placeholder{color:rgba(255,255,255,.35);}</style>
            </div>
        </div>
        @forelse($conversations as $conv)
        @php
            $other       = $conv->lawyer;
            $latest      = $conv->latestMessage;
            $isActive    = $activeConv && $activeConv->id === $conv->id;
            $unreadCount = $conv->messages->filter(fn($m) => $m->sender_id !== $user->id && !$m->read_at)->count();
        @endphp
        <a href="{{ route('lawfirm.messages', ['conversation' => $conv->id]) }}"
           class="msg-conv-item {{ $isActive ? 'active' : '' }}">
            <div class="msg-conv-avatar">{{ strtoupper(substr($other->name, 0, 1)) }}</div>
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
        <div style="padding:24px;text-align:center;color:rgba(255,255,255,.45);font-size:.88rem;">
            <i class="fas fa-comment-slash" style="font-size:2rem;margin-bottom:8px;display:block;"></i>
            No conversations yet.<br>
            <span style="font-size:.8rem;">Message a lawyer from the Team &amp; Applications page.</span>
        </div>
        @endforelse
        <div id="convNoResults" style="display:none;padding:24px;text-align:center;color:rgba(255,255,255,.45);font-size:.85rem;">
            <i class="fas fa-search" style="display:block;font-size:1.4rem;margin-bottom:6px;"></i>
            No results found
        </div>
    </div>

    {{-- Chat Window --}}
    <div class="msg-chat">
        @if($activeConv)
        @php $other = $activeConv->lawyer; @endphp
        <div class="msg-chat-header">
            <div class="msg-chat-avatar">{{ strtoupper(substr($other->name, 0, 1)) }}</div>
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
            <input type="text" name="body" id="msgInput" class="msg-input" placeholder="Type a message…" autocomplete="off" required>
            <button type="submit" class="msg-send-btn"><i class="fas fa-paper-plane"></i></button>
        </form>
        @else
        <div class="msg-chat-empty">
            <i class="fas fa-comment-dots"></i>
            <h3>Select a conversation</h3>
            <p>Choose a lawyer to start chatting</p>
        </div>
        @endif
    </div>
</div>

<script>
(function(){
    var bubbles = document.getElementById('bubbles');
    if (bubbles) bubbles.scrollTop = bubbles.scrollHeight;

    var input     = document.getElementById('convSearch');
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

    var currentUserId = {{ auth()->id() }};
    var convId        = document.getElementById('convId') ? parseInt(document.getElementById('convId').value) : null;
    var sendForm      = document.getElementById('sendForm');
    var msgInput      = document.getElementById('msgInput');
    var csrfToken     = document.querySelector('meta[name="csrf-token"]')
                            ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';

    function appendBubble(body, time, mine) {
        console.log('📝 appendBubble called with:', {body, time, mine, bubblesExists: !!bubbles});
        if (!bubbles) {
            console.error('❌ bubbles element not found!');
            return;
        }
        var wrap = document.createElement('div');
        wrap.className = 'msg-bubble-wrap ' + (mine ? 'mine' : 'theirs');
        var bubble = document.createElement('div');
        bubble.className = 'msg-bubble ' + (mine ? 'mine' : 'theirs');
        bubble.innerHTML = '<div class="msg-bubble-text">' + escHtml(body) + '</div>'
                         + '<div class="msg-bubble-time">' + time + '</div>';
        wrap.appendChild(bubble);
        bubbles.appendChild(wrap);
        bubbles.scrollTop = bubbles.scrollHeight;
        console.log('✅ Message bubble added to DOM');
    }

    function escHtml(text) {
        return text.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
                   .replace(/"/g,'&quot;').replace(/'/g,'&#39;');
    }

    // Clean up any duplicate conversations that might exist
    function removeDuplicateConversations() {
        var seenConversations = new Set();
        var conversationItems = document.querySelectorAll('.msg-conv-item');
        
        conversationItems.forEach(function(item) {
            var href = item.getAttribute('href');
            if (href) {
                var match = href.match(/conversation=(\d+)/);
                if (match) {
                    var conversationId = match[1];
                    if (seenConversations.has(conversationId)) {
                        console.log('🗑️ Removing duplicate conversation:', conversationId);
                        item.remove();
                    } else {
                        seenConversations.add(conversationId);
                    }
                }
            }
        });
    }

    // Clean up duplicates on page load
    removeDuplicateConversations();

    // Debounce mechanism to prevent rapid successive moves
    var moveTimeouts = {};
    
    // Move conversation to top of list when new message arrives
    function moveConversationToTop(conversationId) {
        // Don't move the currently active conversation - it's already being handled
        if (convId && conversationId == convId) {
            console.log('⏭️ Skipping move for active conversation:', conversationId);
            return;
        }
        
        // Clear any existing timeout for this conversation
        if (moveTimeouts[conversationId]) {
            clearTimeout(moveTimeouts[conversationId]);
        }
        
        // Debounce the move operation
        moveTimeouts[conversationId] = setTimeout(function() {
            console.log('📋 Moving conversation', conversationId, 'to top of list');
            
            var conversationItems = document.querySelectorAll('.msg-conv-item');
            var targetConversation = null;
            
            conversationItems.forEach(function(item) {
                var href = item.getAttribute('href');
                if (href && href.includes('conversation=' + conversationId)) {
                    // Make sure we don't have duplicates - take the first one found
                    if (!targetConversation) {
                        targetConversation = item;
                    }
                }
            });
            
            if (targetConversation) {
                var sidebar = document.querySelector('.msg-sidebar');
                var firstConversation = sidebar.querySelector('.msg-conv-item');
                
                // Only move if it's not already at the top and not the active conversation
                if (firstConversation && targetConversation !== firstConversation && !targetConversation.classList.contains('active')) {
                    console.log('📋 Moving conversation', conversationId, 'to top position');
                    
                    // Find the insertion point (after header elements)
                    var headerElements = sidebar.querySelectorAll('.msg-sidebar-header, div[style*="padding:10px"]');
                    var insertAfter = headerElements[headerElements.length - 1];
                    
                    // Use insertAdjacentElement to safely move the element
                    if (insertAfter) {
                        insertAfter.insertAdjacentElement('afterend', targetConversation);
                    } else {
                        // Fallback: prepend to sidebar
                        sidebar.prepend(targetConversation);
                    }
                    
                    console.log('✅ Conversation moved to top successfully');
                } else {
                    console.log('⏭️ Conversation already at top, is active, or no other conversations');
                }
            } else {
                console.log('❌ Target conversation not found in sidebar');
            }
            
            // Clean up the timeout
            delete moveTimeouts[conversationId];
        }, 100); // 100ms debounce
    }

    if (sendForm && convId) {
        sendForm.addEventListener('submit', function(e) {
            e.preventDefault();
            var body = msgInput.value.trim();
            if (!body) return;
            msgInput.value = '';
            appendBubble(body, new Date().toLocaleTimeString([], {hour:'numeric',minute:'2-digit'}), true);

            fetch('{{ route("lawfirm.messages.send") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ conversation_id: convId, body: body }),
            })
            .then(function(r){ if (!r.ok) throw new Error('err'); return r.json(); })
            .catch(function(){});
        });

        // Add delay to ensure Echo is properly initialized
        setTimeout(function() {
            if (window.Echo) {
                console.log('Setting up WebSocket listeners for conversation:', convId);
                
                var dot  = document.getElementById('presenceDot');
                var text = document.getElementById('presenceText');
                function setOnline(isOnline) {
                    if (!dot || !text) return;
                    dot.style.color  = isOnline ? '#22c55e' : '#d1d5db';
                    text.textContent = isOnline ? 'Online' : 'Offline';
                }

                // Listen for messages on private channel
                console.log('Subscribing to private channel: conversation.' + convId);
                window.Echo.private('conversation.' + convId)
                    .listen('.MessageSent', function(e){
                        console.log('🎉 RECEIVED MessageSent event:', e);
                        console.log('Current user ID:', currentUserId, 'Sender ID:', e.sender_id);
                        if (e.sender_id !== currentUserId) {
                            console.log('📝 Calling appendBubble with:', e.body, e.time, false);
                            try {
                                appendBubble(e.body, e.time, false);
                                console.log('✅ appendBubble completed successfully');
                                
                                // Don't move the active conversation - it's already at the top and active
                                console.log('⏭️ Skipping move for active conversation');
                            } catch (error) {
                                console.error('❌ Error in appendBubble:', error);
                            }
                        } else {
                            console.log('⏭️ Skipping message from current user');
                        }
                    })
                    .error(function(error) {
                        console.error('❌ Error on private channel:', error);
                    });

                // Track online presence on presence channel
                console.log('Joining presence channel: presence-conversation.' + convId);
                window.Echo.join('presence-conversation.' + convId)
                    .here(function(users){ 
                        console.log('Users currently here:', users);
                        setOnline(users.some(function(u){ return u.id !== currentUserId; })); 
                    })
                    .joining(function(user){ 
                        console.log('User joining:', user);
                        if (user.id !== currentUserId) setOnline(true); 
                    })
                    .leaving(function(user){ 
                        console.log('User leaving:', user);
                        if (user.id !== currentUserId) setOnline(false); 
                    })
                    .error(function(error) {
                        console.error('❌ Error on presence channel:', error);
                    });
            } else {
                console.error('Echo not available - WebSocket not initialized');
            }
        }, 1000); // 1 second delay
    }

    // Global real-time message notifications for all law firm conversations
    setTimeout(function() {
        if (window.Echo) {
            console.log('🌐 Setting up global law firm message notifications for user:', currentUserId);
            
            // Get law firm's conversation IDs
            @php
                $lawFirmConversationIds = Auth::check() 
                    ? \App\Models\Conversation::where('client_id', Auth::id())->pluck('id')->toArray()
                    : [];
            @endphp
            var allConversationIds = @json($lawFirmConversationIds);
            
            console.log('👥 Law firm conversations:', allConversationIds);
            
            // Listen to all law firm's conversations for new messages
            allConversationIds.forEach(function(globalConvId) {
                // Skip the current conversation if we're already listening to it
                if (convId && globalConvId == convId) {
                    console.log('⏭️ Skipping current conversation:', globalConvId);
                    return;
                }
                
                console.log('🔔 Setting up global notification listener for conversation:', globalConvId);
                
                window.Echo.private('conversation.' + globalConvId)
                    .listen('.MessageSent', function(e) {
                        console.log('🔔 Global law firm notification - received message:', e);
                        console.log('Message sender ID:', e.sender_id, 'Current law firm ID:', currentUserId);
                        
                        // Only show notification if message is from someone else
                        if (e.sender_id !== currentUserId) {
                            console.log('📬 New message from other user in law firm conversation:', e.conversation_id);
                            
                            // Update conversation preview and move to top
                            updateLawFirmConversationPreview(e.conversation_id, e.body, e.time);
                            moveConversationToTop(e.conversation_id);
                        } else {
                            console.log('⏭️ Ignoring message from self (law firm)');
                        }
                    })
                    .error(function(error) {
                        console.error('❌ Error setting up global law firm listener for conversation', globalConvId, ':', error);
                    });
            });
            
        } else {
            console.error('❌ Echo not available for global law firm notifications');
        }
    }, 1500); // Wait a bit longer for Echo to fully initialize

    // Function to update conversation preview in the law firm sidebar
    function updateLawFirmConversationPreview(conversationId, messageBody, messageTime) {
        var conversationItems = document.querySelectorAll('.msg-conv-item');
        
        conversationItems.forEach(function(item) {
            var href = item.getAttribute('href');
            if (href && href.includes('conversation=' + conversationId)) {
                console.log('📝 Updating law firm conversation preview for conversation:', conversationId);
                
                // Update the preview text
                var previewElement = item.querySelector('.msg-conv-preview');
                if (previewElement) {
                    previewElement.textContent = messageBody.length > 38 ? messageBody.substring(0, 38) + '...' : messageBody;
                }
                
                // Update the time
                var timeElement = item.querySelector('.msg-conv-time');
                if (timeElement) {
                    timeElement.textContent = 'now';
                }
                
                // Add or update unread badge
                var unreadBadge = item.querySelector('.msg-unread-badge');
                if (unreadBadge) {
                    var currentCount = parseInt(unreadBadge.textContent) || 0;
                    unreadBadge.textContent = currentCount + 1;
                    unreadBadge.style.display = '';
                } else {
                    // Create new unread badge
                    var metaDiv = item.querySelector('.msg-conv-meta');
                    if (metaDiv) {
                        var newBadge = document.createElement('div');
                        newBadge.className = 'msg-unread-badge';
                        newBadge.textContent = '1';
                        metaDiv.appendChild(newBadge);
                    }
                }
            }
        });
    }
})();
</script>

@endsection
