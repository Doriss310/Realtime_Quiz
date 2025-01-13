<?php

namespace Database\Factories;

use App\Models\Option;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Option>
 */
class OptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
//    public function definition(): array
//    {
//        return [
//            'text' => fake()->text(),
//            'correct' => fake()->boolean(),
//            'question_id' => Question::factory(),
//        ];
//    }
    protected $model = Option::class;

    public function definition(): array
    {
        $correct = $this->faker->boolean(25); // 25% chance of being correct
        return [
            'text' => $correct ? 'Đáp án đúng' . $this->faker->randomElement([1, 2, 3, 4]) : 'Sai ' . $this->faker->randomElement([1, 2, 3, 4]),
            'correct' => $correct,
        ];
    }
}
