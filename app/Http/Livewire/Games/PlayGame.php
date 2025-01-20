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
    }

    public function handleGameStart($data)
    {
        // Chuyển đến trang quiz.show khi game bắt đầu
        $session = GameSession::find($this->sessionId);

            return redirect()->route('quiz.show', ['quiz' => $session->quiz->slug]);
//        } else {
//            session()->flash('error', 'Quiz not found');
//            return redirect()->route('game.join');
//        }
    }

    public function render()
    {
        return view('livewire.games.play-game', ['session' => $this->session]);
    }
}
