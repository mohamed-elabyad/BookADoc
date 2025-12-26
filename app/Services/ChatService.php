<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\User;

class ChatService
{
    public function markMessagesAsRead(Conversation $conversation, User $user): void
    {
        $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function unreadCount(Conversation $conversation, User $user): int
    {
        return $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->count();
    }
}
