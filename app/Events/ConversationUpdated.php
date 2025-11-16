<?php

namespace App\Events;

use App\Models\Conversation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConversationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $conversation;

    public function __construct(Conversation $conversation)
    {
        $this->conversation = $conversation->load(['buyer', 'seller', 'product']);
    }

    public function broadcastOn()
    {
        return [
            new PrivateChannel('user.' . $this->conversation->buyer_id),
            new PrivateChannel('user.' . $this->conversation->seller_id),
        ];
    }

    public function broadcastAs()
    {
        return 'conversation.updated';
    }

    public function broadcastWith()
    {
        return [
            'conversation' => [
                'id' => $this->conversation->id,
                'last_message' => $this->conversation->last_message,
                'last_message_at' => $this->conversation->last_message_at?->toISOString(),
                'buyer_unread_count' => $this->conversation->buyer_unread_count,
                'seller_unread_count' => $this->conversation->seller_unread_count,
            ],
        ];
    }
}
