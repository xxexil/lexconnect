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
.msg-bubbles { overflow-x: visible; padding-left: 56px; gap: 10px; }
.msg-bubble-wrap { position: relative; z-index: 0; }
.msg-bubble-wrap.mine { padding-left: 38px; }
.msg-bubble-wrap.menu-open,
.msg-bubble-wrap.mine:hover { z-index: 40; }
.msg-bubble { width: fit-content; min-width: 0; overflow-wrap: anywhere; position: relative; overflow: visible; }
.msg-bubble-text { white-space: pre-wrap; overflow-wrap: anywhere; word-break: break-word; max-width: 100%; }
.msg-bubble-actions { position: absolute; top: 50%; left: -38px; display: flex; justify-content: flex-end; margin: 0; opacity: 0; pointer-events: none; transform: translateY(-50%); transition: opacity .15s ease; z-index: 12; }
.msg-bubble-wrap.mine:hover .msg-bubble-actions,
.msg-bubble-actions.open { opacity: 1; pointer-events: auto; }
.msg-menu-btn { width: 30px; height: 30px; border: 1px solid rgba(148, 163, 184, .22); border-radius: 999px; background: #ffffff; color: #64748b; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 6px 18px rgba(15, 23, 42, .14); transition: background .15s ease, color .15s ease, transform .15s ease; }
.msg-menu-btn:hover { background: #f8fafc; color: #334155; transform: translateY(-1px); }
.msg-bubble-menu { position: fixed; top: 0; left: 0; min-width: 90px; background: #1f2937; color: #fff; border-radius: 10px; padding: 2px 4px 4px; box-shadow: 0 14px 28px rgba(15, 23, 42, .26); z-index: 10001; display: flex; flex-direction: column; gap: 0; }
.msg-bubble-menu[hidden] { display: none; }
.msg-bubble-menu::after { display: none; }
.msg-bubble-menu-item { width: 100%; border: none; background: transparent; color: inherit; text-align: left; padding: 5px 8px; min-height: 32px; border-radius: 7px; font-size: .82rem; line-height: 1.2; cursor: pointer; display: flex; align-items: center; }
.msg-bubble-menu-item:hover { background: rgba(255,255,255,.08); }
.msg-bubble-menu-item.delete:hover { background: rgba(220, 38, 38, .18); color: #fecaca; }
.msg-delete-modal-backdrop { position: fixed; inset: 0; background: rgba(15, 23, 42, .46); display: flex; align-items: center; justify-content: center; padding: 20px; z-index: 10000; }
.msg-delete-modal-backdrop[hidden] { display: none; }
.msg-delete-modal { width: min(320px, 100%); background: #fff; border-radius: 16px; box-shadow: 0 24px 60px rgba(15, 23, 42, .25); padding: 18px; color: #1f2937; }
.msg-delete-modal-title { margin: 0 0 8px; font-size: 1rem; font-weight: 700; }
.msg-delete-modal-text { margin: 0; font-size: .86rem; line-height: 1.5; color: #4b5563; }
.msg-delete-modal-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 16px; }
.msg-delete-modal-btn { border: none; border-radius: 999px; padding: 9px 14px; font: inherit; font-size: .82rem; cursor: pointer; }
.msg-delete-modal-btn.cancel { background: #e5e7eb; color: #374151; }
.msg-delete-modal-btn.delete { background: #dc2626; color: #fff; }
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
.msg-attach-btn:disabled { opacity: .45; cursor: not-allowed; }
.msg-send-btn.is-editing { background: #16a34a; }
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
                        'message_id' => $message->id,
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
            <div class="msg-bubble-wrap {{ $group['mine'] ? 'mine' : 'theirs' }}" data-message-id="{{ $group['message_id'] }}" data-batch-id="{{ $group['batch_uuid'] ?? '' }}" data-mine="{{ $group['mine'] ? 1 : 0 }}">
                <div class="msg-bubble {{ $group['mine'] ? 'mine' : 'theirs' }}{{ $isImageOnlyGroup ? ' msg-bubble-media-only' : '' }}">
                    @if($group['mine'])
                    <div class="msg-bubble-actions">
                        <button type="button" class="msg-menu-btn" onclick="toggleMessageMenu(event, {{ $group['message_id'] }})" title="Message options">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div class="msg-bubble-menu" data-message-menu="{{ $group['message_id'] }}" hidden>
                            <button type="button" class="msg-bubble-menu-item" onclick="startEditMessage({{ $group['message_id'] }})">Edit</button>
                            <button type="button" class="msg-bubble-menu-item delete" onclick="confirmDeleteMessage({{ $group['message_id'] }})">Delete</button>
                        </div>
                    </div>
                    @endif
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
    var updateMessageUrlTemplate = @json(route('lawfirm.messages.update', ['message' => '__MESSAGE__']));
    var deleteMessageUrlTemplate = @json(route('lawfirm.messages.destroy', ['message' => '__MESSAGE__']));
    var activeEditMessageId = null;
    var pendingDeleteMessageId = null;
    var pendingDeleteSnapshot = null;
    var activeMenuMessageId = null;
    var fileInput     = document.getElementById('fileInput');
    var attachBtn     = document.querySelector('.msg-attach-btn');
    var uploadPreview = document.getElementById('uploadPreview');
    var selectedFiles = [];
    var isSending = false;
    var defaultSendButtonHtml = sendBtn ? sendBtn.innerHTML : '';

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

    function syncComposerMode() {
        if (sendBtn) {
            sendBtn.innerHTML = activeEditMessageId === null
                ? defaultSendButtonHtml
                : '<i class="fas fa-check"></i>';
            sendBtn.classList.toggle('is-editing', activeEditMessageId !== null);
            sendBtn.title = activeEditMessageId === null ? 'Send message' : 'Save edit';
        }

        if (msgInput) {
            msgInput.placeholder = activeEditMessageId === null ? 'Type a message…' : 'Edit message…';
        }

        if (attachBtn) {
            attachBtn.disabled = activeEditMessageId !== null;
            attachBtn.title = activeEditMessageId === null ? 'Attach file' : 'Attachments unavailable while editing';
        }
    }

    function cancelEditMode(clearInput) {
        activeEditMessageId = null;
        syncComposerMode();
        if (clearInput && msgInput) {
            msgInput.value = '';
        }
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
            wrap.setAttribute('data-message-id', message.id);
            wrap.setAttribute('data-batch-id', message.batch_uuid || '');
            wrap.setAttribute('data-mine', mine ? '1' : '0');
            bubble = document.createElement('div');
            bubble.className = 'msg-bubble ' + (mine ? 'mine' : 'theirs');
            if (mine) {
                bubble.appendChild(createMessageMenu(message.id));
            }
            wrap.appendChild(bubble);
            bubbles.appendChild(wrap);
        } else {
            bubble = wrap.querySelector('.msg-bubble');
            if (!wrap.getAttribute('data-message-id')) {
                wrap.setAttribute('data-message-id', message.id);
            }
        }

        if (message.body && !bubble.querySelector('.msg-bubble-text')) {
            var textNode = document.createElement('div');
            textNode.className = 'msg-bubble-text';
            textNode.textContent = message.body;
            bubble.appendChild(textNode);
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
                    var image = document.createElement('img');
                    image.src = message.attachment_path;
                    image.className = 'msg-bubble-img';
                    image.style.cssText = 'display:block;width:auto;max-width:220px;max-height:260px;height:auto;object-fit:contain;';
                    image.alt = message.attachment_name || 'Attachment';
                    image.onclick = function(){ openLightboxFromElement(this); };
                    bubble.appendChild(image);
                }
            } else {
                var files = bubble.querySelector('.msg-bubble-files');
                if (!files) {
                    files = document.createElement('div');
                    files.className = 'msg-bubble-files';
                    bubble.appendChild(files);
                }
                var fileLink = document.createElement('a');
                fileLink.href = message.attachment_path;
                fileLink.className = 'msg-bubble-file';
                fileLink.target = '_blank';
                fileLink.download = message.attachment_name || 'File';
                fileLink.innerHTML = '<i class="fas fa-file-alt"></i> ' + escHtml(message.attachment_name || 'File');
                files.appendChild(fileLink);
            }
        }
        updateBubbleMode(bubble);
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

    function getDeleteMessageUrl(messageId) {
        return deleteMessageUrlTemplate.replace('__MESSAGE__', messageId);
    }

    function getUpdateMessageUrl(messageId) {
        return updateMessageUrlTemplate.replace('__MESSAGE__', messageId);
    }

    function getConversationItem(conversationId) {
        return Array.from(document.querySelectorAll('.msg-conv-item')).find(function(item) {
            var href = item.getAttribute('href') || '';
            return href.indexOf('conversation=' + conversationId) !== -1;
        }) || null;
    }

    function createConversationEmptyState() {
        var empty = document.createElement('div');
        empty.className = 'msg-chat-empty';
        empty.innerHTML = '<i class="fas fa-comment-dots"></i><h3>No messages yet</h3><p>Send a message to start the conversation</p>';
        return empty;
    }

    function ensureConversationEmptyState() {
        if (!bubbles) return;
        if (bubbles.querySelector('.msg-bubble-wrap')) return;
        if (!bubbles.querySelector('.msg-chat-empty')) {
            bubbles.appendChild(createConversationEmptyState());
        }
    }

    function applyConversationPreview(conversationId, latestMessage) {
        var item = getConversationItem(conversationId);
        if (!item) return;

        var previewElement = item.querySelector('.msg-conv-preview');
        if (previewElement) {
            var preview = latestMessage ? latestMessage.preview : 'No messages yet';
            previewElement.textContent = preview.length > 38 ? preview.substring(0, 38) + '...' : preview;
        }

        var timeElement = item.querySelector('.msg-conv-time');
        if (timeElement) {
            timeElement.textContent = latestMessage ? (latestMessage.time || '') : '';
        }
    }

    function adjustConversationUnreadBadge(conversationId, delta) {
        var item = getConversationItem(conversationId);
        if (!item || !delta) return;

        var badge = item.querySelector('.msg-unread-badge');
        var nextCount = Math.max(0, (parseInt(badge ? badge.textContent : '0', 10) || 0) + delta);

        if (nextCount === 0) {
            if (badge) badge.remove();
            return;
        }

        if (!badge) {
            var meta = item.querySelector('.msg-conv-meta');
            if (!meta) return;
            badge = document.createElement('div');
            badge.className = 'msg-unread-badge';
            meta.appendChild(badge);
        }

        badge.textContent = String(nextCount);
        badge.style.display = '';
    }

    function closeAllMessageMenus() {
        document.querySelectorAll('.msg-bubble-menu[data-message-menu]').forEach(function(menu) {
            menu.hidden = true;
        });
        document.querySelectorAll('.msg-bubble-actions.open').forEach(function(actions) {
            actions.classList.remove('open');
        });
        document.querySelectorAll('.msg-bubble-wrap.menu-open').forEach(function(wrap) {
            wrap.classList.remove('menu-open');
        });
        var floatingMenu = document.getElementById('floatingMessageMenu');
        if (floatingMenu) floatingMenu.hidden = true;
        activeMenuMessageId = null;
    }

    function getMessageWrap(messageId) {
        return bubbles ? bubbles.querySelector('.msg-bubble-wrap[data-message-id="' + messageId + '"]') : null;
    }

    function updateBubbleMode(bubble) {
        if (!bubble) return;
        var hasText = !!bubble.querySelector('.msg-bubble-text');
        var hasFiles = !!bubble.querySelector('.msg-bubble-files');
        var hasImages = !!bubble.querySelector('.msg-bubble-album, .msg-bubble-img');
        bubble.classList.toggle('msg-bubble-media-only', hasImages && !hasText && !hasFiles);
    }

    function applyMessageUpdate(payload) {
        var wrap = getMessageWrap(payload.message_id);
        if (!wrap) return;

        var bubble = wrap.querySelector('.msg-bubble');
        if (!bubble) return;

        var textNode = bubble.querySelector('.msg-bubble-text');
        if (payload.body) {
            if (!textNode) {
                textNode = document.createElement('div');
                textNode.className = 'msg-bubble-text';
                var timeNode = bubble.querySelector('.msg-bubble-time');
                bubble.insertBefore(textNode, timeNode || null);
            }
            textNode.textContent = payload.body;
        } else if (textNode) {
            textNode.remove();
        }

        updateBubbleMode(bubble);
        applyConversationPreview(payload.conversation_id, payload.latest_message);
    }

    function buildOptimisticMessageUpdate(messageId, body) {
        var wrap = getMessageWrap(messageId);
        var bubble = wrap ? wrap.querySelector('.msg-bubble') : null;
        var timeNode = bubble ? bubble.querySelector('.msg-bubble-time') : null;
        return {
            message_id: messageId,
            body: body,
            conversation_id: convId,
            latest_message: {
                preview: body,
                time: timeNode ? timeNode.textContent : ''
            }
        };
    }

    function createMessageMenu(messageId) {
        var actions = document.createElement('div');
        actions.className = 'msg-bubble-actions';

        var button = document.createElement('button');
        button.type = 'button';
        button.className = 'msg-menu-btn';
        button.title = 'Message options';
        button.innerHTML = '<i class="fas fa-ellipsis-v"></i>';
        button.addEventListener('click', function(ev) {
            window.toggleMessageMenu(ev, messageId);
        });

        var menu = document.createElement('div');
        menu.className = 'msg-bubble-menu';
        menu.setAttribute('data-message-menu', String(messageId));
        menu.hidden = true;
        menu.innerHTML = '<button type="button" class="msg-bubble-menu-item">Edit</button><button type="button" class="msg-bubble-menu-item delete">Delete</button>';
        menu.querySelector('.msg-bubble-menu-item').addEventListener('click', function() {
            window.startEditMessage(messageId);
        });
        menu.querySelector('.msg-bubble-menu-item.delete').addEventListener('click', function() {
            window.confirmDeleteMessage(messageId);
        });

        actions.appendChild(button);
        actions.appendChild(menu);
        return actions;
    }

    function ensureFloatingMessageMenu() {
        var menu = document.getElementById('floatingMessageMenu');
        if (menu) return menu;

        menu = document.createElement('div');
        menu.id = 'floatingMessageMenu';
        menu.className = 'msg-bubble-menu';
        menu.hidden = true;
        menu.innerHTML = '<button type="button" class="msg-bubble-menu-item" data-action="edit">Edit</button><button type="button" class="msg-bubble-menu-item delete" data-action="delete">Delete</button>';

        menu.querySelector('[data-action="edit"]').addEventListener('click', function() {
            if (activeMenuMessageId !== null) {
                window.startEditMessage(activeMenuMessageId);
            }
        });

        menu.querySelector('[data-action="delete"]').addEventListener('click', function() {
            if (activeMenuMessageId !== null) {
                window.confirmDeleteMessage(activeMenuMessageId);
            }
        });

        document.body.appendChild(menu);
        return menu;
    }

    function positionFloatingMessageMenu(menu, button) {
        if (!menu || !button) return;

        var rect = button.getBoundingClientRect();
        menu.hidden = false;

        var menuWidth = menu.offsetWidth;
        var menuHeight = menu.offsetHeight;
        var viewportWidth = window.innerWidth;
        var viewportHeight = window.innerHeight;
        var gap = 8;

        var left = rect.left;
        var top = rect.top - menuHeight - gap;

        if (top < 8) {
            top = Math.min(viewportHeight - menuHeight - 8, rect.bottom + gap);
        }

        if (left + menuWidth > viewportWidth - 8) {
            left = viewportWidth - menuWidth - 8;
        }

        if (left < 8) {
            left = 8;
        }

        menu.style.top = top + 'px';
        menu.style.left = left + 'px';
    }

    function ensureDeleteMessageModal() {
        var backdrop = document.getElementById('deleteMessageModal');
        if (backdrop) return backdrop;

        backdrop = document.createElement('div');
        backdrop.id = 'deleteMessageModal';
        backdrop.className = 'msg-delete-modal-backdrop';
        backdrop.hidden = true;
        backdrop.innerHTML = '<div class="msg-delete-modal" role="dialog" aria-modal="true" aria-labelledby="deleteMessageTitle"><h3 class="msg-delete-modal-title" id="deleteMessageTitle">Delete message?</h3><p class="msg-delete-modal-text">This message will be removed from the conversation.</p><div class="msg-delete-modal-actions"><button type="button" class="msg-delete-modal-btn cancel" data-action="cancel">Cancel</button><button type="button" class="msg-delete-modal-btn delete" data-action="delete">Delete</button></div></div>';

        backdrop.addEventListener('click', function(event) {
            if (event.target === backdrop) {
                closeDeleteMessageModal();
            }
        });

        backdrop.querySelector('[data-action="cancel"]').addEventListener('click', function() {
            closeDeleteMessageModal();
        });

        backdrop.querySelector('[data-action="delete"]').addEventListener('click', function() {
            submitDeleteMessage();
        });

        document.body.appendChild(backdrop);
        return backdrop;
    }

    function closeDeleteMessageModal() {
        var backdrop = document.getElementById('deleteMessageModal');
        if (backdrop) backdrop.hidden = true;
        pendingDeleteMessageId = null;
    }

    function submitDeleteMessage() {
        if (pendingDeleteMessageId === null) return;
        var messageId = pendingDeleteMessageId;
        closeDeleteMessageModal();
        pendingDeleteSnapshot = removeMessageWrapOptimistically(messageId);

        fetch(getDeleteMessageUrl(messageId), {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(function(response) {
            if (!response.ok) throw new Error('Delete failed');
            return response.json();
        })
        .then(function(payload) {
            pendingDeleteSnapshot = null;
            removeMessageGroup(payload);
        })
        .catch(function() {
            restoreOptimisticallyRemovedMessage();
            window.alert('Message failed to delete. Please try again.');
        });
    }

    function removeMessageWrapOptimistically(messageId) {
        var wrap = getMessageWrap(messageId);
        if (!wrap || !wrap.parentNode) return null;

        var snapshot = {
            messageId: messageId,
            parent: wrap.parentNode,
            nextSibling: wrap.nextSibling,
            node: wrap,
            wasEditing: activeEditMessageId === messageId
        };

        wrap.remove();
        if (snapshot.wasEditing) {
            cancelEditMode(true);
        }
        ensureConversationEmptyState();
        return snapshot;
    }

    function restoreOptimisticallyRemovedMessage() {
        if (!pendingDeleteSnapshot || !pendingDeleteSnapshot.parent || !pendingDeleteSnapshot.node) return;

        pendingDeleteSnapshot.parent.insertBefore(
            pendingDeleteSnapshot.node,
            pendingDeleteSnapshot.nextSibling || null
        );

        var emptyState = bubbles ? bubbles.querySelector('.msg-chat-empty') : null;
        if (emptyState && bubbles.querySelector('.msg-bubble-wrap')) {
            emptyState.remove();
        }

        pendingDeleteSnapshot = null;
    }

    function submitEditMessage() {
        if (activeEditMessageId === null || isSending) return;
        var messageId = activeEditMessageId;
        var nextBody = msgInput ? msgInput.value.trim() : '';
        if (!nextBody) {
            window.alert('Message cannot be empty.');
            return;
        }

        var wrap = getMessageWrap(messageId);
        var bubble = wrap ? wrap.querySelector('.msg-bubble') : null;
        var existingTextNode = bubble ? bubble.querySelector('.msg-bubble-text') : null;
        var previousBody = existingTextNode ? existingTextNode.textContent : '';
        var optimisticPayload = buildOptimisticMessageUpdate(messageId, nextBody);
        var rollbackPayload = buildOptimisticMessageUpdate(messageId, previousBody);

        applyMessageUpdate(optimisticPayload);
        cancelEditMode(true);

        isSending = true;
        if (sendBtn) sendBtn.disabled = true;
        var fd = new FormData();
        fd.append('_method', 'PUT');
        fd.append('body', nextBody);

        fetch(getUpdateMessageUrl(messageId), {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: fd
        })
        .then(function(response) {
            if (!response.ok) throw new Error('Update failed');
            return response.json();
        })
        .then(function(payload) {
            applyMessageUpdate(payload);
        })
        .catch(function() {
            applyMessageUpdate(rollbackPayload);
            window.alert('Message failed to update. Please try again.');
        })
        .finally(function() {
            isSending = false;
            if (sendBtn) sendBtn.disabled = false;
        });
    }

    function removeMessageGroup(payload) {
        if (!bubbles) return;

        var wrap = payload.batch_uuid
            ? bubbles.querySelector('.msg-bubble-wrap[data-batch-id="' + payload.batch_uuid + '"]')
            : bubbles.querySelector('.msg-bubble-wrap[data-message-id="' + payload.deleted_message_id + '"]');

        if (wrap) {
            wrap.remove();
        }

        if (activeEditMessageId === payload.deleted_message_id) {
            cancelEditMode(true);
        }

        ensureConversationEmptyState();
        applyConversationPreview(payload.conversation_id, payload.latest_message);
    }

    window.confirmDeleteMessage = function(messageId) {
        closeAllMessageMenus();
        pendingDeleteMessageId = messageId;
        ensureDeleteMessageModal().hidden = false;
    };

    window.toggleMessageMenu = function(event, messageId) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        var wrap = getMessageWrap(messageId);
        var actions = wrap ? wrap.querySelector('.msg-bubble-actions') : null;
        var button = event && event.currentTarget ? event.currentTarget : (actions ? actions.querySelector('.msg-menu-btn') : null);
        if (!actions || !button || !wrap) return;
        var shouldOpen = activeMenuMessageId !== messageId;
        closeAllMessageMenus();
        if (actions && shouldOpen) {
            actions.classList.add('open');
        }
        if (wrap && shouldOpen) {
            wrap.classList.add('menu-open');
        }
        if (shouldOpen) {
            activeMenuMessageId = messageId;
            positionFloatingMessageMenu(ensureFloatingMessageMenu(), button);
        }
    };

    window.startEditMessage = function(messageId) {
        closeAllMessageMenus();
        var wrap = getMessageWrap(messageId);
        if (!wrap) return;

        var currentTextNode = wrap.querySelector('.msg-bubble-text');
        var currentBody = currentTextNode ? currentTextNode.textContent : '';
        clearFile();
        activeEditMessageId = messageId;
        syncComposerMode();
        if (msgInput) {
            msgInput.value = currentBody;
        }
        setTimeout(function() {
            if (!msgInput) return;
            msgInput.focus();
            msgInput.setSelectionRange(msgInput.value.length, msgInput.value.length);
        }, 0);
    };

    document.addEventListener('click', function() {
        closeAllMessageMenus();
    });
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && activeEditMessageId !== null) {
            cancelEditMode(true);
        } else if (event.key === 'Escape' && pendingDeleteMessageId !== null) {
            closeDeleteMessageModal();
        }
    });

    syncComposerMode();

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
        if (activeEditMessageId !== null) {
            submitEditMessage();
            return;
        }
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
                    .listen('.MessageUpdated', function(e) {
                        applyMessageUpdate(e);
                    })
                    .listen('.MessageDeleted', function(e) {
                        removeMessageGroup(e);
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
                    .listen('.MessageUpdated', function(e) {
                        applyConversationPreview(e.conversation_id, e.latest_message);
                    })
                    .listen('.MessageDeleted', function(e) {
                        applyConversationPreview(e.conversation_id, e.latest_message);
                        adjustConversationUnreadBadge(e.conversation_id, -1 * (parseInt(e.deleted_unread_count || 0, 10) || 0));
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
