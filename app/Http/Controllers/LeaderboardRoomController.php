<?php

namespace App\Http\Controllers;

use App\Models\GameSession;

class LeaderboardRoomController extends Controller
{
    public function show(GameSession $session)
    {

        $sessionId = session('leaderboard_session_id');
        $session = GameSession::find($sessionId);
        $players = $session->players()->orderBy('score', 'desc')->get();

        return view('livewire.games.leaderboardroom', compact('players', 'session'), [
            'session' => $session,
            'players' => $players,
        ]);
    }
}
