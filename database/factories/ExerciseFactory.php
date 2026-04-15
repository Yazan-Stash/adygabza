<?php

namespace Database\Factories;

use App\Models\Exercise;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Exercise>
 */
class ExerciseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => \App\Models\Course::factory(),
            'type' => 'complete_sentence_input',
            'prompt' => 'The word for "hello" is ___.',
            'answer' => ['hola'],
            'options' => null,
            'explanation' => 'Hola means hello.',
            'order' => $this->faker->numberBetween(1, 20),
            'metadata' => null,
        ];
    }
}
