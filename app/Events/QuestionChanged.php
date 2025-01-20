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

    public $gameSession;
    public $questionIndex;
    public $question;

    public function __construct(GameSession $gameSession, $questionIndex, Question $question)
    {
        $this->gameSession = $gameSession;
        $this->questionIndex = $questionIndex;
        $this->question = $question;
    }

    public function broadcastOn()
    {
        return new Channel('game.' . $this->gameSession->code);
    }

    public function broadcastWith()
    {
        return [
            'questionIndex' => $this->questionIndex,
            'question' => [
                'text' => $this->question->text,
                'options' => $this->question->options->map(function($option) {
                    return [
                        'id' => $option->id,
                        'text' => $option->text
                    ];
                })
            ]
        ];
    }
}

