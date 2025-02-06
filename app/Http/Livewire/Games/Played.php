<?php

namespace App\Http\Livewire\Games;

use App\Events\AnswerSubmitted;
use App\Models\GameSession;
use App\Models\Player;
use App\Models\Question;
use App\Models\Option;
use App\Models\Quiz;
use App\Models\Test;
use App\Models\Answer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Livewire\Component;

class Played extends Component
{
    public Quiz $quiz;
    public Collection $questions;
    public Question $currentQuestion;
    public int $currentQuestionIndex = 0;
    public array $answersOfQuestions = [];
    public $selectedOptions = [];
    public $currentOptions = [];
    public $showFeedback = false;
    public $isCorrect = false;
    public $points = 0;
    public $codeSnippetInput = '';
    public $timer = 20;
    public $currentTimer = 0;
    public $isTimerRunning = false;
    public $timerEnabled = true;
    public $playerId = null;
    public $player = null;

    protected $listeners = [
        'echo:game.{session.code},QuestionChanged' => 'handleQuestionChanged',
        'echo:game.{session.code},PlayerJoined' => 'handlePlayerJoined',
        'questionChanged' => 'handleQuestionChanged',
        'timeUp' => 'handleTimeUp'
    ];

    public function mount(Quiz $quiz, GameSession $session, Request $request, int $player = null)
    {
        if ($request->query('playerId')) {
            $this->playerId = $request->query('playerId');
        }

        $this->player = Player::where('id', $this->playerId)->firstOrFail();

        $this->quiz = $quiz;
        $this->session = $session;

        $this->timerEnabled = $session->timer_enabled;
        if($this->timerEnabled){
            $this->timer = $session->timer_limit ?? 20;
        }
        if ($this->timerEnabled) {
            $this->currentTimer = $this->timer;
            $this->isTimerRunning = true;
            $this->startTimer();
        }

        $this->questions = Question::query()
            ->whereHas('quizzes', function ($query) {
                $query->where('id', $this->quiz->id);
            })
            ->with('options')
            ->get();

        if ($this->questions->isEmpty()) {
            abort(404, 'No questions available for this quiz.');
        }

        $this->currentQuestion = $this->questions[$this->currentQuestionIndex];
        $this->cacheCurrentOptions();

        $this->answersOfQuestions = array_fill(0, $this->questionsCount, [
            'selected_options' => [],
            'code_snippet' => '',
            'correct' => false,
        ]);
    }

    public function handleTimeUp()
    {
        if ($this->isTimerRunning) {
            $this->isTimerRunning = false;
            $this->nextQuestion();
        }
    }
    private function cacheCurrentOptions()
    {
        $this->currentOptions = $this->currentQuestion->options->map(function ($option) {
            return [
                'id' => $option->id,
                'text' => $option->text,
                'correct' => $option->correct
            ];
        })->all();
    }

    public function getQuestionsCountProperty(): int
    {
        return $this->questions->count();
    }

    public function selectOption($optionId)
    {
        // Kiểm tra xem optionId đã được chọn chưa
        if (in_array($optionId, $this->selectedOptions)) {
            // Nếu đã chọn rồi thì bỏ chọn
            $this->selectedOptions = array_diff($this->selectedOptions, [$optionId]);
        } else {
            // Nếu chưa chọn thì thêm vào
            $this->selectedOptions[] = $optionId;
        }

        $this->answersOfQuestions[$this->currentQuestionIndex]['selected_options'] = $this->selectedOptions;
    }

    public function handleQuestionChanged($data)
    {
        // Lưu câu trả lời hiện tại
        if ($this->currentQuestionIndex >= 0 && !empty($this->selectedOptions)) {
            $this->saveCurrentAnswer();
        }

        // Cập nhật timer


        // Cập nhật câu hỏi mới
        $this->currentQuestionIndex = $data['question_index'];
        $this->currentQuestion = $this->questions[$this->currentQuestionIndex];

        // Reset trạng thái
        $this->resetQuestionState();

        // Cập nhật options mới
        $this->cacheCurrentOptions();

    }
    private function startTimer()
    {
        if (!$this->timerEnabled) return;

        $this->dispatchBrowserEvent('startTimer', [
            'duration' => $this->timer
        ]);
    }
    private function saveCurrentAnswer()
    {
        $this->answersOfQuestions[$this->currentQuestionIndex] = [
            'selected_options' => $this->selectedOptions,
            'code_snippet' => $this->codeSnippetInput,
            'is_correct' => $this->isCorrect
        ];
    }

    private function resetQuestionState()
    {
        $this->selectedOptions = [];
        $this->codeSnippetInput = '';
        $this->showFeedback = false;
        $this->isCorrect = false;
        if($this->timerEnabled){
            $this->currentTimer = $this->timer;
            $this->isTimerRunning = true;
        }
    }

    public function nextQuestion()
    {
        if (!$this->showFeedback) {
            $correctOptions = Option::whereIn('id', $this->selectedOptions)
                ->where('correct', true)
                ->count();
            $totalCorrectOptions = $this->currentQuestion->options()
                ->where('correct', true)
                ->count();
            $code_snippet = $this->currentQuestion->code_snippet;

            if ($this->currentQuestion->code_snippet === '') {
                $this->isCorrect = $correctOptions === $totalCorrectOptions &&
                    count($this->selectedOptions) === $totalCorrectOptions;
            } else {
                $this->isCorrect = $correctOptions === $totalCorrectOptions &&
                    count($this->selectedOptions) === $totalCorrectOptions ||
                    trim($this->codeSnippetInput) === $code_snippet;
            }
            $this->showFeedback = true;
            return;
        }

        $this->answersOfQuestions[$this->currentQuestionIndex] = [
            'selected_options' => $this->selectedOptions,
            'code_snippet' => $this->codeSnippetInput,
            'is_correct' => $this->isCorrect
        ];

            if ($this->isCorrect) {
                $this->points++;
            }
            $this->showFeedback = false;

            // Lấy player_id từ bảng Player
            $player = Player::where('id', $this->playerId) // Hoặc bạn có thể lấy theo bất kỳ điều kiện nào khác
            ->where('game_session_id', $this->session->id) // Điều kiện cho session
            ->first();

//            if ($player) {
//                // Cập nhật điểm vào bảng Player
//                $player->increment('score', $this->points); // Tăng điểm cho player
//            }
            if ($this->currentQuestionIndex >= $this->questionsCount - 1) {
                $player->update([
                    'score' => $this->points,
                ]);
                return $this->GameEnded();
            }
            $this->currentQuestionIndex++;
            $this->currentQuestion = $this->questions[$this->currentQuestionIndex];
            $this->cacheCurrentOptions();

            // Khôi phục các lựa chọn đã chọn trước đó (nếu có)
            $this->selectedOptions = [];
            $this->codeSnippetInput = '';
            $this->isCorrect = false;

    }

    public function GameEnded(){
        session(['leaderboard_session_id' => $this->session->id]);
        return redirect()->route('leaderboard.show');
    }

    public function render(): View
    {
        return view('livewire.games.played');
    }
}
