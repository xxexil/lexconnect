<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FirmApplication extends Model
{
    protected $fillable = [
        'lawyer_id', 'law_firm_id', 'message', 'status', 'responded_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    public function lawyer()
    {
        return $this->belongsTo(User::class, 'lawyer_id');
    }

    public function lawFirm()
    {
        return $this->belongsTo(LawFirmProfile::class, 'law_firm_id');
    }
}
