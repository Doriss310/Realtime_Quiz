<?php

namespace Database\Seeders;

use App\Models\Option;
use Database\Factories\OptionFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Danh sách câu hỏi
        $questions = [
            'What is HTML',
            'What is CSS',
            'What is JavaScript',
            'What is Accessibility',
            'What is General CS',
            'What is IT',
            'What is Linux',
            'What is Python',
            'What is SQL',
            'Whatttttt',
        ];

        for($i = 0; $i < count($questions); $i++) {
        foreach ($questions as $question) {
            DB::table('questions')->insert([
                'text' => $question,
                'code_snippet' => '',

            ]);


        }
    }}
}
