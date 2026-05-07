<?php

use App\Models\Conversation;
use App\Models\Consultation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('user.{id}.consultations', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('user.{id}.payments', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Only conversation participants may subscribe
Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::find($conversationId);
    return $conversation
        && ($conversation->client_id === $user->id || $conversation->lawyer_id === $user->id);
});

// Presence channel — tracks who is actively viewing a conversation
Broadcast::channel('presence-conversation.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::find($conversationId);
    if ($conversation
        && ($conversation->client_id === $user->id || $conversation->lawyer_id === $user->id)) {
        return ['id' => $user->id, 'name' => $user->name];
    }
    return false;
});

// Global user presence — any logged-in user joining this channel is "online"
Broadcast::channel('presence-user.{userId}', function ($user, $userId) {
    if ((int) $user->id === (int) $userId) {
        return ['id' => $user->id, 'name' => $user->name];
    }
    return false;
});

Broadcast::channel('consultation.{consultationId}', function ($user, $consultationId) {
    $consultation = Consultation::find($consultationId);

    if ($consultation
        && ((int) $consultation->client_id === (int) $user->id || (int) $consultation->lawyer_id === (int) $user->id)) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role,
        ];
    }

    return false;
});

Broadcast::channel('presence-consultation.{consultationId}', function ($user, $consultationId) {
    $consultation = Consultation::find($consultationId);

    if ($consultation
        && ((int) $consultation->client_id === (int) $user->id || (int) $consultation->lawyer_id === (int) $user->id)) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role,
        ];
    }

    return false;
});
