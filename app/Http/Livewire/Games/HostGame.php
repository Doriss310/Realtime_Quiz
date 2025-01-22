<?php
namespace App\Http\Livewire\Games;

use App\Events\GameStarted;
use App\Events\LoadSession;
use App\Models\GameSession;
use Livewire\Component;
use App\Models\Quiz;

class HostGame extends Component
{
    public $session;
    public $players = [];
    public $quiz;
    public $currentQuestionIndex = -1;  // -1 là đang ở waiting room
    public $timer = 20;  // Thời gian cho mỗi câu hỏi

    protected $listeners = [
        'echo:game.{session.code},PlayerJoined' => 'handlePlayerJoined',
        'echo:game.{session.code},AnswerSubmitted' => 'handleAnswer'
    ];

    public function mount(Quiz $quiz, GameSession $session)
    {
        $this->quiz = $quiz;
        $this->session = $session;

        $this->players = $session->players()->get()->toArray();
        // Tạo game session mới
        $this->session = GameSession::create([
            'quiz_id' => $this->quiz->id,
            'host_id' => auth()->id(),
            'status' => 'waiting',
        ]);
    }

    public function handlePlayerJoined($data)
    {
        $this->players[] = [
            'name' => $data['name'],
            'score' => $data['score'],
        ];
    }

    public function startGame()
    {
        $this->session->update(['status' => 'playing']);
        $this->currentQuestionIndex = 0;

        broadcast(new GameStarted($this->session));
    }


    public function nextQuestion()
    {
        if ($this->currentQuestionIndex < $this->session->quiz->questions->count() - 1) {
            $this->currentQuestionIndex++;
            $this->emit('QuestionChanged', [
                'index' => $this->currentQuestionIndex
            ]);
        } else {
            $this->endGame();
        }
    }

    public function render()
    {
        return view('livewire.games.host-game');
    }
}
