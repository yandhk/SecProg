<?php

namespace App\Http\Controllers;

use App\Services\PaymentGatewayService;
use App\Services\WalletService;
use App\Models\PaymentTransaction;
use App\Models\PaymentMethod;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $paymentService;
    protected $walletService;

    public function __construct(PaymentGatewayService $paymentService, WalletService $walletService)
      {
          $this->paymentService = $paymentService;
          $this->walletService = $walletService;
      }

     // Show payment page for course
      public function showPaymentPage(Course $course)
      {
          $learner = auth()->user();

          // Check if already enrolled
          $alreadyEnrolled = Enrollment::where('learner_id', $learner->id)
                                     ->where('course_id', $course->id)
                                     ->exists();

          if ($alreadyEnrolled) {
              return redirect()->route('learner.dashboard')
                             ->with('info', 'You are already enrolled in this course.');
          }

          // Get wallet balance
          $walletBalance = $this->walletService->getOrCreateWallet($learner->id);

          // Get available payment methods
          $paymentMethods = PaymentMethod::where('is_active', true)
                                        ->orderBy('type')
                                        ->get()
                                        ->groupBy('type');

          return view('payment.checkout', compact('course', 'paymentMethods', 'walletBalance'));
      }

      // Initiate payment
      public function initiatePayment(Request $request, Course $course)
      {
          $request->validate([
              'payment_method_id' => 'required|exists:payment_methods,id'
          ]);

          $learner = auth()->user();
          $paymentMethod = PaymentMethod::findOrFail($request->payment_method_id);

          // Create transaction
          $transaction = $this->paymentService->createTransaction(
              $learner->id,
              $course->id,
              $paymentMethod->id
          );

          // Process payment based on method
          $result = $this->paymentService->processPayment($transaction, $request->all());

          if (!$result['success']) {
              return redirect()->back()
                             ->with('error', $result['message']);
          }

          // If requires 3DS, redirect to 3DS page
          if (isset($result['requires_3ds']) && $result['requires_3ds']) {
              return redirect($result['redirect_url']);
          }

          // Redirect to payment instructions page
          return redirect()->route('payment.instructions', $transaction->transaction_id)
                          ->with('success', $result['message']);
      }

      // Payment instructions page
      public function paymentInstructions($transactionId)
      {
          $transaction = PaymentTransaction::where('transaction_id', $transactionId)
                                         ->with(['course', 'paymentMethod', 'learner'])
                                         ->firstOrFail();

          // Check if transaction belongs to authenticated user
          if ($transaction->learner_id !== auth()->id()) {
              abort(403, 'Unauthorized action.');
          }

          // Check if expired
          if ($transaction->isExpired()) {
              $transaction->update(['status' => 'expired']);
              return view('payment.expired', compact('transaction'));
          }

          return view('payment.instructions', compact('transaction'));
      }

      // 3DS verification page
      public function show3DSVerification($transactionId)
      {
          $transaction = PaymentTransaction::where('transaction_id', $transactionId)
                                         ->with(['course', 'paymentMethod'])
                                         ->firstOrFail();

          if ($transaction->learner_id !== auth()->id()) {
              abort(403, 'Unauthorized action.');
          }

          return view('payment.3ds-verification', compact('transaction'));
      }

      // Process 3DS verification
      public function process3DSVerification(Request $request, $transactionId)
      {
          $transaction = PaymentTransaction::where('transaction_id', $transactionId)->firstOrFail();

          if ($transaction->learner_id !== auth()->id()) {
              abort(403, 'Unauthorized action.');
          }

          $request->validate([
              'otp' => 'required|digits:6'
          ]);

          // Simulate 3DS verification (accept any 6-digit code for demo)
          if ($request->otp === '123456' || rand(1, 100) <= 80) { // 80% success rate
              $result = $this->paymentService->completeCreditCard3DS($transactionId);

              if ($result['success']) {
                  return redirect()->route('payment.success', $transactionId)
                                 ->with('success', 'Payment completed successfully with 3DS verification!');
              }
          }

          return redirect()->back()
                         ->with('error', 'Invalid OTP. Please try again. (Hint: Use 123456 for demo)');
      }

      // Payment success page
      public function paymentSuccess($transactionId)
      {
          $transaction = PaymentTransaction::where('transaction_id', $transactionId)
                                         ->with(['course', 'learner'])
                                         ->firstOrFail();

          if ($transaction->learner_id !== auth()->id()) {
              abort(403, 'Unauthorized action.');
          }

          if ($transaction->status !== 'success') {
              return redirect()->route('payment.instructions', $transactionId);
          }

          return view('payment.success', compact('transaction'));
      }

      // Check payment status
      public function checkPaymentStatus(Request $request, $transactionId)
      {
          $transaction = PaymentTransaction::where('transaction_id', $transactionId)->firstOrFail();

          if ($transaction->learner_id !== auth()->id()) {
              abort(403, 'Unauthorized action.');
          }

          // If simulation parameter is present, complete the payment immediately
          if ($request->query('simulate')) {
              $result = $this->paymentService->completePayment($transactionId);
          } else {
              $result = $this->paymentService->checkPaymentStatus($transactionId);
          }

          return response()->json($result);
      }

      // Cancel payment
      public function cancelPayment($transactionId)
      {
          $transaction = PaymentTransaction::where('transaction_id', $transactionId)->firstOrFail();

          if ($transaction->learner_id !== auth()->id()) {
              abort(403, 'Unauthorized action.');
          }

          if ($transaction->status !== 'pending') {
              return redirect()->back()
                             ->with('error', 'Cannot cancel this transaction.');
          }

          $transaction->update(['status' => 'cancelled']);

          return redirect()->route('courses.show', $transaction->course_id)
                         ->with('info', 'Payment cancelled.');
      }

      // Pay with wallet
      public function payWithWallet(Request $request, Course $course)
      {
          $learner = auth()->user();

          $result = $this->walletService->payWithWallet(
              $learner->id,
              $course->id,
              $course->price
          );

          if ($result['success']) {
              return redirect()->route('payment.success', $result['transaction']->transaction_id)
                             ->with('success', $result['message']);
          }

          return redirect()->back()
                         ->with('error', $result['message']);
      }

      // Wallet management
      public function showWallet()
      {
          $learner = auth()->user();
          $wallet = $this->walletService->getOrCreateWallet($learner->id);

          // Get wallet transaction history
          $walletTransactions = PaymentTransaction::where('learner_id', $learner->id)
                                                 ->where('payment_type', 'wallet')
                                                 ->latest()
                                                 ->paginate(10);

          return view('payment.wallet', compact('wallet', 'walletTransactions'));
      }

      // Add funds to wallet (demo)
      public function addWalletFunds(Request $request)
      {
          $request->validate([
              'amount' => 'required|numeric|min:10000|max:10000000'
          ]);

          $learner = auth()->user();
          $amount = $request->amount;

          $result = $this->walletService->addFunds(
              $learner->id,
              $amount,
              'Demo top up'
          );

          if ($result['success']) {
              return redirect()->back()
                             ->with('success', $result['message']);
          }

          return redirect()->back()
                         ->with('error', $result['message']);
      }
}