<?php

namespace App\Http\Controllers;

use App\Models\GameSession;

class LeaderboardRoomController extends Controller
{
    public function show(GameSession $session)
    {
        $players = $session->players()->orderBy('score', 'desc')->get();

        return view('livewire.games.leaderboardroom', compact('players', 'session'), [
            'session' => $session,
            'players' => $players,
        ]);
    }
}
