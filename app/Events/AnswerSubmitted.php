<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\GameSession;
use App\Models\Player;
class AnswerSubmitted implements ShouldBroadcast
{
    public $session_id;
    public $player_id;
    public $answer_id;
    public $score;
    public $time_taken;

    public function __construct($data)
    {
        $this->session_id = $data['session_id'];
        $this->player_id = $data['player_id'];
        $this->answer_id = $data['answer_id'];
        $this->score = $data['score'];
        $this->time_taken = $data['time_taken'];
    }

    public function broadcastOn()
    {
        return new PrivateChannel('game.' . $this->session_id);
    }
}
