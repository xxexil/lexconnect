@extends('layouts.lawfirm')
@section('title', 'Messages')
@section('content')
@push('styles')
<style>
.lf-content { max-width: none; }
.msg-layout { width: 100%; }
.msg-layout, .msg-chat, .msg-bubbles, .msg-bubble-wrap { min-width: 0; max-width: 100%; }
.msg-sidebar { height: calc(100vh - 200px); overflow: hidden; }
.msg-conv-list { flex: 1; overflow-y: auto; overflow-x: hidden; min-height: 0; }
.msg-conv-list::-webkit-scrollbar { width: 4px; }
.msg-conv-list::-webkit-scrollbar-track { background: transparent; }
.msg-conv-list::-webkit-scrollbar-thumb { background: rgba(255,255,255,.25); border-radius: 4px; }
.msg-conv-list::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,.45); }
.msg-bubbles { overflow-x: hidden; }
.msg-bubble { width: fit-content; min-width: 0; overflow-wrap: anywhere; }
.msg-bubble-text { white-space: pre-wrap; overflow-wrap: anywhere; word-break: break-word; max-width: 100%; }
.msg-bubble-img { width: auto; max-width: min(220px, 100%); max-height: 260px; height: auto; object-fit: contain; border-radius: 12px; display: block; margin-top: 6px; cursor: pointer; }
.msg-bubble.msg-bubble-media-only { background: transparent !important; box-shadow: none !important; padding: 0 !important; }
.msg-bubble-file { display: inline-flex; align-items: center; gap: 7px; max-width: 100%; padding: 7px 12px; border-radius: 10px; margin-top: 6px; font-size: .82rem; text-decoration: none; white-space: normal; overflow-wrap: anywhere; word-break: break-word; }
.msg-bubble-album { margin-top: 8px; width: min(240px, 100%); max-width: 100%; }
.msg-bubble-album-stack { position: relative; width: min(200px, 100%); aspect-ratio: 4 / 3; height: auto; margin-left: 8px; cursor: pointer; }
.msg-bubble.mine .msg-bubble-album,
.msg-bubble.theirs .msg-bubble-album { background: transparent; }
.msg-bubble-album-layer,
.msg-bubble-album-cover { position: absolute; inset: 0; border-radius: 22px; box-shadow: 0 16px 36px rgba(15, 23, 42, .18); transform-origin: bottom center; }
.msg-bubble-album-layer { background: linear-gradient(180deg, #f8fafc, #e2e8f0); border: 1px solid rgba(148, 163, 184, .35); }
.msg-bubble-album-layer.layer-1 { transform: rotate(-8deg) translate(-10px, -8px); opacity: .95; }
.msg-bubble-album-layer.layer-2 { transform: rotate(6deg) translate(10px, -2px); opacity: .88; }
.msg-bubble-album-layer.layer-3 { transform: rotate(-3deg) translate(4px, -14px); opacity: .8; }
.msg-bubble-album-cover { width: 100%; height: 100%; object-fit: cover; border: 1px solid rgba(255,255,255,.6); background: #dbe4f0; }
.msg-bubble-album-meta { margin-top: 10px; font-size: .75rem; font-weight: 700; letter-spacing: .02em; text-transform: uppercase; opacity: .8; }
.msg-bubble.theirs .msg-bubble-album-meta,
.msg-bubble.mine .msg-bubble-album-meta { color: #475569; }
.msg-bubble-album-thumbs { display: none; }
.msg-bubble-files { display: flex; flex-direction: column; gap: 6px; margin-top: 6px; }
.msg-bubble.mine .msg-bubble-file { background: rgba(255,255,255,.2); color: #fff; }
.msg-bubble.theirs .msg-bubble-file { background: #f0f2f5; color: #1e2d4d; }
.msg-form-wrap { padding: 12px 16px; border-top: 1px solid #f0f2f5; background: #fff; display: flex; flex-direction: column; gap: 8px; }
.msg-upload-preview { display: flex; flex-wrap: wrap; gap: 8px; }
.msg-upload-item { position: relative; }
.msg-upload-item img { width: 56px; height: 56px; object-fit: cover; border-radius: 8px; border: 1.5px solid #e5e7eb; }
.msg-upload-item .rm { position: absolute; top: -6px; right: -6px; width: 18px; height: 18px; background: #dc3545; border-radius: 50%; color: #fff; font-size: .55rem; display: flex; align-items: center; justify-content: center; cursor: pointer; border: none; }
.msg-upload-item .file-chip { display: flex; align-items: center; gap: 5px; background: #f0f2f5; border-radius: 8px; padding: 6px 10px; font-size: .75rem; color: #1e2d4d; max-width: 130px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
.msg-attach-btn { background: none; border: none; color: #9ca3af; font-size: 1rem; cursor: pointer; padding: 4px 6px; transition: color .2s; flex-shrink: 0; }
.msg-attach-btn:hover { color: #1e2d4d; }
#imgLightbox { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.85); z-index: 9999; align-items: center; justify-content: center; }
#imgLightbox.open { display: flex; }
#imgLightbox img { max-width: 90vw; max-height: 90vh; border-radius: 8px; }
.msg-lightbox-nav { position: absolute; top: 50%; transform: translateY(-50%); width: 46px; height: 46px; border: none; border-radius: 999px; background: rgba(15, 23, 42, .72); color: #fff; font-size: 1rem; cursor: pointer; display: flex; align-items: center; justify-content: center; }
.msg-lightbox-nav.prev { left: 24px; }
.msg-lightbox-nav.next { right: 24px; }
.msg-lightbox-nav[disabled] { opacity: .35; cursor: default; }
</style>
@endpush

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
        <div style="padding:10px 14px 8px;border-bottom:1px solid #e5e7eb;">
            <div style="display:flex;align-items:center;gap:8px;background:#f8fafc;border:1px solid #d1d5db;border-radius:8px;padding:7px 12px;">
                <i class="fas fa-search" style="color:#64748b;font-size:.8rem;"></i>
                <input type="text" id="convSearch" placeholder="Search lawyers…" autocomplete="off"
                    style="border:none;background:transparent;outline:none;font-size:.87rem;color:#0f172a;width:100%;">
                <style>#convSearch::placeholder{color:#94a3b8;}</style>
            </div>
        </div>
        <div class="msg-conv-list">
        @forelse($conversations as $conv)
        @php
            $other       = $conv->lawyer;
            $latest      = $conv->latestMessage;
            $isActive    = $activeConv && $activeConv->id === $conv->id;
            $unreadCount = $conv->messages->filter(fn($m) => $m->sender_id !== $user->id && !$m->read_at)->count();
        @endphp
        <a href="{{ route('lawfirm.messages', ['conversation' => $conv->id]) }}"
           class="msg-conv-item {{ $isActive ? 'active' : '' }}">
            <div class="msg-conv-avatar" style="{{ $other->avatar_url ? 'padding:0;overflow:hidden;' : '' }}">
                @if($other->avatar_url)
                    <img src="{{ $other->avatar_url }}" style="width:100%;height:100%;object-fit:cover;display:block;border-radius:50%;" alt="{{ $other->name }}">
                @else
                    {{ strtoupper(substr($other->name, 0, 1)) }}
                @endif
            </div>
            <div class="msg-conv-info">
                <div class="msg-conv-name">{{ $other->name }}</div>
                <div class="msg-conv-preview">{{ $latest ? Str::limit($latest->body ?: ($latest->attachment_name ?: 'Attachment'), 38) : 'No messages yet' }}</div>
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
    </div>

    {{-- Chat Window --}}
    <div class="msg-chat">
        @if($activeConv)
        @php $other = $activeConv->lawyer; @endphp
        <div class="msg-chat-header">
            <div class="msg-chat-avatar" style="{{ $other->avatar_url ? 'padding:0;overflow:hidden;' : '' }}">
                @if($other->avatar_url)
                    <img src="{{ $other->avatar_url }}" style="width:100%;height:100%;object-fit:cover;display:block;border-radius:50%;" alt="{{ $other->name }}">
                @else
                    {{ strtoupper(substr($other->name, 0, 1)) }}
                @endif
            </div>
            <div>
                <div class="msg-chat-name">{{ $other->name }}</div>
                <div class="msg-chat-status">
                    <i class="fas fa-circle" id="presenceDot" style="font-size:.45rem;color:#d1d5db;"></i>
                    <span id="presenceText">Offline</span>
                </div>
            </div>
        </div>
        @php
            $messageGroups = [];
            foreach ($messages as $message) {
                $mine = $message->sender_id === $user->id;
                $lastIndex = count($messageGroups) - 1;
                $canMerge = $message->batch_uuid
                    && $lastIndex >= 0
                    && $messageGroups[$lastIndex]['batch_uuid'] === $message->batch_uuid
                    && $messageGroups[$lastIndex]['sender_id'] === $message->sender_id;

                if (!$canMerge) {
                    $messageGroups[] = [
                        'sender_id' => $message->sender_id,
                        'mine' => $mine,
                        'batch_uuid' => $message->batch_uuid,
                        'body' => $message->body,
                        'time' => $message->created_at->format('g:i A'),
                        'attachments' => [],
                    ];
                    $lastIndex = count($messageGroups) - 1;
                } elseif (!$messageGroups[$lastIndex]['body'] && $message->body) {
                    $messageGroups[$lastIndex]['body'] = $message->body;
                }

                $messageGroups[$lastIndex]['time'] = $message->created_at->format('g:i A');

                if ($message->attachment_path) {
                    $messageGroups[$lastIndex]['attachments'][] = [
                        'path' => asset('storage/' . $message->attachment_path),
                        'name' => $message->attachment_name,
                        'type' => $message->attachment_type,
                    ];
                }
            }
        @endphp
        <div class="msg-bubbles" id="bubbles">
            @foreach($messageGroups as $group)
            @php $imageAttachments = collect($group['attachments'])->where('type', 'image')->values(); @endphp
            @php $fileAttachments = collect($group['attachments'])->where('type', '!=', 'image')->values(); @endphp
            @php $isImageOnlyGroup = blank($group['body']) && $imageAttachments->count() > 0 && $fileAttachments->count() === 0; @endphp
            <div class="msg-bubble-wrap {{ $group['mine'] ? 'mine' : 'theirs' }}" data-batch-id="{{ $group['batch_uuid'] ?? '' }}" data-mine="{{ $group['mine'] ? 1 : 0 }}">
                <div class="msg-bubble {{ $group['mine'] ? 'mine' : 'theirs' }}{{ $isImageOnlyGroup ? ' msg-bubble-media-only' : '' }}">
                    @if($group['body'])<div class="msg-bubble-text">{{ $group['body'] }}</div>@endif
                    @if(count($group['attachments']) > 0)
                        @if($imageAttachments->count() === 1)
                            <img src="{{ $imageAttachments[0]['path'] }}" class="msg-bubble-img" style="display:block;width:auto;max-width:220px;max-height:260px;height:auto;object-fit:contain;" onclick="openLightboxFromElement(this)" alt="{{ $imageAttachments[0]['name'] }}">
                        @elseif($imageAttachments->count() > 1)
                            <div class="msg-bubble-album" style="width:240px;max-width:100%;" data-photo-count="{{ $imageAttachments->count() }}">
                                <div class="msg-bubble-album-stack" style="position:relative;width:200px;max-width:100%;height:150px;" onclick="openLightboxFromElement(this)" data-first-image="{{ $imageAttachments[0]['path'] }}">
                                    <span class="msg-bubble-album-layer layer-1" aria-hidden="true"></span>
                                    <span class="msg-bubble-album-layer layer-2" aria-hidden="true"></span>
                                    <span class="msg-bubble-album-layer layer-3" aria-hidden="true"></span>
                                    <img src="{{ $imageAttachments[0]['path'] }}" class="msg-bubble-album-cover" style="width:100%;height:100%;object-fit:cover;" alt="{{ $imageAttachments[0]['name'] }}">
                                </div>
                                <div class="msg-bubble-album-meta">{{ $imageAttachments->count() }} photos</div>
                                <div class="msg-bubble-album-thumbs">
                                    @foreach($imageAttachments as $attachment)
                                        <img src="{{ $attachment['path'] }}" alt="{{ $attachment['name'] }}">
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        @if($fileAttachments->count() > 0)
                            <div class="msg-bubble-files">
                                @foreach($fileAttachments as $attachment)
                                    <a href="{{ $attachment['path'] }}" class="msg-bubble-file" target="_blank" download="{{ $attachment['name'] }}">
                                        <i class="fas fa-file-alt"></i> {{ $attachment['name'] }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    @endif
                    <div class="msg-bubble-time">{{ $group['time'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="msg-form-wrap">
            <div class="msg-upload-preview" id="uploadPreview" style="display:none;"></div>
            <form id="sendForm" class="msg-form">
                @csrf
                <input type="file" id="fileInput" accept="image/*,.pdf,.doc,.docx,.txt" style="display:none;" multiple>
                <button type="button" class="msg-attach-btn" onclick="document.getElementById('fileInput').click()" title="Attach file">
                    <i class="fas fa-paperclip"></i>
                </button>
                <input type="hidden" name="conversation_id" id="convId" value="{{ $activeConv->id }}">
                <input type="text" name="body" id="msgInput" class="msg-input" placeholder="Type a message…" autocomplete="off">
                <button type="button" id="sendBtn" class="msg-send-btn"><i class="fas fa-paper-plane"></i></button>
            </form>
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

<div id="imgLightbox" onclick="closeLightbox()">
    <button type="button" class="msg-lightbox-nav prev" id="lightboxPrev" onclick="event.stopPropagation(); showPrevLightboxImage()">
        <i class="fas fa-chevron-left"></i>
    </button>
    <img id="lightboxImg" src="" alt="">
    <button type="button" class="msg-lightbox-nav next" id="lightboxNext" onclick="event.stopPropagation(); showNextLightboxImage()">
        <i class="fas fa-chevron-right"></i>
    </button>
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
    var sendBtn       = document.getElementById('sendBtn');
    var csrfToken     = document.querySelector('meta[name="csrf-token"]')
                            ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';
    var fileInput     = document.getElementById('fileInput');
    var uploadPreview = document.getElementById('uploadPreview');
    var selectedFiles = [];
    var isSending = false;

    var lightboxImages = [];
    var lightboxIndex = 0;

    function updateLightboxNav() {
        var prevBtn = document.getElementById('lightboxPrev');
        var nextBtn = document.getElementById('lightboxNext');
        var multiImage = lightboxImages.length > 1;
        if (prevBtn) {
            prevBtn.style.display = multiImage ? 'flex' : 'none';
            prevBtn.disabled = !multiImage || lightboxIndex === 0;
        }
        if (nextBtn) {
            nextBtn.style.display = multiImage ? 'flex' : 'none';
            nextBtn.disabled = !multiImage || lightboxIndex === lightboxImages.length - 1;
        }
    }

    function setLightboxImage(index) {
        if (!lightboxImages.length) return;
        lightboxIndex = index;
        document.getElementById('lightboxImg').src = lightboxImages[lightboxIndex];
        updateLightboxNav();
    }

    window.openLightbox = function(images, index) {
        lightboxImages = Array.isArray(images) && images.length ? images : [];
        lightboxIndex = typeof index === 'number' ? index : 0;
        if (!lightboxImages.length) return;
        setLightboxImage(lightboxIndex);
        document.getElementById('imgLightbox').classList.add('open');
    };

    window.openLightboxFromElement = function(element) {
        if (!element) return;
        var images = [];
        var index = 0;

        if (element.classList.contains('msg-bubble-album-stack')) {
            var album = element.closest('.msg-bubble-album');
            if (album) {
                images = Array.from(album.querySelectorAll('.msg-bubble-album-thumbs img')).map(function(img){ return img.src; });
            }
        } else if (element.tagName === 'IMG') {
            images = [element.src];
        }

        openLightbox(images, index);
    };

    window.showPrevLightboxImage = function() {
        if (lightboxIndex > 0) setLightboxImage(lightboxIndex - 1);
    };

    window.showNextLightboxImage = function() {
        if (lightboxIndex < lightboxImages.length - 1) setLightboxImage(lightboxIndex + 1);
    };

    window.closeLightbox = function() {
        document.getElementById('imgLightbox').classList.remove('open');
    };

    document.addEventListener('keydown', function(event) {
        var lightbox = document.getElementById('imgLightbox');
        if (!lightbox || !lightbox.classList.contains('open')) return;

        if (event.key === 'ArrowLeft') {
            event.preventDefault();
            showPrevLightboxImage();
        } else if (event.key === 'ArrowRight') {
            event.preventDefault();
            showNextLightboxImage();
        } else if (event.key === 'Escape') {
            event.preventDefault();
            closeLightbox();
        }
    });

    if (fileInput) {
        fileInput.addEventListener('change', function() {
            selectedFiles = Array.from(this.files || []);
            renderPreview();
        });
    }

    function appendBubbleMessage(message, mine) {
        if (!bubbles) {
            console.error('❌ bubbles element not found!');
            return;
        }
        var wrap = null;
        if (message.batch_uuid) {
            wrap = bubbles.querySelector('.msg-bubble-wrap[data-batch-id="' + message.batch_uuid + '"][data-mine="' + (mine ? 1 : 0) + '"]');
        }
        var bubble;
        if (!wrap) {
            wrap = document.createElement('div');
            wrap.className = 'msg-bubble-wrap ' + (mine ? 'mine' : 'theirs');
            wrap.setAttribute('data-batch-id', message.batch_uuid || '');
            wrap.setAttribute('data-mine', mine ? '1' : '0');
            bubble = document.createElement('div');
            bubble.className = 'msg-bubble ' + (mine ? 'mine' : 'theirs');
            wrap.appendChild(bubble);
            bubbles.appendChild(wrap);
        } else {
            bubble = wrap.querySelector('.msg-bubble');
        }

        if (message.body && !bubble.querySelector('.msg-bubble-text')) {
            bubble.innerHTML += '<div class="msg-bubble-text">' + escHtml(message.body) + '</div>';
        }
        if (message.attachment_path) {
            if (message.attachment_type === 'image') {
                var groupedAlbum = message.batch_uuid ? bubble.querySelector('.msg-bubble-album') : null;
                if (message.batch_uuid) {
                    if (!groupedAlbum) {
                        groupedAlbum = document.createElement('div');
                        groupedAlbum.className = 'msg-bubble-album';
                        groupedAlbum.setAttribute('data-photo-count', '0');
                        groupedAlbum.style.cssText = 'width:240px;max-width:100%;';
                        groupedAlbum.innerHTML = '<div class="msg-bubble-album-stack" style="position:relative;width:200px;max-width:100%;height:150px;"><span class="msg-bubble-album-layer layer-1" aria-hidden="true"></span><span class="msg-bubble-album-layer layer-2" aria-hidden="true"></span><span class="msg-bubble-album-layer layer-3" aria-hidden="true"></span><img class="msg-bubble-album-cover" style="width:100%;height:100%;object-fit:cover;" alt="Attachment"></div><div class="msg-bubble-album-meta">0 photos</div><div class="msg-bubble-album-thumbs"></div>';
                        bubble.appendChild(groupedAlbum);
                    }

                    var stack = groupedAlbum.querySelector('.msg-bubble-album-stack');
                    var cover = groupedAlbum.querySelector('.msg-bubble-album-cover');
                    var meta = groupedAlbum.querySelector('.msg-bubble-album-meta');
                    var thumbs = groupedAlbum.querySelector('.msg-bubble-album-thumbs');
                    var count = parseInt(groupedAlbum.getAttribute('data-photo-count') || '0', 10) + 1;

                    if (!stack.dataset.firstImage) {
                        stack.dataset.firstImage = message.attachment_path;
                        stack.onclick = function(){ openLightboxFromElement(this); };
                    }

                    if (!cover.getAttribute('src')) {
                        cover.src = message.attachment_path;
                        cover.alt = message.attachment_name || 'Attachment';
                    }

                    var thumb = document.createElement('img');
                    thumb.src = message.attachment_path;
                    thumb.alt = message.attachment_name || 'Attachment';
                    thumbs.appendChild(thumb);

                    groupedAlbum.setAttribute('data-photo-count', String(count));
                    meta.textContent = count + ' photo' + (count === 1 ? '' : 's');
                } else {
                    bubble.innerHTML += '<img src="' + message.attachment_path + '" class="msg-bubble-img" style="display:block;width:auto;max-width:220px;max-height:260px;height:auto;object-fit:contain;" alt="' + escHtml(message.attachment_name || 'Attachment') + '" onclick="openLightboxFromElement(this)">';
                }
            } else {
                var files = bubble.querySelector('.msg-bubble-files');
                if (!files) {
                    files = document.createElement('div');
                    files.className = 'msg-bubble-files';
                    bubble.appendChild(files);
                }
                files.innerHTML += '<a href="' + message.attachment_path + '" class="msg-bubble-file" target="_blank" download="' + escHtml(message.attachment_name || 'File') + '"><i class="fas fa-file-alt"></i> ' + escHtml(message.attachment_name || 'File') + '</a>';
            }
        }
        var hasText = !!bubble.querySelector('.msg-bubble-text');
        var hasFiles = !!bubble.querySelector('.msg-bubble-files');
        var hasImages = !!bubble.querySelector('.msg-bubble-album, .msg-bubble-img');
        bubble.classList.toggle('msg-bubble-media-only', hasImages && !hasText && !hasFiles);
        var timeNode = bubble.querySelector('.msg-bubble-time');
        if (!timeNode) {
            timeNode = document.createElement('div');
            timeNode.className = 'msg-bubble-time';
            bubble.appendChild(timeNode);
        }
        timeNode.textContent = message.time;
        bubbles.scrollTop = bubbles.scrollHeight;
        console.log('✅ Message bubble added to DOM');
    }

    function escHtml(text) {
        return text.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
                   .replace(/"/g,'&quot;').replace(/'/g,'&#39;');
    }

    function renderPreview() {
        if (!uploadPreview) return;
        if (!selectedFiles.length) {
            uploadPreview.style.display = 'none';
            uploadPreview.innerHTML = '';
            return;
        }

        uploadPreview.style.display = 'flex';
        uploadPreview.innerHTML = '';

        selectedFiles.forEach(function(file, index) {
            var item = document.createElement('div');
            item.className = 'msg-upload-item';

            if (file.type.startsWith('image/')) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    item.innerHTML = '<img src="' + e.target.result + '"><button type="button" class="rm" onclick="removeSelectedFile(' + index + ')"><i class="fas fa-times"></i></button>';
                };
                reader.readAsDataURL(file);
            } else {
                item.innerHTML = '<div class="file-chip"><i class="fas fa-file"></i>' + escHtml(file.name) + '</div><button type="button" class="rm" onclick="removeSelectedFile(' + index + ')"><i class="fas fa-times"></i></button>';
            }

            uploadPreview.appendChild(item);
        });
    }

    window.clearFile = function() {
        selectedFiles = [];
        if (fileInput) fileInput.value = '';
        renderPreview();
    };
    window.removeSelectedFile = function(index) {
        selectedFiles = selectedFiles.filter(function(_, i) { return i !== index; });
        renderPreview();
    };

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

    function doSend() {
        if (isSending) return;
        var body = msgInput ? msgInput.value.trim() : '';
        if (!body && !selectedFiles.length) return;
        isSending = true;
        if (sendBtn) sendBtn.disabled = true;

        var formData = new FormData();
        formData.append('conversation_id', convId);
        formData.append('body', body);
        selectedFiles.forEach(function(file) {
            formData.append('attachments[]', file);
        });

        fetch('{{ route("lawfirm.messages.send") }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: formData,
        })
        .then(function(r){ if (!r.ok) throw new Error('err'); return r.json(); })
        .then(function(data){
            var messages = Array.isArray(data.messages) ? data.messages : [];
            messages.forEach(function(message) {
                appendBubbleMessage(message, true);
            });
            if (msgInput) msgInput.value = '';
            clearFile();
        })
        .catch(function(){
            window.alert('Message failed to send. Please try again.');
        })
        .finally(function(){
            isSending = false;
            if (sendBtn) sendBtn.disabled = false;
        });
    }

    if (sendForm && convId) {
        sendForm.addEventListener('submit', function(e) {
            e.preventDefault();
            doSend();
        });
        if (sendBtn) sendBtn.addEventListener('click', doSend);
        if (msgInput) msgInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                doSend();
            }
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
                            try {
                                appendBubbleMessage(e, false);
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
                            updateLawFirmConversationPreview(e.conversation_id, e.body || 'Attachment', e.time);
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
