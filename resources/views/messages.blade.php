 
@extends('layouts.app')
@section('title', 'Messages')
@section('body-class', '')
@section('content')
@section('hide-footer', true)
<noscript><meta http-equiv="refresh" content="5"></noscript>
<style>
.main-content { padding: 0; max-width: 100%; }
.msg-layout { display: flex; height: calc(100vh - 70px); background: #f5f7fa; overflow: hidden; border-radius: 12px; box-shadow: 0 2px 16px rgba(0,0,0,.07); }

/* Sidebar */
.msg-sidebar { width: 300px; flex-shrink: 0; background: #fff; display: flex; flex-direction: column; border-right: 1px solid #f0f2f5; }
.msg-sidebar-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 18px; border-bottom: 1px solid #f0f2f5; font-weight: 700; color: #1e2d4d; font-size: .95rem; }
.msg-count { background: #1e2d4d; color: #fff; border-radius: 20px; padding: 2px 9px; font-size: .75rem; font-weight: 700; }
.msg-conv-item { display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-bottom: 1px solid #f5f6fa; text-decoration: none; transition: background .15s; cursor: pointer; }
.msg-conv-item:hover { background: #f8f9fc; }
.msg-conv-item.active { background: #eef2ff; border-left: 3px solid #1e2d4d; }
.msg-conv-avatar { width: 44px; height: 44px; border-radius: 50%; background: #1e2d4d; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: .9rem; flex-shrink: 0; }
.msg-conv-info { flex: 1; min-width: 0; }
.msg-conv-name { font-size: .88rem; font-weight: 600; color: #1e2d4d; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.msg-conv-preview { font-size: .78rem; color: #9ca3af; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 2px; }
.msg-conv-meta { text-align: right; flex-shrink: 0; }
.msg-conv-time { font-size: .72rem; color: #c4c9d4; }
.msg-unread-badge { display: inline-flex; align-items: center; justify-content: center; background: #1e2d4d; color: #fff; border-radius: 12px; min-width: 20px; height: 20px; padding: 0 6px; font-size: .68rem; font-weight: 700; margin-top: 4px; }

/* Chat */
.msg-chat { flex: 1; display: flex; flex-direction: column; min-width: 0; background: #fff; }
.msg-chat-header { display: flex; align-items: center; gap: 13px; padding: 14px 20px; border-bottom: 1px solid #f0f2f5; background: #fff; }
.msg-chat-avatar { width: 42px; height: 42px; border-radius: 50%; background: #1e2d4d; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: .9rem; flex-shrink: 0; }
.msg-chat-avatar img { width: 42px; height: 42px; border-radius: 50%; object-fit: cover; }
.msg-chat-name { font-size: .97rem; font-weight: 700; color: #1e2d4d; }
.msg-chat-status { font-size: .75rem; color: #6b7280; display: flex; align-items: center; gap: 5px; margin-top: 2px; }

/* Bubbles */
.msg-bubbles { flex: 1; overflow-y: auto; padding: 20px 24px; display: flex; flex-direction: column; gap: 6px; background: #f5f7fa; }
.msg-bubbles::-webkit-scrollbar { width: 4px; }
.msg-bubbles::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
.msg-bubble-wrap { display: flex; }
.msg-bubble-wrap.mine { justify-content: flex-end; }
.msg-bubble-wrap.theirs { justify-content: flex-start; }
.msg-bubble { max-width: 62%; }
.msg-bubble-text { padding: 10px 14px; border-radius: 18px; font-size: .88rem; line-height: 1.55; word-break: break-word; }
.msg-bubble.mine   .msg-bubble-text { background: #1e2d4d; color: #fff; border-bottom-right-radius: 4px; }
.msg-bubble.theirs .msg-bubble-text { background: #fff; color: #1e2d4d; border-bottom-left-radius: 4px; box-shadow: 0 1px 4px rgba(0,0,0,.07); }
.msg-bubble-time { font-size: .68rem; color: #b0b7c3; margin-top: 4px; }
.msg-bubble.mine   .msg-bubble-time { text-align: right; }
.msg-bubble.theirs .msg-bubble-time { text-align: left; }

/* Attachment in bubble */
.msg-bubble-img { max-width: 220px; border-radius: 12px; display: block; margin-top: 6px; cursor: pointer; }
.msg-bubble-file { display: inline-flex; align-items: center; gap: 7px; padding: 7px 12px; border-radius: 10px; margin-top: 6px; font-size: .82rem; text-decoration: none; }
.msg-bubble.mine   .msg-bubble-file { background: rgba(255,255,255,.2); color: #fff; }
.msg-bubble.theirs .msg-bubble-file { background: #f0f2f5; color: #1e2d4d; }

/* Input */
.msg-form-wrap { padding: 12px 16px; border-top: 1px solid #f0f2f5; background: #fff; display: flex; flex-direction: column; gap: 8px; }
.msg-upload-preview { display: flex; flex-wrap: wrap; gap: 8px; }
.msg-upload-item { position: relative; }
.msg-upload-item img { width: 56px; height: 56px; object-fit: cover; border-radius: 8px; border: 1.5px solid #e5e7eb; }
.msg-upload-item .rm { position: absolute; top: -6px; right: -6px; width: 18px; height: 18px; background: #dc3545; border-radius: 50%; color: #fff; font-size: .55rem; display: flex; align-items: center; justify-content: center; cursor: pointer; border: none; }
.msg-upload-item .file-chip { display: flex; align-items: center; gap: 5px; background: #f0f2f5; border-radius: 8px; padding: 6px 10px; font-size: .75rem; color: #1e2d4d; max-width: 130px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
.msg-form { display: flex; align-items: center; gap: 10px; }
.msg-attach-btn { background: none; border: none; color: #9ca3af; font-size: 1rem; cursor: pointer; padding: 4px 6px; transition: color .2s; flex-shrink: 0; }
.msg-attach-btn:hover { color: #1e2d4d; }
.msg-input { flex: 1; padding: 11px 18px; border: 1.5px solid #e5e7eb; border-radius: 24px; font-size: .88rem; font-family: inherit; outline: none; background: #f5f7fa; color: #1e2d4d; transition: border-color .2s, background .2s; }
.msg-input:focus { border-color: #1e2d4d; background: #fff; }
.msg-input::placeholder { color: #9ca3af; }
.msg-send-btn { width: 42px; height: 42px; background: #1e2d4d; border-radius: 50%; border: none; color: #fff; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: .9rem; flex-shrink: 0; transition: background .2s, transform .1s; box-shadow: 0 2px 8px rgba(30,45,77,.25); }
.msg-send-btn:hover { background: #2d4a7a; transform: scale(1.05); }

.msg-chat-empty { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #c4c9d4; gap: 10px; }
.msg-chat-empty i { font-size: 3rem; }
.msg-chat-empty h3 { margin: 0; font-size: 1rem; color: #9ca3af; }
.msg-chat-empty p { margin: 0; font-size: .85rem; }

/* Lightbox */
#imgLightbox { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.85); z-index: 9999; align-items: center; justify-content: center; }
#imgLightbox.open { display: flex; }
#imgLightbox img { max-width: 90vw; max-height: 90vh; border-radius: 8px; }
</style>

<div class="msg-layout">
    {{-- Sidebar --}}
    <div class="msg-sidebar">
        <div class="msg-sidebar-header">
            <span><i class="fas fa-comment-dots"></i> Conversations</span>
            <span class="msg-count">{{ $conversations->count() }}</span>
        </div>
        @livewire('client.message-search', ['activeConv' => $activeConv])
        <div id="convNoResults" style="display:none;padding:24px;text-align:center;color:#aaa;font-size:.85rem;">No results found</div>
    </div>

    {{-- Chat --}}
    <div class="msg-chat">
        @if($activeConv)
        @php
            $other  = auth()->id() === $activeConv->client_id ? $activeConv->lawyer : $activeConv->client;
            $status = $other->lawyerProfile->availability_status ?? 'offline';
        @endphp
        <div class="msg-chat-header">
            <div class="msg-chat-avatar">
                @if($other && $other->avatar_url)
                    <img src="{{ $other->avatar_url }}" alt="">
                @else
                    {{ $other ? strtoupper(substr($other->name,0,1)) : '?' }}
                @endif
            </div>
            <div>
                <div class="msg-chat-name">{{ $other ? $other->name : 'Unknown' }}</div>
                <div class="msg-chat-status">
                    <i class="fas fa-circle" id="presenceDot" style="font-size:.45rem;color:{{ $status==='available'?'#22c55e':($status==='busy'?'#f59e0b':'#d1d5db') }};"></i>
                    <span id="presenceText">{{ $status==='available'?'Available':($status==='busy'?'Busy':'Offline') }}</span>
                </div>
            </div>
        </div>

        <div class="msg-bubbles" id="bubbles">
            @foreach($messages as $m)
            @php $mine = $m->sender_id === auth()->id(); @endphp
            <div class="msg-bubble-wrap {{ $mine ? 'mine' : 'theirs' }}">
                <div class="msg-bubble {{ $mine ? 'mine' : 'theirs' }}">
                    @if($m->body)<div class="msg-bubble-text">{{ $m->body }}</div>@endif
                    @if($m->attachment_path)
                        @if($m->attachment_type === 'image')
                            <img src="{{ asset('storage/'.$m->attachment_path) }}" class="msg-bubble-img" onclick="openLightbox(this.src)" alt="{{ $m->attachment_name }}">
                        @else
                            <a href="{{ asset('storage/'.$m->attachment_path) }}" class="msg-bubble-file" target="_blank" download="{{ $m->attachment_name }}">
                                <i class="fas fa-file-alt"></i> {{ $m->attachment_name }}
                            </a>
                        @endif
                    @endif
                    <div class="msg-bubble-time">{{ $m->created_at->format('g:i A') }}</div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="msg-form-wrap">
            <div class="msg-upload-preview" id="uploadPreview" style="display:none;"></div>
            <div class="msg-form">
                <input type="file" id="fileInput" accept="image/*,.pdf,.doc,.docx,.txt" style="display:none;">
                <button type="button" class="msg-attach-btn" onclick="document.getElementById('fileInput').click()" title="Attach file">
                    <i class="fas fa-paperclip"></i>
                </button>
                <input type="hidden" id="convId" value="{{ $activeConv->id }}">
                <input type="text" id="msgInput" class="msg-input" placeholder="Type a message..." autocomplete="off">
                <button type="button" id="sendBtn" class="msg-send-btn"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>

        @else
        <div class="msg-chat-empty">
            <i class="fas fa-comment-dots"></i>
            <h3>Select a conversation</h3>
            <p>Choose a lawyer to start chatting</p>
        </div>
        @endif
    </div>
</div>

<div id="imgLightbox" onclick="this.classList.remove('open')">
    <img id="lightboxImg" src="" alt="">
</div>

<script>
(function(){
    var bubbles = document.getElementById('bubbles');
    if (bubbles) bubbles.scrollTop = bubbles.scrollHeight;

    // Search
    var searchInput = document.getElementById('convSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function(){
            var q = this.value.toLowerCase();
            var items = document.querySelectorAll('.msg-conv-item');
            var visible = 0;
            items.forEach(function(el){
                var name    = (el.querySelector('.msg-conv-name')||{}).textContent||'';
                var preview = (el.querySelector('.msg-conv-preview')||{}).textContent||'';
                var show    = (name+preview).toLowerCase().indexOf(q) !== -1;
                el.style.display = show ? '' : 'none';
                if (show) visible++;
            });
            var noRes = document.getElementById('convNoResults');
            if (noRes) noRes.style.display = (visible===0 && q.length>0) ? 'block' : 'none';
        });
    }

    window.openLightbox = function(src) {
        document.getElementById('lightboxImg').src = src;
        document.getElementById('imgLightbox').classList.add('open');
    };

    var currentUserId = {{ auth()->id() }};
    var convId   = document.getElementById('convId') ? parseInt(document.getElementById('convId').value) : null;
    var msgInput = document.getElementById('msgInput');
    var sendBtn  = document.getElementById('sendBtn');
    var csrfToken= (document.querySelector('meta[name="csrf-token"]')||{getAttribute:function(){return '';}}).getAttribute('content');

    // File handling
    var selectedFile = null;
    var fileInput    = document.getElementById('fileInput');
    var uploadPreview= document.getElementById('uploadPreview');

    if (fileInput) {
        fileInput.addEventListener('change', function(){ selectedFile = this.files[0]||null; renderPreview(); });
    }

    function escHtml(t){ return (t||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

    function renderPreview() {
        if (!uploadPreview) return;
        if (!selectedFile) { uploadPreview.style.display='none'; uploadPreview.innerHTML=''; return; }
        uploadPreview.style.display = 'flex';
        var item = document.createElement('div');
        item.className = 'msg-upload-item';
        if (selectedFile.type.startsWith('image/')) {
            var reader = new FileReader();
            reader.onload = function(e){
                item.innerHTML = '<img src="'+e.target.result+'"><button class="rm" onclick="clearFile()"><i class="fas fa-times"></i></button>';
            };
            reader.readAsDataURL(selectedFile);
        } else {
            item.innerHTML = '<div class="file-chip"><i class="fas fa-file"></i>'+escHtml(selectedFile.name)+'</div><button class="rm" onclick="clearFile()"><i class="fas fa-times"></i></button>';
        }
        uploadPreview.innerHTML = '';
        uploadPreview.appendChild(item);
    }

    window.clearFile = function(){ selectedFile=null; if(fileInput) fileInput.value=''; renderPreview(); };

    function appendBubble(body, time, mine, attachUrl, attachName, attachType) {
        if (!bubbles) return;
        var empty = bubbles.querySelector('.msg-chat-empty');
        if (empty) empty.remove();

        var wrap = document.createElement('div');
        wrap.className = 'msg-bubble-wrap ' + (mine ? 'mine' : 'theirs');
        var inner = document.createElement('div');
        inner.className = 'msg-bubble ' + (mine ? 'mine' : 'theirs');

        if (body) {
            var txt = document.createElement('div');
            txt.className = 'msg-bubble-text';
            txt.textContent = body;
            inner.appendChild(txt);
        }
        if (attachUrl) {
            if (attachType === 'image') {
                var img = document.createElement('img');
                img.src = attachUrl; img.className = 'msg-bubble-img';
                img.onclick = function(){ openLightbox(this.src); };
                inner.appendChild(img);
            } else {
                var a = document.createElement('a');
                a.href = attachUrl; a.className = 'msg-bubble-file';
                a.target = '_blank'; a.download = attachName||'';
                a.innerHTML = '<i class="fas fa-file-alt"></i> '+escHtml(attachName||'File');
                inner.appendChild(a);
            }
        }
        var t = document.createElement('div');
        t.className = 'msg-bubble-time'; t.textContent = time;
        inner.appendChild(t);
        wrap.appendChild(inner);
        bubbles.appendChild(wrap);
        bubbles.scrollTop = bubbles.scrollHeight;
    }

    function doSend() {
        var body = msgInput ? msgInput.value.trim() : '';
        if (!body && !selectedFile) return;
        var time = new Date().toLocaleTimeString([],{hour:'numeric',minute:'2-digit'});
        var fd = new FormData();
        fd.append('conversation_id', convId);
        fd.append('body', body);
        if (selectedFile) fd.append('attachment', selectedFile);

        appendBubble(body, time, true,
            selectedFile ? (selectedFile.type.startsWith('image/') ? URL.createObjectURL(selectedFile) : '#') : null,
            selectedFile ? selectedFile.name : null,
            selectedFile ? (selectedFile.type.startsWith('image/') ? 'image' : 'file') : null
        );
        if (msgInput) msgInput.value = '';
        clearFile();

        fetch('{{ route("messages.send") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: fd,
        }).catch(function(){});
    }

    if (sendBtn) sendBtn.addEventListener('click', doSend);
    if (msgInput) msgInput.addEventListener('keydown', function(e){ if(e.key==='Enter'&&!e.shiftKey){e.preventDefault();doSend();} });

    // WebSocket
    if (convId) {
        setTimeout(function(){
            if (!window.Echo) return;
            var dot  = document.getElementById('presenceDot');
            var text = document.getElementById('presenceText');
            function setOnline(on) {
                if (!dot||!text) return;
                dot.style.color = on ? '#22c55e' : '#d1d5db';
                text.textContent = on ? 'Available' : 'Offline';
            }
            window.Echo.private('conversation.'+convId).listen('.MessageSent', function(e){
                if (e.sender_id !== currentUserId)
                    appendBubble(e.body, e.time, false, e.attachment_path, e.attachment_name, e.attachment_type);
            });
            if (convId) {
                window.Echo.join('presence-conversation.' + convId)
                    .here(function(users){
                        setOnline(users.some(function(user){ return user.id !== currentUserId; }));
                    })
                    .joining(function(user){
                        if (user.id !== currentUserId) setOnline(true);
                    })
                    .leaving(function(user){
                        if (user.id !== currentUserId) setOnline(false);
                    });
            }
        }, 1000);

        // Global listeners
        setTimeout(function(){
            if (!window.Echo) return;
            @php $cids = Auth::check() ? \App\Models\Conversation::where('client_id',Auth::id())->pluck('id')->toArray() : []; @endphp
            var cids = @json($cids);
            cids.forEach(function(cid){
                if (cid === convId) return;
                window.Echo.private('conversation.'+cid).listen('.MessageSent', function(e){
                    if (e.sender_id === currentUserId) return;
                    var item = document.querySelector('.msg-conv-item[data-id="'+e.conversation_id+'"]');
                    if (!item) return;
                    var prev = item.querySelector('.msg-conv-preview');
                    if (prev) prev.textContent = e.body || '📎 Attachment';
                    var badge = item.querySelector('.msg-unread-badge');
                    if (badge) { badge.textContent = parseInt(badge.textContent||0)+1; }
                    else {
                        var meta = item.querySelector('.msg-conv-meta');
                        if (meta) { var b=document.createElement('div'); b.className='msg-unread-badge'; b.textContent='1'; meta.appendChild(b); }
                    }
                    var sidebar = document.querySelector('.msg-sidebar');
                    var first = sidebar.querySelector('.msg-conv-item');
                    if (first && first !== item) sidebar.insertBefore(item, first);
                });
            });
        }, 1500);
    }
})();
</script>
@endsection
