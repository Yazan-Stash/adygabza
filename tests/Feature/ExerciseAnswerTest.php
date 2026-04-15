<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Exercise;
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
}
