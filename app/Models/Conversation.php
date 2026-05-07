<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = ['client_id','lawyer_id'];

    public function callRoomName(): string
    {
        return 'lexconnect-conversation-' . $this->id;
    }

    public function directCallInvitePayload(string $fromName, string $title = 'Video Call'): string
    {
        return '__LC_CALL__' . json_encode([
            'v' => 1,
            'type' => 'invite',
            'mode' => 'one-on-one',
            'conversationId' => $this->id,
            'title' => $title,
            'fromName' => $fromName,
        ], JSON_UNESCAPED_SLASHES);
    }

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
