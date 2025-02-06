@if($timerEnabled)
    <div x-data="{
    secondsLeft: {{$session->timer_limit}},
    selectedAnswer: null
}"
         x-init="setInterval(() => {
        if (secondsLeft > 1) { secondsLeft--; } else {
            secondsLeft = {{$session->timer_limit}};
            $wire.nextQuestion();
        }
    }, 1000);">
@endif

    <div id="root">
        @vite('resources/css/app.css')
        <div>
            <a class="btn-default btn--link" href="/">Trang chủ</a>
            @if($timerEnabled)
            <h2 class="text-2xl" style="text-align: center">Thời gian: <span x-text="secondsLeft"></span></h2>
            @endif
        </div>
        <div class="quiz-container">
            <div>
                <p>Player: {{$player->name}}</p>
            </div>
            <div class="quiz-text text-white">
                <p>Câu hỏi {{ $currentQuestionIndex + 1 }} / {{ $this->questionsCount }}</p>

                <p>Điểm: {{ $points }}</p>
            </div>
            <div></div>

            <h2 class="mb-4 text-2xl" style="text-align: center">Câu hỏi {{ $currentQuestionIndex + 1 }}</h2>

            <div class="quiz-div">
                <fieldset class="quiz-answers-div text-black">
                    <legend class="text-white">{{$currentQuestion->text}}</legend>
                    @foreach ($currentOptions as $option)
                        <button
                            wire:key="static-{{ $currentQuestionIndex }}-{{ $option['id'] }}"
                            wire:click="selectOption({{ $option['id'] }})"
                            class="answers-btns {{ in_array($option['id'], $selectedOptions) ? 'answers-btns--selected' : '' }}"
                            @if($showFeedback) disabled @endif>
                            <div class="flex text-center">
                                {{ $option['text'] }}
                            </div>
                        </button>
                    @endforeach
                    @if($currentQuestion->code_snippet !== null)
                        <div class="code-snippet-container">
                            <label class="block text-sm font-medium text-white mb-2">
                                Hoặc nhập câu trả lời:
                            </label>
                            <textarea
                                wire:model="codeSnippetInput"
                                class="w-full h-25 p-3 border rounded-md font-mono"
                                placeholder="Nhập câu trả lời ở đây"
                                @if($showFeedback) disabled @endif
                    ></textarea>
                        </div>
                    @endif
                    @if ($currentQuestionIndex < $this->questionsCount - 1)
                        <button class="select-btns submit-btn"
                                wire:click="nextQuestion"
                                @if($showFeedback) disabled @endif>
                            Tiếp tục
                        </button>

                    @else
                        <button class="select-btns submit-btn"
                                x-on:click="$wire.nextQuestion();">
                            Submit
                        </button>
                    @endif

                    @if ($showFeedback)
                        <div class="feedback-modal">
                            <div class="feedback-content {{ $isCorrect ? 'correct' : 'incorrect' }}">
                                <div class="feedback-header" style="color: {{$isCorrect ? 'green' : 'red'}}">
                                    @if ($isCorrect)
                                        <span class="emoji">✅</span> Chính xác!
                                    @else
                                        <span class="emoji">😔</span> Sai rồi!
                                    @endif
                                </div>
                                <div class="feedback-details">
                                    @if($isCorrect)
                                        <p>Điểm: {{ $points }}</p>
                                    @else
                                        <p>Điểm: {{ $points }}</p>
                                    @endif
                                    @if($this->codeSnippetInput == '')
                                        <p class="font-bold">Đáp án của bạn:</p>
                                        @foreach($selectedOptions as $selectedId)
                                            <p>{{ $currentOptions[array_search($selectedId, array_column($currentOptions, 'id'))]['text'] }}</p>
                                        @endforeach

                                        <p class="font-bold">Đáp án đúng:</p>
                                        @foreach($currentOptions as $option)
                                            @if($option['correct'])
                                                <p>{{ $option['text'] }}</p>
                                            @endif
                                        @endforeach
                                    @else
                                        <p class="font-bold">Đáp án của bạn:</p>
                                        <pre class="code-preview">{{trim($this->codeSnippetInput)}}</pre>

                                        <p class="font-bold">Đáp án đúng:</p>
                                        <pre class="code-preview">{{ $currentQuestion->code_snippet }}</pre>
                                    @endif
                                        <button type="button"
                                                class="next-question-btn"
                                                wire:click="nextQuestion">
                                            Next Question
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                </fieldset>
            </div>
        </div>
    </div>
        @if($timerEnabled)
    </div>
      @endif
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.Echo.channel('game.{{ $session->code }}')
                .listen('QuestionChanged', (event) => {
                    console.log('QuestionChanged:', event);
                    // Bạn có thể thêm logic để cập nhật danh sách người chơi
                @this.call('handleQuestionChanged', event);
                })
            .listen('GameEnded', (event) => {
                console.log('GameEnded:', event);

                @this.call('GameEnded', event);
            })
                .listen('AnswerSubmitted', (event) => {
                    console.log('Player answered correctly:', event);
                    // Cập nhật UI của người chơi ở frontend, như điểm số
                })
                .listen('PlayerInitialized', (event) => {
                    console.log('PlayerInfo:', event);
                })
        });
    </script>
{{--</div>--}}

<style>
    .answers-btns {
        width: 100%;
        padding: 1rem;
        margin: 0.5rem 0;
        border-radius: 0.5rem;
        background: white;
        color: black;
        transition: all 0.3s ease;
        text-align: left;
    }

    .answers-btns--selected {
        background: #ffd700;
        color: black;
        transform: scale(1.02);
    }

    .checkbox-container {
        margin-right: 1rem;
        display: inline-flex;
        align-items: center;
    }

    .checkbox {
        width: 20px;
        height: 20px;
        border: 2px solid #666;
        border-radius: 4px;
        position: relative;
    }

    .checkbox.checked::after {
        content: '✓';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: #000;
    }

    .radio {
        width: 20px;
        height: 20px;
        border: 2px solid #666;
        border-radius: 50%;
        position: relative;
    }

    .radio.checked::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 10px;
        height: 10px;
        background: #000;
        border-radius: 50%;
    }

    .code-input-container {
        margin: 1rem 0;
    }

    .code-input {
        font-family: monospace;
        background: #f8f8f8;
    }

    .code-preview {
        background: #f8f8f8;
        padding: 1rem;
        border-radius: 4px;
        font-family: monospace;
        margin: 0.5rem 0;
        white-space: pre-wrap;
    }

    /* Existing styles... */
    .feedback-modal {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        width: 90%;
        max-width: 500px;
    }

    .feedback-content {
        text-align: left;
    }

    .feedback-header {
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 1rem;
    }

    .feedback-details {
        margin: 1rem 0;
    }

    .next-question-btn {
        background: #98c379;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        float: right;
        margin-top: 1rem;
    }

    .emoji {
        font-size: 1.5rem;
        margin-right: 0.5rem;
    }
</style>
