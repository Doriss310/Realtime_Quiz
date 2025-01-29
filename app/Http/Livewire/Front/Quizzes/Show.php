<?php

namespace App\Http\Livewire\Front\Quizzes;

use App\Models\Question;
use App\Models\Option;
use App\Models\Quiz;
use App\Models\Test;
use App\Models\Answer;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

class Show extends Component
{
    public Quiz $quiz;
    public Collection $questions;
    public Question $currentQuestion;
    public int $currentQuestionIndex = 0;
    public array $answersOfQuestions = [];
    public int $startTimeInSeconds = 0;
    public $selectedOptions = [];
    public $currentOptions = [];
    public $showFeedback = false;
    public $isCorrect = false;
    public $points = 0;
    public $codeSnippetInput = '';

    protected $listeners = ['nextQuestion'];

    public function mount()
    {
        $this->startTimeInSeconds = now()->timestamp;

        // Lấy các câu hỏi thuộc quiz hiện tại
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

    public function nextQuestion()
    {
        if (!$this->showFeedback) {
            // Kiểm tra đáp án
            $correctOptions = Option::whereIn('id', $this->selectedOptions)
                ->where('correct', true)
                ->count();
            $totalCorrectOptions = $this->currentQuestion->options()
                ->where('correct', true)
                ->count();
            $code_snippet = $this->currentQuestion->code_snippet;

            // Chỉ đúng khi chọn đủ và đúng tất cả các đáp án
            if($this->currentQuestion->code_snippet === ''){
                $this->isCorrect = $correctOptions === $totalCorrectOptions &&
                    count($this->selectedOptions) === $totalCorrectOptions;
            } else {
                $this->isCorrect = $correctOptions === $totalCorrectOptions &&
                    count($this->selectedOptions) === $totalCorrectOptions ||
                    trim($this->codeSnippetInput) === $code_snippet ;
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

        if ($this->currentQuestionIndex >= $this->questionsCount - 1) {
            return $this->submit();
        }

        $this->currentQuestionIndex++;
        $this->currentQuestion = $this->questions[$this->currentQuestionIndex];
        $this->cacheCurrentOptions();

        // Khôi phục các lựa chọn đã chọn trước đó (nếu có)
        $this->selectedOptions = [];
        $this->codeSnippetInput = '';
        $this->isCorrect = false;


    }

    public function submit()
    {
        $result = 0;
        $test = Test::create([
            'user_id' => auth()->id(),
            'quiz_id' => $this->quiz->id,
            'result' => 0,
            'ip_address' => request()->ip(),
            'time_spent' => now()->timestamp - $this->startTimeInSeconds
        ]);

        foreach ($this->answersOfQuestions as $key => $answer) {
            $question = $this->questions[$key];

            // Thay đổi điều kiện kiểm tra
            if (!empty($answer['code_snippet'])) {  // Kiểm tra nếu code_snippet có giá trị
                $code_answer = $answer['code_snippet'];
                $isCorrect = trim($code_answer) === trim($question->code_snippet);
                if ($isCorrect) $result++;
                Answer::create([
                    'user_id' => auth()->id(),
                    'test_id' => $test->id,
                    'question_id' => $question->id,
                    'code_answer' => $code_answer,
                    'correct' => $isCorrect ? 1 : 0
                ]);
            } elseif (!empty($answer['selected_options'])) {  // Kiểm tra nếu có selected_options
                $optionIds = $answer['selected_options'];
                $correctOptions = $question->options()->where('correct', true)->pluck('id')->toArray();

                sort($optionIds);
                sort($correctOptions);

                $isCorrect = $optionIds === $correctOptions;
                if ($isCorrect) $result++;

                foreach ($optionIds as $optionId) {
                    Answer::create([
                        'user_id' => auth()->id(),
                        'test_id' => $test->id,
                        'question_id' => $question->id,
                        'option_id' => $optionId,
                        'correct' => $isCorrect ? 1 : 0
                    ]);
                }
            }
        }
//dd($this->answersOfQuestions);
        $test->update(['result' => $result]);
        return to_route('results.show', ['test' => $test]);
    }

    public function render(): View
    {
        return view('livewire.front.quizzes.show');
    }
}
