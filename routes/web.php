<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CourseController::class, 'index'])->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Main auth routes
require __DIR__ . '/auth.php';

// ===============================================================
// Course Index
// ===============================================================
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');

// ===============================================================
// Instructor only routes
// ===============================================================
Route::middleware(['auth', 'role:instructor'])->group(function () {
    Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
});

// ===============================================================
// Public course routes (URUTAN PENTING)
// ===============================================================

// Start Course — HARUS sebelum show
Route::get('/courses/{course}/start', [CourseController::class, 'start'])
    ->name('courses.start')
    ->middleware('auth');

// Show — paling bawah
Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');

// ===============================================================
// Enrollment (Learner Only)
// ===============================================================
Route::middleware(['auth', 'role:learner'])->group(function () {
    Route::post('/enroll/{course}', [EnrollmentController::class, 'store'])->name('enroll.store');
});

Route::get('/courses/{course}/lesson/{lesson}', [LessonController::class, 'show'])
    ->name('lessons.show')
    ->middleware('auth');
