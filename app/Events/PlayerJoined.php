<?php

namespace App\Events;

use App\Models\GameSession;
use App\Models\Player;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerJoined implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $session;
    public $player;

    public function __construct(GameSession $session, Player $player)
    {
        $this->session = $session;
        $this->player = $player;
    }

    public function broadcastOn()
    {
        return new Channel('game.' . $this->session->code);
    }

//    public function broadcastAs()
//    {
//        return 'PlayerJoined';
//    }

    public function broadcastWith()
    {
        return [
            'name' => $this->player->name,
            'score' => $this->player->score,
        ];
    }
}
