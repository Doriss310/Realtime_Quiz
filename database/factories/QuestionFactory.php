<?php

namespace Database\Factories;

use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
//    public function definition(): array
//    {
//        return [
//            'text' => fake()->paragraph()
//        ];
//    }

    protected $model = Question::class;

    public function definition(): array
    {
        return [
            'text' => $this->faker->sentence,
            'code_snippet' => '1',
            'answer_explanation' => $this->faker->optional()->paragraph,
            'more_info_link' => $this->faker->optional()->url,
        ];
    }
}
