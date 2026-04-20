<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Message extends Model
{
    protected $fillable = ['conversation_id','sender_id','body','read_at','attachment_path','attachment_name','attachment_type','batch_uuid'];

    protected $casts = ['read_at' => 'datetime'];

    public function sender() {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function conversation() {
        return $this->belongsTo(Conversation::class);
    }

    public static function attachmentTypeForMime(?string $mime): string
    {
        if ($mime && str_starts_with($mime, 'image/')) {
            return 'image';
        }

        if ($mime && str_starts_with($mime, 'audio/')) {
            return 'audio';
        }

        return 'file';
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        if (!$this->attachment_path) {
            return null;
        }

        if (str_starts_with($this->attachment_path, 'http')) {
            return $this->attachment_path;
        }

        return Storage::disk('public')->url($this->attachment_path);
    }

    public function toApiArray(?int $viewerId = null): array
    {
        return [
            'id'              => $this->id,
            'conversation_id' => $this->conversation_id,
            'body'            => $this->body,
            'sender_id'       => $this->sender_id,
            'sender_name'     => $this->sender?->name,
            'created_at'      => $this->created_at,
            'time'            => $this->created_at?->format('g:i A'),
            'is_mine'         => $viewerId !== null ? $this->sender_id === $viewerId : null,
            'attachment_url'  => $this->attachment_url,
            'attachment_path' => $this->attachment_url,
            'attachment_name' => $this->attachment_name,
            'attachment_type' => $this->attachment_type,
            'batch_uuid'      => $this->batch_uuid,
        ];
    }
}
