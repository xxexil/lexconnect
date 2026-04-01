<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['client_id', 'lawyer_id', 'consultation_id', 'rating', 'comment'];

    public function client()       { return $this->belongsTo(User::class, 'client_id'); }
    public function lawyer()       { return $this->belongsTo(User::class, 'lawyer_id'); }
    public function consultation() { return $this->belongsTo(Consultation::class); }
}
