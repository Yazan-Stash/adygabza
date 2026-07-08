<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLessonRequest;
use App\Http\Requests\UpdateLessonRequest;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class LessonController extends Controller
{
    public function create(Course $course): Response
    {
        return Inertia::render('admin/lessons/Edit', [
            'course' => $course,
            'lesson' => null,
        ]);
    }

    public function store(StoreLessonRequest $request, Course $course): RedirectResponse
    {
        $course->lessons()->create($request->validated());

        return redirect()->route('admin.courses.edit', $course)
            ->with('flash', ['message' => 'Lesson created.', 'type' => 'success']);
    }

    public function edit(Course $course, Lesson $lesson): Response
    {
        return Inertia::render('admin/lessons/Edit', [
            'course' => $course,
            'lesson' => $lesson,
        ]);
    }

    public function update(UpdateLessonRequest $request, Course $course, Lesson $lesson): RedirectResponse
    {
        $lesson->update($request->validated());

        return redirect()->route('admin.courses.edit', $course)
            ->with('flash', ['message' => 'Lesson updated.', 'type' => 'success']);
    }

    public function destroy(Course $course, Lesson $lesson): RedirectResponse
    {
        $lesson->delete();

        return redirect()->route('admin.courses.edit', $course)
            ->with('flash', ['message' => 'Lesson deleted.', 'type' => 'success']);
    }
}
