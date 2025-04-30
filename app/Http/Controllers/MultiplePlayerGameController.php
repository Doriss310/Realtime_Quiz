<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MultiplePlayerGameController extends Controller
{
    public function show(Quiz $quiz)
    {
        return view('front.games.played', compact('quiz'));
    }

    public function index()
    {
        $quizzes = Quiz::all();
        return view('livewire.games.index', compact('quizzes'));
    }
}
