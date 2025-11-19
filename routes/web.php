<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CourseController::class, 'index'])->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/learner/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('learner.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


// ===============================================================
// Course Index route 
// ===============================================================
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');

// ===============================================================
// Instructor only course management
// ===============================================================
Route::middleware(['auth', 'role:instructor'])->group(function () {
    Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
});

// ===============================================================
// Public course route
// ===============================================================
Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
    
// ===============================================================
// Learner only enrollment routes
// ===============================================================
Route::middleware(['auth', 'role:learner'])->group(function () {
    Route::post('/enroll/{course}', [EnrollmentController::class, 'store'])->name('enroll.store');
});

 // Payment Routes
Route::middleware(['auth', 'role:learner'])->group(function () {
    // Payment checkout
    Route::get('/courses/{course}/payment', [PaymentController::class, 'showPaymentPage'])->name('payment.checkout');
    Route::post('/courses/{course}/payment/initiate', [PaymentController::class, 'initiatePayment'])->name('payment.initiate');
    Route::post('/courses/{course}/payment/wallet', [PaymentController::class, 'payWithWallet'])->name('payment.pay.wallet');

    // Payment instructions and status
    Route::get('/payment/{transactionId}/instructions', [PaymentController::class, 'paymentInstructions'])->name('payment.instructions');
    Route::get('/payment/{transactionId}/success', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
    Route::get('/payment/{transactionId}/cancel', [PaymentController::class, 'cancelPayment'])->name('payment.cancel');
    Route::get('/payment/{transactionId}/status', [PaymentController::class, 'checkPaymentStatus'])->name('payment.check.status');

    // 3DS Verification
    Route::get('/payment/{transactionId}/3ds', [PaymentController::class, 'show3DSVerification'])->name('payment.3ds.verify');
    Route::post('/payment/{transactionId}/3ds', [PaymentController::class, 'process3DSVerification'])->name('payment.3ds.process');

    // Wallet Management
    Route::get('/wallet', [PaymentController::class, 'showWallet'])->name('wallet.show');
    Route::post('/wallet/add-funds', [PaymentController::class, 'addWalletFunds'])->name('wallet.add.funds');
});


