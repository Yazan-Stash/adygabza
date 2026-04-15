<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Exercise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    private function user(): User
    {
        return User::factory()->create(['is_admin' => false]);
    }

    // --- Courses index ---

    public function test_non_admin_cannot_access_admin_courses(): void
    {
        $this->actingAs($this->user())
            ->get('/admin/courses')
            ->assertForbidden();
    }

    public function test_admin_can_view_courses_index(): void
    {
        Course::factory()->count(3)->create();

        $this->actingAs($this->admin())
            ->get('/admin/courses')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('admin/courses/Index')
                ->has('courses', 3)
            );
    }

    // --- Course creation ---

    public function test_admin_can_create_a_course(): void
    {
        $response = $this->actingAs($this->admin())
            ->post('/admin/courses', [
                'title' => 'French for Beginners',
                'description' => 'Learn French',
                'language_from' => 'English',
                'language_to' => 'French',
                'is_published' => true,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('courses', ['title' => 'French for Beginners']);
    }

    public function test_non_admin_cannot_create_course(): void
    {
        $this->actingAs($this->user())
            ->post('/admin/courses', [
                'title' => 'French for Beginners',
                'language_from' => 'English',
                'language_to' => 'French',
            ])
            ->assertForbidden();
    }

    public function test_course_creation_requires_title(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/courses', [
                'language_from' => 'English',
                'language_to' => 'French',
            ])
            ->assertSessionHasErrors('title');
    }

    // --- Course update ---

    public function test_admin_can_update_course(): void
    {
        $course = Course::factory()->create(['title' => 'Old Title']);

        $this->actingAs($this->admin())
            ->put("/admin/courses/{$course->id}", [
                'title' => 'New Title',
                'language_from' => 'English',
                'language_to' => 'French',
                'is_published' => true,
            ]);

        $this->assertDatabaseHas('courses', ['id' => $course->id, 'title' => 'New Title']);
    }

    // --- Course delete ---

    public function test_admin_can_delete_course(): void
    {
        $course = Course::factory()->create();

        $this->actingAs($this->admin())
            ->delete("/admin/courses/{$course->id}")
            ->assertRedirect('/admin/courses');

        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    // --- Exercise creation ---

    public function test_admin_can_create_exercise(): void
    {
        $course = Course::factory()->create();

        $this->actingAs($this->admin())
            ->post("/admin/courses/{$course->id}/exercises", [
                'type' => 'complete_sentence_input',
                'prompt' => 'Say hello: ___',
                'answer' => ['hola'],
                'options' => null,
                'explanation' => 'Hola means hello.',
                'order' => 1,
                'metadata' => null,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('exercises', [
            'course_id' => $course->id,
            'type' => 'complete_sentence_input',
        ]);
    }

    public function test_admin_can_delete_exercise(): void
    {
        $course = Course::factory()->create();
        $exercise = Exercise::factory()->create(['course_id' => $course->id]);

        $this->actingAs($this->admin())
            ->delete("/admin/courses/{$course->id}/exercises/{$exercise->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('exercises', ['id' => $exercise->id]);
    }

    public function test_deleting_course_cascades_to_exercises(): void
    {
        $course = Course::factory()->create();
        Exercise::factory()->count(3)->create(['course_id' => $course->id]);

        $this->actingAs($this->admin())
            ->delete("/admin/courses/{$course->id}");

        $this->assertDatabaseCount('exercises', 0);
    }
}
