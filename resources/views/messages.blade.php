 
@extends('layouts.app')
@section('title', 'Messages')
@section('body-class', '')
@section('content')
@section('hide-footer', true)
<noscript><meta http-equiv="refresh" content="5"></noscript>
<style>
.main-content { padding: 0; max-width: 100%; }
.msg-layout { display: flex; height: calc(100vh - 70px); background: #f5f7fa; overflow: hidden; border-radius: 12px; box-shadow: 0 2px 16px rgba(0,0,0,.07); min-width: 0; }

/* Sidebar */
.msg-sidebar { width: 300px; flex-shrink: 0; background: #fff; display: flex; flex-direction: column; border-right: 1px solid #f0f2f5; overflow: hidden; }
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
.msg-bubbles { flex: 1; overflow-y: auto; overflow-x: visible; padding: 20px 24px 20px 56px; display: flex; flex-direction: column; gap: 10px; background: #f5f7fa; min-width: 0; }
.msg-bubbles::-webkit-scrollbar { width: 4px; }
.msg-bubbles::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
.msg-bubble-wrap { display: flex; min-width: 0; max-width: 100%; position: relative; z-index: 0; }
.msg-bubble-wrap.mine { justify-content: flex-end; padding-left: 38px; }
.msg-bubble-wrap.theirs { justify-content: flex-start; }
.msg-bubble-wrap.menu-open,
.msg-bubble-wrap.mine:hover { z-index: 40; }
.msg-bubble { width: fit-content; max-width: 62%; min-width: 0; overflow-wrap: anywhere; position: relative; overflow: visible; }
.msg-bubble.msg-bubble-media-only { background: transparent !important; box-shadow: none !important; padding: 0 !important; }
.msg-bubble-text { padding: 10px 14px; border-radius: 18px; font-size: .88rem; line-height: 1.55; white-space: pre-wrap; overflow-wrap: anywhere; word-break: break-word; max-width: 100%; }
.msg-bubble.mine   .msg-bubble-text { background: #1e2d4d; color: #fff; border-bottom-right-radius: 4px; }
.msg-bubble.theirs .msg-bubble-text { background: #fff; color: #1e2d4d; border-bottom-left-radius: 4px; box-shadow: 0 1px 4px rgba(0,0,0,.07); }
.msg-bubble-time { font-size: .68rem; color: #b0b7c3; margin-top: 4px; }
.msg-bubble.mine   .msg-bubble-time { text-align: right; }
.msg-bubble.theirs .msg-bubble-time { text-align: left; }
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

/* Attachment in bubble */
.msg-bubble-img { width: auto; max-width: min(220px, 100%); max-height: 260px; height: auto; object-fit: contain; border-radius: 12px; display: block; margin-top: 6px; cursor: pointer; }
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
.msg-attach-btn:disabled { opacity: .45; cursor: not-allowed; }
.msg-input { flex: 1; padding: 11px 18px; border: 1.5px solid #e5e7eb; border-radius: 24px; font-size: .88rem; font-family: inherit; outline: none; background: #f5f7fa; color: #1e2d4d; transition: border-color .2s, background .2s; }
.msg-input:focus { border-color: #1e2d4d; background: #fff; }
.msg-input::placeholder { color: #9ca3af; }
.msg-send-btn { width: 42px; height: 42px; background: #1e2d4d; border-radius: 50%; border: none; color: #fff; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: .9rem; flex-shrink: 0; transition: background .2s, transform .1s; box-shadow: 0 2px 8px rgba(30,45,77,.25); }
.msg-send-btn:hover { background: #2d4a7a; transform: scale(1.05); }
.msg-send-btn.is-editing { background: #16a34a; }

.msg-chat-empty { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #c4c9d4; gap: 10px; }
.msg-chat-empty i { font-size: 3rem; }
.msg-chat-empty h3 { margin: 0; font-size: 1rem; color: #9ca3af; }
.msg-chat-empty p { margin: 0; font-size: .85rem; }

/* Lightbox */
#imgLightbox { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.85); z-index: 9999; align-items: center; justify-content: center; }
#imgLightbox.open { display: flex; }
#imgLightbox img { max-width: 90vw; max-height: 90vh; border-radius: 8px; }
.msg-lightbox-nav { position: absolute; top: 50%; transform: translateY(-50%); width: 46px; height: 46px; border: none; border-radius: 999px; background: rgba(15, 23, 42, .72); color: #fff; font-size: 1rem; cursor: pointer; display: flex; align-items: center; justify-content: center; }
.msg-lightbox-nav.prev { left: 24px; }
.msg-lightbox-nav.next { right: 24px; }
.msg-lightbox-nav[disabled] { opacity: .35; cursor: default; }

@media (max-width: 900px) {
    .msg-layout {
        height: auto;
        min-height: calc(100vh - 90px);
        flex-direction: column;
    }

    .msg-sidebar {
        width: 100%;
        max-height: 42vh;
        border-right: none;
        border-bottom: 1px solid #f0f2f5;
    }

    .msg-chat {
        min-height: 58vh;
    }
}

@media (max-width: 640px) {
    .msg-chat-header,
    .msg-form-wrap {
        padding-left: 14px;
        padding-right: 14px;
    }

    .msg-bubbles {
        padding: 16px 14px;
    }

    .msg-bubble-wrap.mine {
        padding-left: 18px;
    }

    .msg-bubble {
        max-width: 82%;
    }

    .msg-bubble-actions {
        left: -28px;
    }

    .msg-menu-btn {
        width: 26px;
        height: 26px;
    }
}
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
            $status = $other->lawyerProfile?->currentStatus() ?? 'offline';
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
                    <i class="fas fa-circle" id="presenceDot" style="font-size:.45rem;color:{{ $status==='active'?'#22c55e':($status==='busy'?'#f59e0b':'#d1d5db') }};"></i>
                    <span id="presenceText">{{ $status==='active'?'Active':($status==='busy'?'Busy':'Offline') }}</span>
                </div>
            </div>
        </div>

        @php
            $messageGroups = [];
            foreach ($messages as $message) {
                $mine = $message->sender_id === auth()->id();
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
                        'path' => $message->attachment_url,
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
            <div class="msg-form">
                <input type="file" id="fileInput" accept="image/*,.pdf,.doc,.docx,.txt" style="display:none;" multiple>
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

    var currentUserId = {{ auth()->id() }};
    var convId   = document.getElementById('convId') ? parseInt(document.getElementById('convId').value) : null;
    var msgInput = document.getElementById('msgInput');
    var sendBtn  = document.getElementById('sendBtn');
    var csrfToken= (document.querySelector('meta[name="csrf-token"]')||{getAttribute:function(){return '';}}).getAttribute('content');
    var updateMessageUrlTemplate = @json(route('messages.update', ['message' => '__MESSAGE__']));
    var deleteMessageUrlTemplate = @json(route('messages.destroy', ['message' => '__MESSAGE__']));
    var latestMessagesUrlTemplate = @json(route('messages.latest', ['conversation' => '__CONVERSATION__']));
    var activeEditMessageId = null;
    var pendingDeleteMessageId = null;
    var pendingDeleteSnapshot = null;
    var activeMenuMessageId = null;

    // File handling
    var selectedFiles = [];
    var fileInput    = document.getElementById('fileInput');
    var attachBtn    = document.querySelector('.msg-attach-btn');
    var uploadPreview= document.getElementById('uploadPreview');
    var isSending    = false;
    var defaultSendButtonHtml = sendBtn ? sendBtn.innerHTML : '';
    var activeConversationSubscribed = false;
    var globalConversationsSubscribed = false;
    var lastSeenMessageId = 0;

    if (bubbles) {
        bubbles.querySelectorAll('.msg-bubble-wrap[data-message-id]').forEach(function(wrap) {
            var id = parseInt(wrap.getAttribute('data-message-id'), 10);
            if (!Number.isNaN(id)) {
                lastSeenMessageId = Math.max(lastSeenMessageId, id);
            }
        });
    }

    if (fileInput) {
        fileInput.addEventListener('change', function(){ selectedFiles = Array.from(this.files || []); renderPreview(); });
    }

    function escHtml(t){ return (t||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

    function renderPreview() {
        if (!uploadPreview) return;
        if (!selectedFiles.length) { uploadPreview.style.display='none'; uploadPreview.innerHTML=''; return; }
        uploadPreview.style.display = 'flex';
        uploadPreview.innerHTML = '';

        selectedFiles.forEach(function(file, index) {
            var item = document.createElement('div');
            item.className = 'msg-upload-item';

            if (file.type.startsWith('image/')) {
                var reader = new FileReader();
                reader.onload = function(e){
                    item.innerHTML = '<img src="'+e.target.result+'"><button type="button" class="rm" onclick="removeSelectedFile('+index+')"><i class="fas fa-times"></i></button>';
                };
                reader.readAsDataURL(file);
            } else {
                item.innerHTML = '<div class="file-chip"><i class="fas fa-file"></i>'+escHtml(file.name)+'</div><button type="button" class="rm" onclick="removeSelectedFile('+index+')"><i class="fas fa-times"></i></button>';
            }

            uploadPreview.appendChild(item);
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
            msgInput.placeholder = activeEditMessageId === null ? 'Type a message...' : 'Edit message...';
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

    function getDeleteMessageUrl(messageId) {
        return deleteMessageUrlTemplate.replace('__MESSAGE__', messageId);
    }

    function getUpdateMessageUrl(messageId) {
        return updateMessageUrlTemplate.replace('__MESSAGE__', messageId);
    }

    function getConversationItem(conversationId) {
        return document.querySelector('.msg-conv-item[data-id="' + conversationId + '"]')
            || Array.from(document.querySelectorAll('.msg-conv-item')).find(function(item) {
                var href = item.getAttribute('href') || '';
                return href.indexOf('conversation=' + conversationId) !== -1;
            })
            || null;
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
        if (latestMessage) {
            if (!timeElement) {
                var meta = item.querySelector('.msg-conv-meta');
                if (meta) {
                    timeElement = document.createElement('div');
                    timeElement.className = 'msg-conv-time';
                    meta.prepend(timeElement);
                }
            }
            if (timeElement) {
                timeElement.textContent = latestMessage.time || '';
            }
        } else if (timeElement) {
            timeElement.textContent = '';
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
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
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
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
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

    window.clearFile = function(){ selectedFiles=[]; if(fileInput) fileInput.value=''; renderPreview(); };
    window.removeSelectedFile = function(index){
        selectedFiles = selectedFiles.filter(function(_, i){ return i !== index; });
        renderPreview();
    };

    function appendBubbleMessage(message, mine) {
        if (!bubbles) return;
        if (message.id && bubbles.querySelector('.msg-bubble-wrap[data-message-id="' + message.id + '"]')) {
            return;
        }

        var empty = bubbles.querySelector('.msg-chat-empty');
        if (empty) empty.remove();

        var wrap = null;
        if (message.batch_uuid) {
            wrap = bubbles.querySelector('.msg-bubble-wrap[data-batch-id="' + message.batch_uuid + '"][data-mine="' + (mine ? 1 : 0) + '"]');
        }

        var inner;
        if (!wrap) {
            wrap = document.createElement('div');
            wrap.className = 'msg-bubble-wrap ' + (mine ? 'mine' : 'theirs');
            wrap.setAttribute('data-message-id', message.id);
            wrap.setAttribute('data-batch-id', message.batch_uuid || '');
            wrap.setAttribute('data-mine', mine ? '1' : '0');

            inner = document.createElement('div');
            inner.className = 'msg-bubble ' + (mine ? 'mine' : 'theirs');
            if (mine) {
                inner.appendChild(createMessageMenu(message.id));
            }
            wrap.appendChild(inner);
            bubbles.appendChild(wrap);
        } else {
            inner = wrap.querySelector('.msg-bubble');
            if (!wrap.getAttribute('data-message-id')) {
                wrap.setAttribute('data-message-id', message.id);
            }
        }

        if (message.body && !inner.querySelector('.msg-bubble-text')) {
            var txt = document.createElement('div');
            txt.className = 'msg-bubble-text';
            txt.textContent = message.body;
            inner.appendChild(txt);
        }

        if (message.attachment_path) {
            if (message.attachment_type === 'image') {
                var groupedAlbum = message.batch_uuid ? inner.querySelector('.msg-bubble-album') : null;
                if (message.batch_uuid) {
                    if (!groupedAlbum) {
                        groupedAlbum = document.createElement('div');
                        groupedAlbum.className = 'msg-bubble-album';
                        groupedAlbum.setAttribute('data-photo-count', '0');
                        groupedAlbum.style.cssText = 'width:240px;max-width:100%;';
                        groupedAlbum.innerHTML = '<div class="msg-bubble-album-stack" style="position:relative;width:200px;max-width:100%;height:150px;"><span class="msg-bubble-album-layer layer-1" aria-hidden="true"></span><span class="msg-bubble-album-layer layer-2" aria-hidden="true"></span><span class="msg-bubble-album-layer layer-3" aria-hidden="true"></span><img class="msg-bubble-album-cover" style="width:100%;height:100%;object-fit:cover;" alt="Attachment"></div><div class="msg-bubble-album-meta">0 photos</div><div class="msg-bubble-album-thumbs"></div>';
                        inner.appendChild(groupedAlbum);
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
                    var img = document.createElement('img');
                    img.src = message.attachment_path; img.className = 'msg-bubble-img';
                    img.style.cssText = 'display:block;width:auto;max-width:220px;max-height:260px;height:auto;object-fit:contain;';
                    img.alt = message.attachment_name || 'Attachment';
                    img.onclick = function(){ openLightboxFromElement(this); };
                    inner.appendChild(img);
                }
            } else {
                var files = inner.querySelector('.msg-bubble-files');
                if (!files) {
                    files = document.createElement('div');
                    files.className = 'msg-bubble-files';
                    inner.appendChild(files);
                }
                var a = document.createElement('a');
                a.href = message.attachment_path; a.className = 'msg-bubble-file';
                a.target = '_blank'; a.download = message.attachment_name||'';
                a.innerHTML = '<i class="fas fa-file-alt"></i> '+escHtml(message.attachment_name||'File');
                files.appendChild(a);
            }
        }

        updateBubbleMode(inner);

        var t = inner.querySelector('.msg-bubble-time');
        if (!t) {
            t = document.createElement('div');
            t.className = 'msg-bubble-time';
            inner.appendChild(t);
        }
        t.textContent = message.time;
        var numericMessageId = parseInt(message.id, 10);
        if (!Number.isNaN(numericMessageId)) {
            lastSeenMessageId = Math.max(lastSeenMessageId, numericMessageId);
        }
        bubbles.scrollTop = bubbles.scrollHeight;
    }

    function getLatestMessagesUrl() {
        return latestMessagesUrlTemplate.replace('__CONVERSATION__', convId);
    }

    function pollLatestMessages() {
        if (!convId || !bubbles) return;

        fetch(getLatestMessagesUrl() + '?after_id=' + encodeURIComponent(lastSeenMessageId), {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        })
            .then(function(response) {
                if (!response.ok) throw new Error('Could not fetch latest messages.');
                return response.json();
            })
            .then(function(data) {
                var messages = Array.isArray(data.messages) ? data.messages : [];
                messages.forEach(function(message) {
                    appendBubbleMessage(message, message.sender_id === currentUserId);
                });
            })
            .catch(function(error) {
                console.warn('Latest message polling failed:', error);
            });
    }

    function removeBubbleMessage(messageId) {
        if (!bubbles || !messageId) return;
        var wrap = bubbles.querySelector('.msg-bubble-wrap[data-message-id="' + messageId + '"]');
        if (wrap) {
            wrap.remove();
            ensureConversationEmptyState();
        }
    }

    function optimisticTextMessage(body) {
        return {
            id: 'pending-' + Date.now() + '-' + Math.random().toString(36).slice(2),
            conversation_id: convId,
            sender_id: currentUserId,
            body: body,
            time: new Date().toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' }),
            batch_uuid: '',
            attachment_path: null,
            attachment_type: null,
            attachment_name: null,
        };
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

        var pendingMessage = null;
        var originalBody = body;
        if (body && !selectedFiles.length) {
            pendingMessage = optimisticTextMessage(body);
            appendBubbleMessage(pendingMessage, true);
            if (msgInput) msgInput.value = '';
        }

        var fd = new FormData();
        fd.append('conversation_id', convId);
        fd.append('body', body);
        selectedFiles.forEach(function(file){ fd.append('attachments[]', file); });

        fetch('{{ route("messages.send") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: fd,
        })
        .then(function(r){
            if (!r.ok) throw new Error('Server error');
            return r.json();
        })
        .then(function(data){
            var messages = Array.isArray(data.messages) ? data.messages : [];
            if (pendingMessage) {
                removeBubbleMessage(pendingMessage.id);
            }
            messages.forEach(function(message){
                appendBubbleMessage(message, true);
            });
            if (msgInput) msgInput.value = '';
            clearFile();
        })
        .catch(function(){
            if (pendingMessage) {
                removeBubbleMessage(pendingMessage.id);
                if (msgInput && !msgInput.value) msgInput.value = originalBody;
            }
            window.alert('Message failed to send. Please try again.');
        })
        .finally(function(){
            isSending = false;
            if (sendBtn) sendBtn.disabled = false;
        });
    }

    if (sendBtn) sendBtn.addEventListener('click', doSend);
    if (msgInput) msgInput.addEventListener('keydown', function(e){ if(e.key==='Enter'&&!e.shiftKey){e.preventDefault();doSend();} });

    // WebSocket
    if (convId) {
        function setupActiveConversationSocket(attempt) {
            attempt = attempt || 1;
            if (activeConversationSubscribed) return;
            if (!window.Echo) {
                if (attempt < 10) {
                    setTimeout(function(){ setupActiveConversationSocket(attempt + 1); }, 500);
                }
                return;
            }
            activeConversationSubscribed = true;
            var dot  = document.getElementById('presenceDot');
            var text = document.getElementById('presenceText');
            var initialPresenceStatus = @json($status ?? 'offline');
            function setOnline(on) {
                if (!dot||!text) return;
                if (initialPresenceStatus === 'busy') return;
                dot.style.color = on ? '#22c55e' : '#d1d5db';
                text.textContent = on ? 'Active' : 'Offline';
            }
            window.Echo.private('conversation.'+convId).listen('.MessageSent', function(e){
                if (e.sender_id !== currentUserId)
                    appendBubbleMessage(e, false);
            }).listen('.MessageUpdated', function(e) {
                applyMessageUpdate(e);
            }).listen('.MessageDeleted', function(e) {
                removeMessageGroup(e);
            });
            if (convId) {
                window.Echo.join('presence-conversation.' + convId)
                    .here(function(users){
                        if (users.some(function(user){ return user.id !== currentUserId; })) {
                            setOnline(true);
                        }
                    })
                    .joining(function(user){
                        if (user.id !== currentUserId) setOnline(true);
                    })
                    .leaving(function(user){
                        if (user.id !== currentUserId && initialPresenceStatus === 'offline') setOnline(false);
                    });
            }
        }

        // Global listeners
        function setupGlobalConversationSockets(attempt) {
            attempt = attempt || 1;
            if (globalConversationsSubscribed) return;
            if (!window.Echo) {
                if (attempt < 10) {
                    setTimeout(function(){ setupGlobalConversationSockets(attempt + 1); }, 500);
                }
                return;
            }
            globalConversationsSubscribed = true;
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
                }).listen('.MessageUpdated', function(e) {
                    applyConversationPreview(e.conversation_id, e.latest_message);
                }).listen('.MessageDeleted', function(e) {
                    applyConversationPreview(e.conversation_id, e.latest_message);
                    adjustConversationUnreadBadge(e.conversation_id, -1 * (parseInt(e.deleted_unread_count || 0, 10) || 0));
                });
            });
        }

        setTimeout(function(){ setupActiveConversationSocket(1); }, 300);
        setTimeout(function(){ setupGlobalConversationSockets(1); }, 500);
        setInterval(pollLatestMessages, 3000);
    }
})();
</script>
@endsection
