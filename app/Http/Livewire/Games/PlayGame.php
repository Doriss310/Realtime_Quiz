<?php

namespace App\Http\Livewire\Games;

use App\Events\PlayerJoined;
use App\Models\GameSession;
use App\Models\Player;
use Illuminate\Http\Request;
use Livewire\Component;
use Illuminate\Support\Collection;

class PlayGame extends Component
{
    public $gameCode = '';
    public $playerName = '';
    public $sessionId = null;
    public $session = null;
    public $sessionStatus = 'waiting'; // Thêm
    public $playerId = null;
    public $score = 0;

    protected $listeners = [
        'echo:game.{session.code},GameStarted' => 'handleGameStart',
        ];

    public function mount(Request $request, int $sessionId = null): void
    {
        if (isset($sessionId)) {
            $this->session = GameSession::where('id', $sessionId)->firstOrFail();
        }

        if ($request->query('playerId')) {
            $this->playerId = $request->query('playerId');
        }
    }

    public function join()
    {
        $this->validate([
            'gameCode' => 'required|exists:game_sessions,code',
            'playerName' => 'required|min:3'
        ]);

        $session = GameSession::where('code', $this->gameCode)->firstOrFail();

        $player = $session->players()->create([
            'user_id' => auth()->id(),
            'name' => $this->playerName,
            'score' => $this->score
        ]);

        $this->sessionId = $session->id;
        $this->sessionStatus = $session->status;
        $this->playerId = $player->id;

        broadcast(new PlayerJoined($session, $player));
        return redirect()->route('game.join', ['sessionId' => $session->id, 'playerId' => $this->playerId]);
    }

    public function handleGameStart($data)
    {

            $sessionId = $data['session']['id'];
            $quizSlug = $data['session']['quiz']['slug'];

            $url = route('game.play', [
                'session' => $sessionId,
                'quiz' => $quizSlug,
                'playerId' => $this->playerId
            ]);


            $this->emit('redirectToQuiz', $url);
            $this->redirect($url);
    }

    public function render()
    {
        return view('livewire.games.play-game', ['session' => $this->session]);
    }
}
