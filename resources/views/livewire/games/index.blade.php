<x-app-layout>
    <div id="root">
        <a class="btn-default btn--link " href="/">Trang chủ</a><img class="fcc-logo"
                                                                     src="/assets/fcc_primary_large-BET8Cs_b.webp"
                                                                     alt="freeCodeCamp logo">
        <div class="select-quiz-styles">
            <h2 class="quiz-heading">Chọn một chủ đề</h2>
            <div class="select-btn-div">
                @foreach($quizzes as $quiz)
                    <form action="{{ route('game.host', ['quiz' => $quiz->slug]) }}" method="GET">
                        <button class="select-btns" type="submit">{{$quiz->title}}</button>
                    </form>
                @endforeach
                @if(empty($quiz))
                    <h5 style="margin-top: 8rem;margin-left: 4rem;">Không tìm thấy chủ đề nào.</h5>
                @endif
            </div>
        </div>
    </div>


</x-app-layout>
