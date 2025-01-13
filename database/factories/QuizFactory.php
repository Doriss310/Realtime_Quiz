<?php

namespace Database\Factories;

use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quiz>
 */
class QuizFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
//    public function definition(): array
//    {
//        $title = $this->faker->sentence();
//
//        return [
//            'title' => $title,
//            'description' => fake()->paragraph(),
//            'published' => false,
//            'public' => false,
//        ];

    protected $model = Quiz::class;

    public function definition(): array
    {
        static $titles = ['HTML', 'CSS', 'Javascript', 'IT', 'General CS'];

        $title = !empty($titles) ? array_shift($titles) : $this->faker->sentence;

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => $this->faker->paragraph,
            'published' => $this->faker->boolean,
            'public' => $this->faker->boolean,
        ];
    }
}
