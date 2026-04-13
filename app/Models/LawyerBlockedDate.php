<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class LawyerBlockedDate extends Model
{
    protected $table = 'lawyer_blocked_dates';

    protected $fillable = [
        'lawyer_id',
        'blocked_date',
        'start_time',
        'end_time',
        'reason',
    ];

    protected $casts = [
        'blocked_date' => 'date',
    ];

    public function lawyer()
    {
        return $this->belongsTo(User::class, 'lawyer_id');
    }

    public function isAllDay(): bool
    {
        return empty($this->start_time) || empty($this->end_time);
    }

    public function formattedTimeRange(): ?string
    {
        if ($this->isAllDay()) {
            return null;
        }

        return Carbon::createFromFormat('H:i:s', $this->start_time)->format('g:i A')
            . ' - '
            . Carbon::createFromFormat('H:i:s', $this->end_time)->format('g:i A');
    }

    public function toScheduleArray(): array
    {
        return [
            'id' => $this->id,
            'date' => $this->blocked_date->format('Y-m-d'),
            'start_time' => $this->start_time ? substr($this->start_time, 0, 5) : null,
            'end_time' => $this->end_time ? substr($this->end_time, 0, 5) : null,
            'is_all_day' => $this->isAllDay(),
            'reason' => $this->reason,
            'label' => $this->isAllDay() ? 'All day' : $this->formattedTimeRange(),
        ];
    }
}
