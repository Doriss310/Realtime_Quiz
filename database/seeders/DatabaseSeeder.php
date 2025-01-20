<?php

namespace Database\Seeders;

    use App\Models\User;
    use Illuminate\Database\Seeder;
    use App\Models\Quiz;
    use App\Models\Question;
    use App\Models\Option;
    use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->admin()->create();
        // Create 5 quizzes
//        Quiz::factory(5)->create()->each(function ($quiz) {
//            // Create 50 questions for each quiz
//            $questions = Question::factory(50)->create();
//
//            $questions->each(function ($question) use ($quiz) {
//                // Attach question to quiz
//                $quiz->questions()->attach($question);
//
//                // Create 4 options for each question
//                Option::factory(4)->create([
//                    'question_id' => $question->id,
//                ]);
//            });
//        });
    }
}
