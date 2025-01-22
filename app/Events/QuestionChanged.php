<?php

namespace App\Events;

use App\Models\GameSession;
use App\Models\Question;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuestionChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $session_id;
    public $question;
    public $timer;
    public $question_index;

    protected $session;

    public function __construct(GameSession $session, Question $question, int $timer, int $questionIndex)
    {
        $this->session = $session;
        $this->session_id = $session->id;
        $this->question = $question;
        $this->timer = $timer;
        $this->question_index = $questionIndex;
    }

    public function broadcastOn()
    {
        if (!$this->session->code) {
            throw new \Exception('Game session code is missing');
        }

        return new Channel('game.' . $this->session->code);
    }

    public function broadcastWith()
    {
        return [
            'session_id' => $this->session_id,
            'question' => $this->question, // Customize fields
            'timer' => $this->timer,
            'question_index' => $this->question_index,
        ];
    }
}
