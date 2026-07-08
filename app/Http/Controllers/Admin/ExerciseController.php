<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExerciseRequest;
use App\Http\Requests\UpdateExerciseRequest;
use App\Models\Course;
use App\Models\Exercise;
use App\Models\Lesson;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ExerciseController extends Controller
{
    public function create(Course $course, Lesson $lesson): Response
    {
        return Inertia::render('admin/exercises/Edit', [
            'course' => $course,
            'lesson' => $lesson,
            'exercise' => null,
        ]);
    }

    public function store(StoreExerciseRequest $request, Course $course, Lesson $lesson): RedirectResponse
    {
        $lesson->exercises()->create([
            ...$request->validated(),
            'course_id' => $course->id,
        ]);

        return redirect()->route('admin.courses.edit', $course)
            ->with('flash', ['message' => 'Exercise created.', 'type' => 'success']);
    }

    public function edit(Course $course, Lesson $lesson, Exercise $exercise): Response
    {
        return Inertia::render('admin/exercises/Edit', [
            'course' => $course,
            'lesson' => $lesson,
            'exercise' => $exercise,
        ]);
    }

    public function update(UpdateExerciseRequest $request, Course $course, Lesson $lesson, Exercise $exercise): RedirectResponse
    {
        $exercise->update([
            ...$request->validated(),
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
        ]);

        return redirect()->route('admin.courses.edit', $course)
            ->with('flash', ['message' => 'Exercise updated.', 'type' => 'success']);
    }

    public function destroy(Course $course, Lesson $lesson, Exercise $exercise): RedirectResponse
    {
        $exercise->delete();

        return redirect()->route('admin.courses.edit', $course)
            ->with('flash', ['message' => 'Exercise deleted.', 'type' => 'success']);
    }
}
