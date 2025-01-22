<?php

namespace App\Events;

use App\Models\GameSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LoadSession implements ShouldBroadcast
{
    use SerializesModels;

    public $session;

    public function __construct(GameSession $session)
    {
        $this->session = $session;
    }

    public function broadcastOn()
    {
        // Đảm bảo kênh game.{gameCode} được tạo ra
        return new Channel('game.' . $this->session->code);
    }
}
