<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Exercise;
use App\Models\Lesson;
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
        $course = Course::factory()->create();
        $lesson = Lesson::factory()->create(['course_id' => $course->id]);

        return [
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
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
