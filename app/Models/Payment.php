<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'client_id','lawyer_id','consultation_id','amount','status','type','firm_cut','lawyer_net','paymongo_session_id',
        'payment_reference','payment_proof'
    ];

    public function client() {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function lawyer() {
        return $this->belongsTo(User::class, 'lawyer_id');
    }

    public function consultation() {
        return $this->belongsTo(Consultation::class);
    }
}
