<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
