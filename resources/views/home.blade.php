<x-app-layout>
    <div id="root">

        @include('layouts.navigation')
        @vite('resources/css/app.css')
        <div class="hero-container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1>Trắc nghiệm học lập trình</h1>
                    <h2>Thực hành với hơn <strong class="question-count">1200+</strong> Câu hỏi</h2>
                </div>
                <div class="hero-button">
                    <a class="btn-default btn--link large-btn" href="{{route('quiz.index')}}">Bắt đầu(chơi đơn)</a>
                    <a class="btn-default btn--link large-btn" href="http://127.0.0.1:8000/game/">Tạo phòng</a>

                </div>
            </div>
        </div>
        <main class="text-center">
            <hr class="featurette-divider" id="divider">
            <div class="row featurette content-row-container" style="background-color: rgb(10, 10, 35); margin: 0px;">
                <div class="col-md-7 content-text-container">
                    <h2 class="featurette-heading">Bạn có muốn kiểm tra kiến thức của mình?</h2>
                    <p class="lead">Trau dồi kiến thức về lập trình của bạn với hơn 1200+ câu hỏi.</p>
                    <a class="btn-default btn--link " href="{{route('quiz.index')}}">Bắt đầu</a>
                </div>
                <div class="col-md-5 content-img-container">
                    <img src="/assets/main-character-0-XZwldw.webp" class="img-fluid rounded content-section-img"
                         id="main-character-img" alt="main female character from rpg game"></div>
            </div>
            <hr class="featurette-divider" id="divider">
            <div class="row featurette content-row-container" style="background-color: rgb(42, 42, 64); margin: 0px;">
                <div class="col-md-7 order-md-2 content-text-container"><h2 class="featurette-heading">Bạn mới làm quen với lập trình?</h2>
                    <p class="lead">Học lập trình miễn phí và bắt đầu hành trình lập trình của bạn với <a target="_blank"
                                                                                                      rel="noopener noreferrer"
                                                                                                      href="http://127.0.0.1:8000/">chúng tôi</a>.
                    </p></div>
                <div class="col-md-5 order-md-1 content-img-container"><img src="/assets/fcc_background-qAq3suH_.webp"
                                                                            class="img-fluid rounded" id="#fcc-image"
                                                                            alt="freeCodeCamp rpg logo"></div>
            </div>
            <hr class="featurette-divider" id="divider">
            <div class="row featurette content-row-container" style="background-color: rgb(10, 10, 35); margin: 0px;">
                <div class="col-md-7 order-md-2 content-text-container"><h2 class="featurette-heading"
                                                                            style="margin-top: 40px;">Bạn muốn học lập trình khi đang chơi trò chơi?</h2>
                    <p class="lead"> Hãy thử <br> <a
                            href="http://127.0.0.1:8000/" target="_blank"
                            rel="noopener noreferrer">Learn to Code RPG Game</a></p>
                    <p class="lead">Có thể tải xuống miễn phí trên <br> Windows, Mac và Linux.</p></div>
                <div class="col-md-5 order-md-2 content-img-container"><img src="/assets/rpg-menu-DR_IzDuY.webp"
                                                                            class="img-fluid rounded"
                                                                            alt="freeCodeCamp rpg logo"></div>
            </div>
        </main>
    </div>
</x-app-layout>
