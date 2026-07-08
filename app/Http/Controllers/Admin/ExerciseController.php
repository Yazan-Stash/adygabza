<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExerciseRequest;
use App\Http\Requests\UpdateExerciseRequest;
use App\Models\Course;
use App\Models\Exercise;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ExerciseController extends Controller
{
    public function create(Course $course): Response
    {
        return Inertia::render('admin/exercises/Edit', [
            'course' => $course,
            'exercise' => null,
        ]);
    }

    public function store(StoreExerciseRequest $request, Course $course): RedirectResponse
    {
        $course->exercises()->create($request->validated());

        return redirect()->route('admin.courses.edit', $course)
            ->with('flash', ['message' => 'Exercise created.', 'type' => 'success']);
    }

    public function edit(Course $course, Exercise $exercise): Response
    {
        return Inertia::render('admin/exercises/Edit', [
            'course' => $course,
            'exercise' => $exercise,
        ]);
    }

    public function update(UpdateExerciseRequest $request, Course $course, Exercise $exercise): RedirectResponse
    {
        $exercise->update($request->validated());

        return redirect()->route('admin.courses.edit', $course)
            ->with('flash', ['message' => 'Exercise updated.', 'type' => 'success']);
    }

    public function destroy(Course $course, Exercise $exercise): RedirectResponse
    {
        $exercise->delete();

        return redirect()->route('admin.courses.edit', $course)
            ->with('flash', ['message' => 'Exercise deleted.', 'type' => 'success']);
    }
}
