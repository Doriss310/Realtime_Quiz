<?php

namespace App\Events;

use App\Models\GameSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class GameStarted implements ShouldBroadcast
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
