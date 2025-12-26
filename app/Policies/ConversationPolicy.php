<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\User;

class ConversationPolicy
{
    public function view(User $user, Conversation $conversation): bool
    {
        if ($conversation->user_id === $user->id) {
            return true;
        }

        if ($user->doctor && $conversation->doctor_id === $user->doctor->id) {
            return true;
        }

        return false;
    }

    public function sendMessage(User $user, Conversation $conversation): bool
    {
        if ($conversation->user_id === $user->id) {
            return true;
        }

        if ($user->doctor && $conversation->doctor_id === $user->doctor->id) {
            return true;
        }

        return false;
    }
}
