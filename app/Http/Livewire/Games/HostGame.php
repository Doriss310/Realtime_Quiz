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
    protected $listeners = [
        'echo:game.{session.code},PlayerJoined' => 'handlePlayerJoined',
        'echo:game.{session.code},GameStarted' => 'handleGameStarted',
//        'echo:game.{session.code},AnswerSubmitted' => 'handleAnswer'
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
        $this->validate([
            'playerName' => 'required|min:3',
            ]);

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
        try {
            if (!isset($data['session']) || !isset($data['session']['quiz'])) {
                \Log::error('Invalid game start data structure', ['data' => $data]);
                return;
            }

            $sessionId = $data['session']['id'];
            $quizSlug = $data['session']['quiz']['slug'];

            if (empty($sessionId) || empty($quizSlug)) {
                \Log::error('Missing required game start data', [
                    'sessionId' => $sessionId ?? null,
                    'quizSlug' => $quizSlug ?? null
                ]);
                return;
            }

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
        $this->currentTimer = $this->timer;
        $this->isTimerRunning = true;

        broadcast(new QuestionChanged(
            $this->session,
            $currentQuestion,
            $this->timer,
            $this->currentQuestionIndex
        ));
    }

//    public function nextQuestion()
//    {
//        $this->isTimerRunning = false;
//
//        $questionsCount = $this->session->quiz->questions->count();
//
//        if ($this->currentQuestionIndex + 1 < $questionsCount) {
//            $this->currentQuestionIndex++;
//            $this->startQuestion();
//        }
//    }

//    public function handleAnswer($data)
//    {
//        \Log::info('Received answer data', ['data' => $data]);
//
//        // Kiểm tra xem 'player_id' có tồn tại trong mảng $data['data'] không
//        if (!isset($data['data']['id'])) {
//            \Log::error("Player ID not found in the answer data", ['data' => $data]);
//            return; // Dừng việc xử lý nếu player_id không có
//        }
//
//        // Cập nhật điểm số cho người chơi
//        foreach ($this->players as $player) {
//            if (isset($player['id']) && $player['id'] === $data['data']['id']) {
//                $player['score'] = $data['data']['score'];
//                \Log::info("Player score updated", ['player_id' => $player['id'], 'new_score' => $player['score']]);
//                break;
//            }
//        }
//
//        // Cập nhật vào cơ sở dữ liệu
//        Player::where('id', $data['data']['id'])
//            ->update(['score' => $data['data']['score']]);
//        \Log::info('Player score updated in database', ['player_id' => $data['data']['id']]);
//    }
    public function endGame()
    {
        // Cập nhật trạng thái session
        $this->session->update([
            'status' => 'finished',
        ]);

        // Lấy danh sách người chơi trong session
        $players = Player::where('game_session_id', $this->session->id)->get();

        \Log::info('Players retrieved from database', [
            'players_count' => $players->count(),
            'player_details' => $players->toArray()
        ]);

        // Broadcast kết quả cuối cùng
        broadcast(new GameEnded([
            'session_code' => $this->session->code,
            'final_scores' => $players->map(function ($player) {
                return [
                    'player_id' => $player->id,
                    'player_name' => $player->user->name ?? 'Unknown', // Lấy tên người chơi
                    'score' => $player->score ?? 0  // Mặc định 0 nếu không có điểm
                ];
            })
        ]))->toOthers();
    }

    public function render()
    {
        return view('livewire.games.host-game');
    }
}
