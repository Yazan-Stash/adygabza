<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Course;
use App\Models\Exercise;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): Admin
    {
        return Admin::factory()->create();
    }

    // --- Courses index ---

    public function test_guest_cannot_access_admin_courses(): void
    {
        $this->get('/admin/courses')
            ->assertRedirect(route('admin.login'));
    }

    public function test_regular_user_cannot_access_admin_courses(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin/courses')
            ->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_view_courses_index(): void
    {
        Course::factory()->count(3)->create();

        $this->actingAs($this->admin(), 'admin')
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
        $response = $this->actingAs($this->admin(), 'admin')
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

    public function test_unauthenticated_cannot_create_course(): void
    {
        $this->post('/admin/courses', [
            'title' => 'French for Beginners',
            'language_from' => 'English',
            'language_to' => 'French',
        ])
            ->assertRedirect(route('admin.login'));
    }

    public function test_course_creation_requires_title(): void
    {
        $this->actingAs($this->admin(), 'admin')
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

        $this->actingAs($this->admin(), 'admin')
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

        $this->actingAs($this->admin(), 'admin')
            ->delete("/admin/courses/{$course->id}")
            ->assertRedirect('/admin/courses');

        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    // --- Exercise creation ---

    public function test_admin_can_create_exercise(): void
    {
        $course = Course::factory()->create();
        $lesson = Lesson::factory()->create(['course_id' => $course->id]);

        $this->actingAs($this->admin(), 'admin')
            ->post("/admin/courses/{$course->id}/lessons/{$lesson->id}/exercises", [
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
            'lesson_id' => $lesson->id,
            'type' => 'complete_sentence_input',
        ]);
    }

    public function test_admin_can_create_lesson(): void
    {
        $course = Course::factory()->create();

        $this->actingAs($this->admin(), 'admin')
            ->post("/admin/courses/{$course->id}/lessons", [
                'title' => 'Greetings',
                'description' => 'Basic greeting phrases.',
                'order' => 1,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('lessons', [
            'course_id' => $course->id,
            'title' => 'Greetings',
        ]);
    }

    public function test_admin_can_create_concept_text_exercise(): void
    {
        $course = Course::factory()->create();
        $lesson = Lesson::factory()->create(['course_id' => $course->id]);

        $this->actingAs($this->admin(), 'admin')
            ->post("/admin/courses/{$course->id}/lessons/{$lesson->id}/exercises", [
                'type' => 'concept_text',
                'prompt' => 'Adjectives usually follow nouns in Spanish.',
                'answer' => 'understood',
                'options' => null,
                'explanation' => null,
                'order' => 2,
                'metadata' => null,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('exercises', [
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
            'type' => 'concept_text',
        ]);
    }

    public function test_admin_can_delete_exercise(): void
    {
        $course = Course::factory()->create();
        $lesson = Lesson::factory()->create(['course_id' => $course->id]);
        $exercise = Exercise::factory()->create(['course_id' => $course->id, 'lesson_id' => $lesson->id]);

        $this->actingAs($this->admin(), 'admin')
            ->delete("/admin/courses/{$course->id}/lessons/{$lesson->id}/exercises/{$exercise->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('exercises', ['id' => $exercise->id]);
    }

    public function test_admin_can_delete_lesson(): void
    {
        $course = Course::factory()->create();
        $lesson = Lesson::factory()->create(['course_id' => $course->id]);

        $this->actingAs($this->admin(), 'admin')
            ->delete("/admin/courses/{$course->id}/lessons/{$lesson->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('lessons', ['id' => $lesson->id]);
    }

    public function test_deleting_course_cascades_to_exercises(): void
    {
        $course = Course::factory()->create();
        $lesson = Lesson::factory()->create(['course_id' => $course->id]);
        Exercise::factory()->count(3)->create(['course_id' => $course->id, 'lesson_id' => $lesson->id]);

        $this->actingAs($this->admin(), 'admin')
            ->delete("/admin/courses/{$course->id}");

        $this->assertDatabaseCount('exercises', 0);
    }

    // --- Admin login ---

    public function test_admin_login_page_is_accessible(): void
    {
        $this->get(route('admin.login'))->assertOk();
    }

    public function test_admin_can_login_with_valid_credentials(): void
    {
        $admin = $this->admin();

        $this->post(route('admin.login.store'), [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_login_fails_with_wrong_password(): void
    {
        $admin = $this->admin();

        $this->post(route('admin.login.store'), [
            'email' => $admin->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');
    }

    public function test_user_credentials_cannot_login_to_admin(): void
    {
        $user = User::factory()->create(['password' => 'password']);

        $this->post(route('admin.login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');
    }
}
