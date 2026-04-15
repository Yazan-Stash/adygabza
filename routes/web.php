<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

// Courses (public, published only)
Route::get('courses', [\App\Http\Controllers\CourseController::class, 'index'])->name('courses.index');
Route::get('courses/{course}', [\App\Http\Controllers\CourseController::class, 'show'])->name('courses.show');

// Exercise answer submission (auth required)
Route::middleware(['auth'])->post('exercises/{exercise}/answer', [\App\Http\Controllers\ExerciseAnswerController::class, 'submit'])->name('exercises.answer');

// Admin panel
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::inertia('/', 'admin/Dashboard')->name('dashboard');

    Route::resource('courses', \App\Http\Controllers\Admin\CourseController::class)
        ->except(['show']);

    Route::prefix('courses/{course}/exercises')->name('courses.exercises.')->group(function () {
        Route::get('create', [\App\Http\Controllers\Admin\ExerciseController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\ExerciseController::class, 'store'])->name('store');
        Route::get('{exercise}/edit', [\App\Http\Controllers\Admin\ExerciseController::class, 'edit'])->name('edit');
        Route::put('{exercise}', [\App\Http\Controllers\Admin\ExerciseController::class, 'update'])->name('update');
        Route::delete('{exercise}', [\App\Http\Controllers\Admin\ExerciseController::class, 'destroy'])->name('destroy');
    });
});

require __DIR__.'/settings.php';
