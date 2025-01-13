<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy danh sách question_id từ bảng questions
        $questionIds = DB::table('questions')->pluck('id');

        // Mảng dữ liệu cho từng option
        $options = [];

        foreach ($questionIds as $questionId) {
            $options[] = [
                'text' => 'Option 1 for question ',
                'correct' => true,
                'question_id' => $questionId,
            ];
            $options[] = [
                'text' => 'Option 2 for question ',
                'correct' => false,
                'question_id' => $questionId,
            ];
            $options[] = [
                'text' => 'Option 3 for question ',
                'correct' => false,
                'question_id' => $questionId,
            ];
            $options[] = [
                'text' => 'Option 4 for question ',
                'correct' => false,
                'question_id' => $questionId,
            ];
        }

        // Thêm dữ liệu vào bảng options
        DB::table('options')->insert($options);
    }
}
