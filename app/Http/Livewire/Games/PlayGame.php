<?php

namespace App\Http\Livewire\Games;

use App\Events\PlayerJoined;
use App\Models\GameSession;
use App\Models\Player;
use Livewire\Component;
use Illuminate\Support\Collection;

class PlayGame extends Component
{
    public $gameCode = '';
    public $playerName = '';
    public $sessionId = null;
    public $session;
    public $sessionStatus = 'waiting'; // Thêm
    public $playerId = null;

    protected $listeners = [
        'echo:game.{session.code},GameStarted' => 'handleGameStart'
    ];

    public function mount(GameSession $session)
    {
        $this->session = $session;
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
            'name' => $this->playerName
        ]);

        $this->sessionId = $session->id;
        $this->sessionStatus = $session->status;
        $this->playerId = $player->id;

        broadcast(new PlayerJoined($session, $player));
        return redirect()->route('game.join', ['session' => $session->id]);

    }

    public function handleGameStart($data)
    {
        if (isset($data['session']) && isset($data['session']['quiz'])) {
            $url = route('quiz.show', ['quiz' => $data['session']['quiz']['slug']]);
            \Log::info('Redirect URL', ['url' => $url]);

            $this->emit('redirectToQuiz', $url);
            $this->redirect($url);

        } else {
            \Log::error('Quiz not found or invalid session', ['sessionId' => $this->sessionId]);
        }
    }

    public function render()
    {
        return view('livewire.games.play-game', ['session' => $this->session]);
    }
}
