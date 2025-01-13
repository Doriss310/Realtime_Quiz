<?php

namespace App\Http\Controllers\Quiz;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class QuizController extends Controller
{
    public function index(){
        $quizzes = Quiz::all();
        return view('quiz.index', compact('quizzes'));
    }

    public function show(Quiz $quiz)
    {
        return view('front.quizzes.show', compact('quiz'));
    }

    public function showQuestion($questionNumber, $totalQuestions)
    {
        // Lấy danh sách các question ID được lưu trong session
        $questionIds = session('question_ids');

        // Kiểm tra nếu không có id, thì có thể là đang truy cập trực tiếp hoặc có lỗi
        if (!$questionIds || !is_array($questionIds)) {
            Log::error("Error in showQuestion: question_ids not found in session or is not an array.");
            // Redirect về trang chọn quiz
            return redirect()->route('quiz.index');
        }
        // Chắc chắn rằng questionNumber hợp lệ
        if ($questionNumber <= 0 || $questionNumber > $totalQuestions) {
            Log::error("Error in showQuestion: invalid question number: {$questionNumber} of {$totalQuestions}.");
            // Nếu số câu hỏi không hợp lệ, redirect về trang quiz
            return redirect()->route('quiz.index');
        }

        // Lấy ID câu hỏi cần hiển thị từ session
        $currentQuestionId = $questionIds[$questionNumber - 1];
        // Lấy câu hỏi từ database
        $question = Question::find($currentQuestionId);
        // Kiểm tra nếu không có câu hỏi
        if (!$question) {
            Log::error("Error in showQuestion: Question with id: {$currentQuestionId} not found.");
            return redirect()->route('quiz.index');
        }

        // Tạo danh sách các đáp án
        $options = $question->options;
        // Trộn đáp án để hiển thị ngẫu nhiên

        // Truyền dữ liệu vào view
        return view('questions.index', [
            'question' => $question,
            'questionNumber' => $questionNumber,
            'totalQuestions' => $totalQuestions,
            'options' => $options,
        ]);
    }

    public function startQuiz(Request $request){

        // Lấy độ dài quiz từ request (form)
        $length = $request->input('length');
        // lấy tất cả questions
        $allQuestions = Question::all();
        // Kiểm tra độ dài quiz
        if($length === 'all'){
            $questions = $allQuestions;
            $length = count($questions);
        } else {
            // Tạo bộ random các câu hỏi.
            $questions = $allQuestions->random($length);
        }
        // Lấy danh sách ID câu hỏi
        $questionIds = $questions->pluck('id')->toArray();
        // Lưu danh sách ID vào session
        session(['question_ids' => $questionIds]);

        // Chuyển hướng đến câu hỏi đầu tiên
        return redirect()->route('question.show', ['questionNumber' => 1, 'totalQuestions' => $length]);
    }
}
