<?php

use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\ExerciseController;
use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ExerciseAnswerController;
use App\Http\Middleware\EnsureIsAdmin;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

// Courses (public, published only)
Route::get('courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('courses/{course}', [CourseController::class, 'show'])->name('courses.show');

// Exercise answer submission (auth required)
Route::middleware(['auth'])->post('exercises/{exercise}/answer', [ExerciseAnswerController::class, 'submit'])->name('exercises.answer');

// Admin auth (unauthenticated)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminLoginController::class, 'create'])->name('login');
    Route::post('login', [AdminLoginController::class, 'store'])->name('login.store');
    Route::post('logout', [AdminLoginController::class, 'destroy'])->name('logout');
});

// Admin panel (protected)
Route::middleware([EnsureIsAdmin::class])->prefix('admin')->name('admin.')->scopeBindings()->group(function () {
    Route::inertia('/', 'admin/Dashboard')->name('dashboard');

    Route::resource('courses', App\Http\Controllers\Admin\CourseController::class)
        ->except(['show']);

    Route::prefix('courses/{course}/lessons')->name('courses.lessons.')->group(function () {
        Route::get('create', [LessonController::class, 'create'])->name('create');
        Route::post('/', [LessonController::class, 'store'])->name('store');
        Route::get('{lesson}/edit', [LessonController::class, 'edit'])->name('edit');
        Route::put('{lesson}', [LessonController::class, 'update'])->name('update');
        Route::delete('{lesson}', [LessonController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('courses/{course}/lessons/{lesson}/exercises')->name('courses.lessons.exercises.')->group(function () {
        Route::get('create', [ExerciseController::class, 'create'])->name('create');
        Route::post('/', [ExerciseController::class, 'store'])->name('store');
        Route::get('{exercise}/edit', [ExerciseController::class, 'edit'])->name('edit');
        Route::put('{exercise}', [ExerciseController::class, 'update'])->name('update');
        Route::delete('{exercise}', [ExerciseController::class, 'destroy'])->name('destroy');
    });
});

require __DIR__.'/settings.php';
