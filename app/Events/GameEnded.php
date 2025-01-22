<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameEnded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $session_code;
    public $final_scores;

    public function __construct($data)
    {
        $this->session_code = $data['session_code'];
        $this->final_scores = $data['final_scores'];
    }

    public function broadcastOn()
    {
        return new Channel('game.' . $this->session_code);
    }
}
