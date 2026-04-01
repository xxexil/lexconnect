<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = ['client_id','lawyer_id'];

    public function client() {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function lawyer() {
        return $this->belongsTo(User::class, 'lawyer_id');
    }

    public function messages() {
        return $this->hasMany(Message::class);
    }

    public function latestMessage() {
        return $this->hasOne(Message::class)->latestOfMany();
    }
}
