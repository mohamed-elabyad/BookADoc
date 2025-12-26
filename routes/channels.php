<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('conversation.{conversationId}', function ($user, int $conversationId) {
    $conversation = Conversation::find($conversationId);

    if (!$conversation) {
        return false;
    }


    if ($conversation->user_id === $user->id) {
        return true;
    }

    if ($user->role->value === 'doctor' && $user->doctor && $conversation->doctor_id === $user->doctor->id) {
        return true;
    }

    return false;
});
