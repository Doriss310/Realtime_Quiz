<?php

namespace App\Events;

use App\Models\GameSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $session;
    public $quiz;

    public function __construct(GameSession $session)
    {
        $this->session = $session;
        $this->quiz = $session->quiz;
    }

    public function broadcastOn()
    {
        return new Channel('game.' . $this->session->code);
    }

    public function broadcastWith()
    {
        return [
            'session' => $this->session,
            'quiz' => $this->quiz,
            'redirectUrl' => route('quiz.show', ['quiz' => $this->quiz->slug])
        ];
    }
}
