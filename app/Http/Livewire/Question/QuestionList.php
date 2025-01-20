<?php

namespace App\Http\Livewire\Question;

use App\Models\Question;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

class QuestionList extends Component
{
    public function delete(int $id)
    {
        abort_if(!auth()->user()->is_admin, Response::HTTP_FORBIDDEN, '403');

        $question = Question::findOrFail($id);
        $question->delete();
    }

    public function render()
    {
        $questions = Question::latest()->paginate();

        // Trả về một view với dữ liệu
        return view('livewire.question.qusetion-list', compact('questions'));
    }
}
