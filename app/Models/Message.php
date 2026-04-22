<?php

namespace App\Models;

use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Message extends Model
{
    public const ATTACHMENT_DISK = 'local';

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'body',
        'read_at',
        'attachment_path',
        'attachment_name',
        'attachment_type',
        'attachment_mime',
        'attachment_size',
        'batch_uuid',
    ];

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

    public static function messageValidationRules(int $bodyMax = 5000): array
    {
        return [
            'body' => 'nullable|string|max:' . $bodyMax,
            'attachment' => 'nullable|file|max:20480|mimes:jpeg,png,jpg,gif,webp,heic,heif,pdf,doc,docx,txt,rtf,csv,mp3,wav,m4a,aac,ogg,oga,webm',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:20480|mimes:jpeg,png,jpg,gif,webp,heic,heif,pdf,doc,docx,txt,rtf,csv,mp3,wav,m4a,aac,ogg,oga,webm',
        ];
    }

    public static function messageValidationMessages(): array
    {
        return [
            'attachment.mimes' => 'Unsupported file type. Allowed types: images, PDF, DOC, DOCX, TXT, RTF, CSV, MP3, WAV, M4A, AAC, OGG, WEBM.',
            'attachments.*.mimes' => 'Unsupported file type. Allowed types: images, PDF, DOC, DOCX, TXT, RTF, CSV, MP3, WAV, M4A, AAC, OGG, WEBM.',
            'attachment.max' => 'Attachment must be 20 MB or smaller.',
            'attachments.*.max' => 'Each attachment must be 20 MB or smaller.',
        ];
    }

    public static function storeUploadedAttachment(UploadedFile $file): array
    {
        $mime = $file->getMimeType() ?: 'application/octet-stream';

        return [
            'attachment_path' => $file->store('message-attachments', static::ATTACHMENT_DISK),
            'attachment_name' => $file->getClientOriginalName(),
            'attachment_type' => static::attachmentTypeForMime($mime),
            'attachment_mime' => $mime,
            'attachment_size' => $file->getSize(),
        ];
    }

    public function attachmentDisk(): string
    {
        if ($this->attachment_path && Storage::disk(static::ATTACHMENT_DISK)->exists($this->attachment_path)) {
            return static::ATTACHMENT_DISK;
        }

        return 'public';
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        if (!$this->attachment_path) {
            return null;
        }

        if (str_starts_with($this->attachment_path, 'http')) {
            return $this->attachment_path;
        }

        return route('api.messages.attachments.show', $this);
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
            'attachment_size' => $this->attachment_size,
            'attachment_mime' => $this->attachment_mime,
            'batch_uuid'      => $this->batch_uuid,
        ];
    }
}
