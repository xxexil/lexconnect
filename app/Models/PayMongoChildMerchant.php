<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayMongoChildMerchant extends Model
{
    protected $fillable = [
        'provider',
        'merchant_type',
        'status',
        'onboarding_mode',
        'paymongo_child_account_id',
        'hosted_onboarding_url',
        'requirements_payload',
        'metadata',
        'submitted_at',
        'approved_at',
        'rejected_at',
        'last_synced_at',
    ];

    protected $casts = [
        'requirements_payload' => 'array',
        'metadata' => 'array',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'last_synced_at' => 'datetime',
    ];

    public const STATUS_NOT_STARTED = 'not_started';
    public const STATUS_DRAFT = 'draft';
    public const STATUS_REQUIREMENTS_PENDING = 'requirements_pending';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_UNDER_REVIEW = 'under_review';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    public function owner()
    {
        return $this->morphTo();
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_NOT_STARTED => 'Not Started',
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_REQUIREMENTS_PENDING => 'Requirements Pending',
            self::STATUS_SUBMITTED => 'Submitted',
            self::STATUS_UNDER_REVIEW => 'Under Review',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function getStatusToneAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => 'success',
            self::STATUS_SUBMITTED, self::STATUS_UNDER_REVIEW => 'info',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_DRAFT, self::STATUS_REQUIREMENTS_PENDING => 'warning',
            default => 'neutral',
        };
    }
}
