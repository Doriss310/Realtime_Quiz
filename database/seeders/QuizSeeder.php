<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('quizzes')->insert([
            ['title' => 'HTML',
            'slug' => 'html'],
            ['title' => 'CSS',
                'slug' => 'css'],
            ['title' => 'JavaScript',
                'slug' => 'javascript'],
            ['title' => 'Accessibility',
                'slug' => 'accessibility'],
            ['title' => 'General CS',
                'slug' => 'general-cs'],
            ['title' => 'IT',
                'slug' => 'it'],
            ['title' => 'Linux',
                'slug' => 'linux'],
            ['title' => 'Python',
                'slug' => 'python'],
            ['title' => 'SQL',
                'slug' => 'sql'],
            ['title' => 'Random',
                'slug' => 'random'],
        ]);
    }
}
