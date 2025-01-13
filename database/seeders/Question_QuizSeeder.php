<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Question_QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $quizCount = 10; // Số lượng quiz
        $questionsPerQuiz = 10; // Số câu hỏi mỗi quiz
        $questionStartId = 1; // ID bắt đầu của câu hỏi

        $data = [];

        for ($quizId = 1; $quizId <= $quizCount; $quizId++) {
            for ($i = 0; $i < $questionsPerQuiz; $i++) {
                $data[] = [
                    'question_id' => $questionStartId + $i,
                    'quiz_id' => $quizId,
                ];
            }

            // Tăng questionStartId để các quiz không dùng lại câu hỏi trước đó
            $questionStartId += $questionsPerQuiz;
        }

        DB::table('question_quiz')->insert($data);
    }
}
