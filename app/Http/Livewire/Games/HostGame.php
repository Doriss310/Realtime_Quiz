<?php
namespace App\Http\Livewire\Games;

use App\Events\GameStarted;
use App\Models\GameSession;
use Livewire\Component;
use App\Models\Quiz;

class HostGame extends Component
{
    public $session;
    public $players = [];
    public $quiz;
    public $currentQuestionIndex = 0;  // -1 là đang ở waiting room
    public $timer = 20;  // Thời gian cho mỗi câu hỏi
    public $playerName = '';
    public $playerId = null;
    public $score = 0;
    protected $listeners = [
        'echo:game.{session.code},PlayerJoined' => 'handlePlayerJoined',
        'echo:game.{session.code},GameStarted' => 'handleGameStarted',
    ];
    protected $rules = [
        'playerName' => 'required|min:3',
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


        $this->players[] = [
            'name' => $player->name,
            'score' => $player->score,
        ];
        $this->playerId = $player->id;
    }

    public function handleGameStarted($data)
    {
            $sessionId = $data['session']['id'];
            $quizSlug = $data['session']['quiz']['slug'];

            $url = route('game.play', [
                'session' => $sessionId,
                'quiz' => $quizSlug,
                'playerId' => $this->playerId
            ]);

            $this->redirect($url);
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

    }
    public function render()
    {
        return view('livewire.games.host-game');
    }
}
