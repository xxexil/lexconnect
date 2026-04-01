<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentRiskEvent extends Model
{
    protected $fillable = [
        'client_id',
        'lawyer_id',
        'consultation_id',
        'payment_id',
        'context',
        'amount',
        'currency',
        'risk_score',
        'risk_level',
        'recommendation',
        'ip_address',
        'user_agent',
        'email',
        'flags',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'flags' => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function lawyer()
    {
        return $this->belongsTo(User::class, 'lawyer_id');
    }

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
