<div style="flex:1;overflow-y:auto;overflow-x:hidden;">
    <div style="padding:10px 14px 8px;border-bottom:1px solid #f0f2f5;">
        <div style="display:flex;align-items:center;gap:8px;background:#f5f7fa;border:1px solid #e3e7ed;border-radius:8px;padding:7px 12px;">
            <i class="fas fa-search" style="color:#aaa;font-size:.8rem;"></i>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search conversations..." autocomplete="off"
                style="border:none;background:transparent;outline:none;font-size:.87rem;color:#333;width:100%;">
        </div>
    </div>
    @foreach($conversations as $conv)
        @php
            $other   = auth()->id() === $conv->client_id ? $conv->lawyer : $conv->client;
            $latest  = $conv->latestMessage;
            $isActive= $activeConv && $activeConv->id === $conv->id;
            $unread  = $conv->messages->filter(fn($m) => $m->sender_id !== auth()->id() && !$m->read_at)->count();
        @endphp
        <a href="{{ route('messages', ['conversation' => $conv->id]) }}"
           wire:key="conversation-{{ $conv->id }}"
           class="msg-conv-item {{ $isActive ? 'active' : '' }}" data-id="{{ $conv->id }}">
            <div class="msg-conv-avatar">
                @if($other && $other->avatar_url)
                    <img src="{{ $other->avatar_url }}" style="width:44px;height:44px;border-radius:50%;object-fit:cover;">
                @else
                    {{ $other ? strtoupper(substr($other->name,0,1)) : '?' }}
                @endif
            </div>
            <div class="msg-conv-info">
                <div class="msg-conv-name">{{ $other ? $other->name : 'Unknown' }}</div>
                <div class="msg-conv-preview">{{ $latest ? Str::limit($latest->body ?: '📎 Attachment', 38) : 'No messages yet' }}</div>
            </div>
            <div class="msg-conv-meta">
                @if($latest)<div class="msg-conv-time">{{ $latest->created_at->diffForHumans(null,true) }}</div>@endif
                @if($unread > 0)<div class="msg-unread-badge">{{ $unread }}</div>@endif
            </div>
        </a>
    @endforeach
</div>
