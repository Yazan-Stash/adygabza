<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CourseController extends Controller
{
    public function index(): Response
    {
        $courses = Course::withCount('exercises')->latest()->get();

        return Inertia::render('admin/courses/Index', [
            'courses' => $courses,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/courses/Edit', [
            'course' => null,
        ]);
    }

    public function store(StoreCourseRequest $request): RedirectResponse
    {
        $course = Course::create($request->validated());

        return redirect()->route('admin.courses.edit', $course)
            ->with('flash', ['message' => 'Course created.', 'type' => 'success']);
    }

    public function edit(Course $course): Response
    {
        $course->load('lessons.exercises');

        return Inertia::render('admin/courses/Edit', [
            'course' => $course,
        ]);
    }

    public function update(UpdateCourseRequest $request, Course $course): RedirectResponse
    {
        $course->update($request->validated());

        return redirect()->route('admin.courses.edit', $course)
            ->with('flash', ['message' => 'Course updated.', 'type' => 'success']);
    }

    public function destroy(Course $course): RedirectResponse
    {
        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('flash', ['message' => 'Course deleted.', 'type' => 'success']);
    }
}
