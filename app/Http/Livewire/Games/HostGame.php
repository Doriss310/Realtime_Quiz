<?php
namespace App\Http\Livewire\Games;

use App\Events\GameEnded;
use App\Events\GameStarted;
use App\Events\LoadSession;
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
        \Log::info('Received answer data', ['data' => $data]);

        // Kiểm tra xem 'player_id' có tồn tại trong mảng $data['data'] không
        if (!isset($data['data']['player_id'])) {
            \Log::error("Player ID not found in the answer data", ['data' => $data]);
            return; // Dừng việc xử lý nếu player_id không có
        }

        // Cập nhật điểm số cho người chơi
        foreach ($this->players as $player) {
            if (isset($player['id']) && $player['id'] === $data['data']['player_id']) {
                $player['score'] += $data['data']['score'];
                \Log::info("Player score updated", ['player_id' => $player['id'], 'new_score' => $player['score']]);
                break;
            }
        }

        // Cập nhật vào cơ sở dữ liệu
        Player::where('id', $data['data']['player_id'])->update('score', $data['data']['score']);
        \Log::info('Player score updated in database', ['player_id' => $data['data']['player_id']]);
    }
    public function endGame()
    {
        // Cập nhật trạng thái session
        $this->session->update([
            'status' => 'finished',
        ]);

        // Broadcast kết quả cuối cùng với điểm của tất cả người chơi
        $players = Player::whereIn('id', array_column($this->players, 'id'))->get(); // Hoặc từ bảng users nếu cần

        broadcast(new GameEnded([
            'session_code' => $this->session->code,
            'final_scores' => $players->map(function ($player) {
                return [
                    'player_id' => $player->id,
                    'score' => $player->score
                ];
            })
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
