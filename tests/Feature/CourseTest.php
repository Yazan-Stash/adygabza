<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Exercise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_view_published_courses(): void
    {
        Course::factory()->create(['is_published' => true]);
        Course::factory()->create(['is_published' => false]);

        $response = $this->get(route('courses.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('courses/Index')
            ->has('courses', 1)
        );
    }

    public function test_guests_cannot_view_unpublished_course(): void
    {
        $course = Course::factory()->create(['is_published' => false]);

        $this->get(route('courses.show', $course))->assertNotFound();
    }

    public function test_guests_can_view_published_course(): void
    {
        $course = Course::factory()->create(['is_published' => true]);

        $response = $this->get(route('courses.show', $course));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('courses/Show')
            ->has('course')
            ->has('exercises')
        );
    }

    public function test_authenticated_user_gets_progress_on_course_show(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create(['is_published' => true]);

        $response = $this->actingAs($user)->get(route('courses.show', $course));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('courses/Show')
            ->has('progress')
        );
    }
}
