<?php
namespace App\Http\Livewire\Games;

use App\Events\GameEnded;
use App\Events\GameStarted;
use App\Events\QuestionChanged;
use App\Models\GameSession;
use App\Models\Player;
use Livewire\Component;
use App\Models\Quiz;

class HostGame extends Component
{
    public $session;
    public $players = [];
    public $quiz;
    public $currentQuestionIndex = 0;  // -1 là đang ở waiting room
    public $timer = 20;  // Thời gian cho mỗi câu hỏi
    public $currentTimer = 0;
    public $isTimerRunning = false;
    public $playerName = '';
    public $playerId = null;
    public $score = 0;
    public $enableTimer = true;
    public $customTimer = 20;
    protected $listeners = [
        'echo:game.{session.code},PlayerJoined' => 'handlePlayerJoined',
        'echo:game.{session.code},GameStarted' => 'handleGameStarted',
    ];
    protected $rules = [
        'playerName' => 'required|min:3',
        'customTimer' => 'required|integer|min:5|max:120',
    ];

    public function mount(Quiz $quiz, GameSession $session): void
    {
        $this->quiz = $quiz;
        $this->players = $session->players()->get()->toArray();

        // Tạo game session mới
        $this->session = GameSession::create([
            'quiz_id' => $this->quiz->id,
            'host_id' => auth()->id(),
            'status' => 'waiting',
            'timer_limit' => $this->customTimer,
            'timer_enabled' => $this->enableTimer,
        ]);
    }

    public function handlePlayerJoined($data)
    {
        $this->players[] = [
            'name' => $data['name'],
            'score' => $data['score'],
        ];
    }

    public function createPlayer(){
        $this->validate();

        $player = $this->session->players()->create([
            'user_id' => auth()->id(),
            'name' => $this->playerName,
            'score' => $this->score
        ]);

        $this->session->update([
            'timer_limit' => $this->enableTimer ? $this->customTimer : null,
            'timer_enabled' => $this->enableTimer
        ]);

        $this->players[] = [
            'name' => $player->name,
            'score' => $player->score,
        ];
        $this->playerId = $player->id;
        $this->timer = $this->customTimer;
    }

    public function handleGameStarted($data)
    {
        try {
            $sessionId = $data['session']['id'];
            $quizSlug = $data['session']['quiz']['slug'];

            $url = route('game.play', [
                'session' => $sessionId,
                'quiz' => $quizSlug,
                'playerId' => $this->playerId
            ]);

            \Log::info('Redirecting to quiz', ['url' => $url]);
            $this->redirect($url);

        } catch (\Exception $e) {
            \Log::error('Error handling game start', [
                'error' => $e->getMessage(),
                'sessionId' => $this->session->id
            ]);
        }
    }

    public function startGame()
    {
        $this->session->update(['status' => 'playing']);
        $this->currentQuestionIndex = 0;

        broadcast(new GameStarted($this->session));
        $this->startQuestion();
    }

    public function startQuestion()
    {
        if (!$this->quiz) return;

        $currentQuestion = $this->quiz->questions[$this->currentQuestionIndex];
        $this->currentTimer = $this->enableTimer ? $this->timer : null;
        $this->isTimerRunning = $this->enableTimer;

//        broadcast(new QuestionChanged(
//            $this->session,
//            $currentQuestion,
//            $this->currentTimer,
//            $this->currentQuestionIndex
//        ));
    }
    public function render()
    {
        return view('livewire.games.host-game');
    }
}
