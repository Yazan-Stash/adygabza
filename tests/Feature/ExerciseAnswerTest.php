<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Exercise;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExerciseAnswerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_submit_answer(): void
    {
        $exercise = Exercise::factory()->create();

        $this->postJson(route('exercises.answer', $exercise), ['answer' => 'hola'])
            ->assertUnauthorized();
    }

    public function test_correct_input_answer_returns_correct_true(): void
    {
        $user = User::factory()->create();
        $exercise = Exercise::factory()->create([
            'type' => 'complete_sentence_input',
            'answer' => ['hola', 'Hola'],
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('exercises.answer', $exercise), ['answer' => 'hola']);

        $response->assertOk()->assertJson(['correct' => true]);
    }

    public function test_case_insensitive_input_answer_is_correct(): void
    {
        $user = User::factory()->create();
        $exercise = Exercise::factory()->create([
            'type' => 'complete_sentence_input',
            'answer' => ['hola'],
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('exercises.answer', $exercise), ['answer' => 'HOLA']);

        $response->assertOk()->assertJson(['correct' => true]);
    }

    public function test_wrong_input_answer_returns_correct_false(): void
    {
        $user = User::factory()->create();
        $exercise = Exercise::factory()->create([
            'type' => 'complete_sentence_input',
            'answer' => ['hola'],
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('exercises.answer', $exercise), ['answer' => 'adios']);

        $response->assertOk()->assertJson(['correct' => false]);
    }

    public function test_correct_mcq_answer_returns_correct_true(): void
    {
        $user = User::factory()->create();
        $exercise = Exercise::factory()->create([
            'type' => 'complete_sentence_mcq',
            'answer' => 'Me',
            'options' => ['Me', 'Te', 'Se', 'Le'],
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('exercises.answer', $exercise), ['answer' => 'Me']);

        $response->assertOk()->assertJson(['correct' => true]);
    }

    public function test_correct_reorder_answer_returns_correct_true(): void
    {
        $user = User::factory()->create();
        $exercise = Exercise::factory()->create([
            'type' => 'reorder_translation',
            'answer' => ['Quiero', 'un', 'café'],
            'options' => ['un', 'Quiero', 'café'],
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('exercises.answer', $exercise), ['answer' => ['Quiero', 'un', 'café']]);

        $response->assertOk()->assertJson(['correct' => true]);
    }

    public function test_wrong_reorder_answer_returns_correct_false(): void
    {
        $user = User::factory()->create();
        $exercise = Exercise::factory()->create([
            'type' => 'reorder_translation',
            'answer' => ['Quiero', 'un', 'café'],
            'options' => ['un', 'Quiero', 'café'],
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('exercises.answer', $exercise), ['answer' => ['un', 'Quiero', 'café']]);

        $response->assertOk()->assertJson(['correct' => false]);
    }

    public function test_concept_text_answer_is_always_correct(): void
    {
        $user = User::factory()->create();
        $exercise = Exercise::factory()->create([
            'type' => 'concept_text',
            'prompt' => 'Spanish adjectives usually follow the noun.',
            'answer' => 'understood',
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('exercises.answer', $exercise), ['answer' => 'understood']);

        $response->assertOk()->assertJson(['correct' => true]);
    }

    public function test_concept_text_answer_completes_without_incrementing_score(): void
    {
        $user = User::factory()->create();
        $exercise = Exercise::factory()->create([
            'type' => 'concept_text',
            'answer' => 'understood',
        ]);

        $this->actingAs($user)
            ->postJson(route('exercises.answer', $exercise), ['answer' => 'understood']);

        $this->assertDatabaseHas('user_course_progress', [
            'user_id' => $user->id,
            'course_id' => $exercise->course_id,
            'score' => 0,
        ]);
    }

    public function test_answer_increments_user_score(): void
    {
        $user = User::factory()->create();
        $exercise = Exercise::factory()->create([
            'type' => 'complete_sentence_input',
            'answer' => ['hola'],
        ]);

        $this->actingAs($user)
            ->postJson(route('exercises.answer', $exercise), ['answer' => 'hola']);

        $this->assertDatabaseHas('user_course_progress', [
            'user_id' => $user->id,
            'course_id' => $exercise->course_id,
            'score' => 1,
        ]);
    }

    public function test_answer_advances_to_next_lesson_exercise(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        $firstLesson = Lesson::factory()->create(['course_id' => $course->id, 'order' => 1]);
        $secondLesson = Lesson::factory()->create(['course_id' => $course->id, 'order' => 2]);
        $firstExercise = Exercise::factory()->create([
            'course_id' => $course->id,
            'lesson_id' => $firstLesson->id,
            'order' => 1,
            'answer' => ['hola'],
        ]);
        $secondExercise = Exercise::factory()->create([
            'course_id' => $course->id,
            'lesson_id' => $secondLesson->id,
            'order' => 1,
            'answer' => ['adios'],
        ]);

        $this->actingAs($user)
            ->postJson(route('exercises.answer', $firstExercise), ['answer' => 'hola']);

        $this->assertDatabaseHas('user_course_progress', [
            'user_id' => $user->id,
            'course_id' => $course->id,
            'current_exercise_id' => $secondExercise->id,
        ]);
    }
}
