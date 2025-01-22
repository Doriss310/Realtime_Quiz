<?php
namespace App\Http\Livewire\Games;

use App\Events\GameEnded;
use App\Events\GameStarted;
use App\Events\LoadSession;
use App\Events\QuestionChanged;
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
    public $currentTimer = 0;
    public $isTimerRunning = false;
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
        $this->startQuestion();
    }

    public function startQuestion()
    {
        if (!$this->quiz) return;

        $currentQuestion = $this->quiz->questions[$this->currentQuestionIndex];
        $this->currentTimer = $this->timer;
        $this->isTimerRunning = true;

        broadcast(new QuestionChanged(
            $this->session,
            $currentQuestion,
            $this->timer,
            $this->currentQuestionIndex
        ))->toOthers();

        $this->startTimer();
    }

    public function startTimer(){
        $this->currentTimer = $this->timer;
        $this->isTimerRunning = true;

        $this->dispatchBrowserEvent('startTimer', [
            'duration' => $this->timer,
            'sessionCode' => $this->session->code,
        ]);
    }


    public function nextQuestion()
    {
        $this->isTimerRunning = false;

        $questionsCount = $this->session->quiz->questions->count();
        \Log::info('Total Questions: ', ['count' => $questionsCount]);
        \Log::info('Current Question Index Before Increment: ', ['index' => $this->currentQuestionIndex]);

        if ($this->currentQuestionIndex + 1 < $questionsCount) {
            $this->currentQuestionIndex++;
            \Log::info('Current Question Index After Increment: ', ['index' => $this->currentQuestionIndex]);
            $this->startQuestion();
        } else {
            \Log::info('Game Ending Triggered.');
            $this->endGame();
        }
    }

    public function handleAnswer($data)
    {
        // Cập nhật điểm số cho người chơi
        foreach ($this->players as &$player) {
            if ($player['id'] === $data['player_id']) {
                $player['score'] += $data['score'];
                break;
            }
        }
    }

    public function endGame()
    {
        // Cập nhật trạng thái session
        $this->session->update([
            'status' => 'finished',
            'ended_at' => now()
        ]);

        // Broadcast event kết thúc game
        broadcast(new GameEnded([
            'session_code' => $this->session->code,
            'final_scores' => $this->players
        ]))->toOthers();

        // Chuyển hướng về trang leaderboard hoặc kết quả
//        return redirect()->route('leaderboard.show', ['session' => $this->session->id]);
//        return redirect()->route('game.results', [
//            'session' => $this->session->code
//        ]);
    }

    public function render()
    {
        return view('livewire.games.host-game');
    }
}
