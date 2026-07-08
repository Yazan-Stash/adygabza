<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\UserCourseProgress;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CourseController extends Controller
{
    public function index(): Response
    {
        $courses = Course::where('is_published', true)
            ->withCount('exercises')
            ->latest()
            ->get();

        return Inertia::render('courses/Index', [
            'courses' => $courses,
        ]);
    }

    public function show(Request $request, Course $course): Response
    {
        abort_unless($course->is_published, 404);

        $exercises = $course->exercises()->get();
        $course->load('lessons');

        $progress = $request->user()
            ? UserCourseProgress::firstOrCreate(
                ['user_id' => $request->user()->id, 'course_id' => $course->id],
                ['current_exercise_id' => $exercises->first()?->id, 'completed_exercise_ids' => [], 'score' => 0]
            )
            : null;

        return Inertia::render('courses/Show', [
            'course' => $course,
            'exercises' => $exercises,
            'progress' => $progress,
        ]);
    }
}
