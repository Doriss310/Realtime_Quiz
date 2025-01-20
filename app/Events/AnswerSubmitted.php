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
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $gameSession;
    public $player;
    public $answer;
    public $score;

    public function __construct(GameSession $gameSession, Player $player, $answer, $score)
    {
        $this->gameSession = $gameSession;
        $this->player = $player;
        $this->answer = $answer;
        $this->score = $score;
    }

    public function broadcastOn()
    {
        return new Channel('game.' . $this->gameSession->code);
    }

    public function broadcastWith()
    {
        return [
            'player' => [
                'id' => $this->player->id,
                'name' => $this->player->name
            ],
            'answer' => $this->answer,
            'score' => $this->score
        ];
    }
}
