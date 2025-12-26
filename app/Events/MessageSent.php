<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Message $message)
    {
        $this->message->load('sender.doctor');
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('conversation.' . $this->message->conversation_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        $sender_image = null;

        if ($this->message->sender->doctor?->image) {
            $image = $this->message->sender->doctor->image;

            if (filter_var($image, FILTER_VALIDATE_URL)) {
                $sender_image = $image;
            } elseif (str_starts_with($image, 'doctors/')) {
                $sender_image = asset('storage/' . $image);
            } else {
                $sender_image = asset($image);
            }
        }
        return [
            'id' => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender->name,
            'sender_image' => $sender_image,
            'message' => $this->message->message,
            'is_read' => (bool) $this->message->is_read,
            'created_at' => $this->message->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
