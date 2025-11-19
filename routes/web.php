<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// =============== HOME ===============
Route::get('/', [CourseController::class, 'index'])->name('home');

// =============== DASHBOARD REDIRECT ===============
// Semua user masuk ke /dashboard, tapi diarahkan sesuai user_type
Route::get('/dashboard', function () {
    if (auth()->user()->user_type === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return app(DashboardController::class)->index();
})
->middleware(['auth', 'verified'])
->name('dashboard');

// =============== PROFILE ===============
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

// =============== COURSES PUBLIC ===============
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');

// =============== INSTRUCTOR AREA ===============
Route::middleware(['auth', 'role:instructor'])->group(function () {
    Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
});

// =============== ADMIN PANEL ===============
Route::middleware(['auth', 'role:admin'])->group(function () {

    // Dashboard Admin
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Manage Users
    Route::get('/admin/users', [AdminController::class, 'index'])->name('admin.users');

    Route::post('/admin/users/{user}/suspend', [AdminController::class, 'suspend'])
        ->name('admin.users.suspend');

    Route::post('/admin/users/{user}/unsuspend', [AdminController::class, 'unsuspend'])
        ->name('admin.users.unsuspend');
});

// =============== COURSE START (must be above show) ===============
Route::get('/courses/{course}/start', [CourseController::class, 'start'])
    ->middleware('auth')
    ->name('courses.start');

// =============== COURSE SHOW (keep last) ===============
Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');

// =============== ENROLL (LEARNER) ===============
Route::middleware(['auth', 'role:learner'])->group(function () {
    Route::post('/enroll/{course}', [EnrollmentController::class, 'store'])->name('enroll.store');
});

// =============== LESSONS ===============
Route::get('/courses/{course}/lesson/{lesson}', [LessonController::class, 'show'])
    ->middleware('auth')
    ->name('lessons.show');
