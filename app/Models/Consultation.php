<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Consultation extends Model
{
    protected $fillable = [
        'code','client_id','lawyer_id','scheduled_at','duration_minutes',
        'type','status','price','notes','case_document',
    ];

    protected $casts = ['scheduled_at' => 'datetime'];

    public function getDurationLabelAttribute(): string {
        return match((int) $this->duration_minutes) {
            30  => '30 minutes',
            60  => '1 hour',
            90  => '1.5 hours',
            120 => '2 hours',
            default => $this->duration_minutes . ' min',
        };
    }

    public function videoJoinOpensAt(): \Carbon\Carbon
    {
        return \Carbon\Carbon::parse($this->scheduled_at)->copy()->subMinutes(5)->startOfMinute();
    }

    public function canJoinVideoCall(?\Carbon\Carbon $at = null): bool
    {
        return $this->type === 'video'
            && $this->status === 'upcoming'
            && ($at ?? now())->gte($this->videoJoinOpensAt());
    }

    public function getCaseDocumentUrlAttribute(): ?string
    {
        if (!$this->case_document) {
            return null;
        }

        if (str_starts_with($this->case_document, 'http')) {
            return $this->case_document;
        }

        return Storage::disk('public')->url($this->case_document);
    }

    public function videoRoomName(): string
    {
        return 'LexConnect-' . $this->code;
    }

    public function videoJoinUrl(): string
    {
        return url('/consultations/' . $this->getKey() . '/video');
    }

    public function videoEchoSignalingChannel(): string
    {
        return 'consultation.' . $this->getKey();
    }

    public function videoPresenceSignalingChannel(): string
    {
        return 'presence-' . $this->videoEchoSignalingChannel();
    }

    public function toApiArray(?int $viewerId = null): array
    {
        return [
            'id'                => $this->id,
            'code'              => $this->code,
            'scheduled_at'      => $this->scheduled_at,
            'type'              => $this->type,
            'status'            => $this->status,
            'duration_minutes'  => $this->duration_minutes,
            'price'             => $this->price,
            'notes'             => $this->notes,
            'case_document'     => $this->case_document,
            'case_document_url' => $this->case_document_url,
            'can_join_video'    => $this->canJoinVideoCall(),
            'video_room_name'   => $this->type === 'video' ? $this->videoRoomName() : null,
            'video_join_url'    => $this->type === 'video' ? $this->videoJoinUrl() : null,
            'video_signaling_channel' => $this->type === 'video' ? $this->videoPresenceSignalingChannel() : null,
            'video_echo_signaling_channel' => $this->type === 'video' ? $this->videoEchoSignalingChannel() : null,
        ];
    }

    public function client() {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function lawyer() {
        return $this->belongsTo(User::class, 'lawyer_id');
    }

    public function payment() {
        return $this->hasOne(Payment::class)->where('type', 'downpayment');
    }

    public function downpaymentPayment()
    {
        return $this->hasOne(Payment::class)->where('type', 'downpayment');
    }

    public function balancePayment()
    {
        return $this->hasOne(Payment::class)->where('type', 'balance');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function review() {
        return $this->hasOne(Review::class);
    }

    public static function expireOverdue(string $column, int $userId): void
    {
        static::where($column, $userId)
            ->whereIn('status', ['pending', 'upcoming'])
            ->get()
            ->each(function ($consultation) {
                $endsAt = \Carbon\Carbon::parse($consultation->scheduled_at)
                    ->addMinutes($consultation->duration_minutes);
                if (now()->gte($endsAt)) {
                    $consultation->update(['status' => 'expired']);
                }
            });
    }
}
