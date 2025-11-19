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

// =============== AUTH ROUTES ===============
require __DIR__ . '/auth.php';

// =============== ADMIN PANEL ===============
Route::middleware(['auth', 'role:admin'])->group(function () {

    // Admin dashboard
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/admin/users', [AdminController::class, 'index'])->name('admin.users');

    Route::post('/admin/users/{user}/suspend', [AdminController::class, 'suspend'])
        ->name('admin.users.suspend');

    Route::post('/admin/users/{user}/unsuspend', [AdminController::class, 'unsuspend'])
        ->name('admin.users.unsuspend');
});


// =============== DASHBOARD (SEMUA USER BISA AKSES) ===============
Route::get('/dashboard', function () {
    return app(DashboardController::class)->index();
})
->middleware(['auth', 'verified', 'not-admin'])
->name('dashboard');


// =============== PROFILE ===============
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// =============== COURSES PUBLIC ===============
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');

// =============== INSTRUCTOR + ADMIN BOLEH EDIT COURSE ===============
Route::middleware(['auth', 'role:instructor,admin'])->group(function () {

    Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
});

// =============== COURSE START ===============
Route::get('/courses/{course}/start', [CourseController::class, 'start'])
    ->middleware('auth')
    ->name('courses.start');

// =============== COURSE SHOW ===============
Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');

// =============== ENROLL (LEARNER SAJA) ===============
Route::middleware(['auth', 'role:learner'])->group(function () {
    Route::post('/enroll/{course}', [EnrollmentController::class, 'store'])->name('enroll.store');
});

// =============== LESSONS ===============
Route::get('/courses/{course}/lesson/{lesson}', [LessonController::class, 'show'])
    ->middleware('auth')
    ->name('lessons.show');
