<?php

namespace App\Services;

use App\Models\PaymentTransaction;
use App\Models\PaymentMethod;
use App\Models\WalletBalance;
use Illuminate\Support\Str;

class PaymentGatewayService
{
    // Generate unique transaction ID
    public function generateTransactionId()
    {
        do {
            $transactionId = 'TRX-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        } while (PaymentTransaction::where('transaction_id', $transactionId)->exists());

        return $transactionId;
    }

    // Create payment transaction
    public function createTransaction($learnerId, $courseId, $paymentMethodId)
    {
        $course = \App\Models\Course::findOrFail($courseId);
        $paymentMethod = PaymentMethod::findOrFail($paymentMethodId);

        $feeAmount = $paymentMethod->calculateFee($course->price);
        $totalAmount = $course->price + $feeAmount;

            return PaymentTransaction::create([
              'transaction_id' => $this->generateTransactionId(),
              'learner_id' => $learnerId,
              'course_id' => $courseId,
              'payment_method_id' => $paymentMethodId,
              'amount' => $course->price,
              'fee_amount' => $feeAmount,
              'total_amount' => $totalAmount,
              'status' => 'pending',
              'payment_type' => 'full',
              'expires_at' => now()->addHours(24),
            ]);
    }
    
 // Process payment based on method
      public function processPayment(PaymentTransaction $transaction, $paymentData)
      {
          switch ($transaction->paymentMethod->type) {
              case 'credit_card':
                  return $this->processCreditCard($transaction, $paymentData);
              case 'qris':
                  return $this->processQRIS($transaction);
              case 'virtual_account':
                  return $this->processVirtualAccount($transaction);
              case 'ewallet':
                  return $this->processEWallet($transaction, $paymentData);
              case 'bank_transfer':
                  return $this->processBankTransfer($transaction);
              default:
                  return ['success' => false, 'message' => 'Payment method not supported'];
          }
      }

      // Credit card processing with 3DS simulation
      private function processCreditCard($transaction, $paymentData)
      {
          // Simulate 3DS flow
          if ($this->shouldRequire3DS($paymentData)) {
              return $this->initiate3DSFlow($transaction, $paymentData);
          }

          // Process payment directly
          return $this->completeCreditCardPayment($transaction, $paymentData);
      }

      private function shouldRequire3DS($paymentData)
      {
          // Simulate 3DS requirement based on amount or card type
          return rand(1, 100) <= 30; // 30% chance of requiring 3DS
      }

      private function initiate3DSFlow($transaction, $paymentData)
      {
          $transaction->update([
              'status' => 'processing',
              'payment_details' => [
                  'card_number' => '****-****-****-' . substr($paymentData['card_number'] ?? '1234' , -4),
                  '3ds_required' => true,
                  '3ds_url' => route('payment.3ds.verify', $transaction->transaction_id)
              ]
          ]);

          return [
              'success' => true,
              'requires_3ds' => true,
              'redirect_url' => route('payment.3ds.verify', $transaction->transaction_id),
              'message' => '3DS verification required'
          ];
      }

      // QRIS processing
      private function processQRIS($transaction)
      {
          $qrCode = $this->generateQRCode($transaction);

          $transaction->update([
              'status' => 'pending',
              'payment_details' => [
                  'qr_code' => $qrCode,
                  'qr_string' => '00020101021126570011ID.WWW.QRIS.COM.WWW0148910' . $qrCode .
  '520452415303UMI5802ID5911AcadEasy6014Jakarta61051234562070303UMI',
                  'expires_at' => $transaction->expires_at->toISOString()
              ]
          ]);

          return [
              'success' => true,
              'qr_code' => $qrCode,
              'qr_string' => $transaction->payment_details['qr_string'],
              'expires_at' => $transaction->expires_at,
              'message' => 'QRIS code generated. Please scan with your e-wallet app.'
          ];
      }

      private function generateQRCode($transaction)
      {
          return 'QRIS' . date('YmdHis') . rand(1000, 9999);
      }

      // Virtual Account processing
      private function processVirtualAccount($transaction)
      {
          $vaNumber = $this->generateVirtualAccountNumber($transaction->paymentMethod->provider);

          $transaction->update([
              'status' => 'pending',
              'payment_details' => [
                  'va_number' => $vaNumber,
                  'bank_name' => $transaction->paymentMethod->provider,
                  'amount' => $transaction->total_amount,
                  'expires_at' => $transaction->expires_at->toISOString()
              ]
          ]);

          return [
              'success' => true,
              'va_number' => $vaNumber,
              'bank_name' => $transaction->paymentMethod->provider,
              'amount' => $transaction->total_amount,
              'expires_at' => $transaction->expires_at,
              'message' => "Please transfer to VA account: {$vaNumber}"
          ];
      }

      private function generateVirtualAccountNumber($provider)
      {
          $prefixes = [
              'bca' => '88061',
              'bni' => '88028',
              'bri' => '88037',
              'mandiri' => '88012'
          ];

          $prefix = $prefixes[strtolower($provider)] ?? '88099';
          $random = rand(100000000, 999999999);

          return $prefix . $random;
      }

      // E-Wallet processing
      private function processEWallet($transaction, $paymentData)
      {
          $walletId = $paymentData['wallet_id'] ?? 'default-wallet-' . $transaction->id;

          $transaction->update([
              'status' => 'pending',
              'payment_details' => [
                  'wallet_id' => $walletId,
                  'wallet_type' => $transaction->paymentMethod->provider,
                  'deeplink' => $this->generateEWalletDeeplink($transaction, $walletId),
                  'expires_at' => $transaction->expires_at->toISOString()
              ]
          ]);

          return [
              'success' => true,
              'wallet_id' => $walletId,
              'deeplink' => $transaction->payment_details['deeplink'],
              'expires_at' => $transaction->expires_at,
              'message' => "Please complete payment in your {$transaction->paymentMethod->provider} app"
          ];
      }

      private function generateEWalletDeeplink($transaction, $walletId)
      {
          $baseUrl = [
              'gopay' => 'https://gopay.co.id/app',
              'ovo' => 'https://www.ovo.id/topup',
              'dana' => 'https://www.dana.id/topup',
              'shopeepay' => 'https://shopee.co.id/wallet'
          ];

          return ($baseUrl[strtolower($transaction->paymentMethod->provider)] ?? '#') .
                 '?trx_id=' . $transaction->transaction_id .
                 '&amount=' . $transaction->total_amount;
      }


      // Bank transfer processing
      private function processBankTransfer($transaction)
      {
          $transaction->update([
              'status' => 'pending',
              'payment_details' => [
                  'bank_name' => 'Transfer Bank',
                  'account_number' => '1234567890',
                  'account_name' => 'PT AcadEasy Indonesia',
                  'amount' => $transaction->total_amount,
                  'notes' => $transaction->transaction_id,
                  'expires_at' => $transaction->expires_at->toISOString()
              ]
          ]);

          return [
              'success' => true,
              'bank_name' => 'Transfer Bank',
              'account_number' => '1234567890',
              'account_name' => 'PT AcadEasy Indonesia',
              'amount' => $transaction->total_amount,
              'notes' => $transaction->transaction_id,
              'expires_at' => $transaction->expires_at,
              'message' => 'Please transfer to the specified account and include transaction ID in notes'
          ];
      }

      // Complete successful payment
      public function completePayment($transactionId)
      {
          $transaction = PaymentTransaction::where('transaction_id', $transactionId)->firstOrFail();

          if ($transaction->status === 'success') {
              return ['success' => false, 'message' => 'Payment already completed'];
          }

          $transaction->update([
              'status' => 'success',
              'completed_at' => now(),
              'gateway_response' => [
                  'status' => 'success',
                  'approval_code' => 'APR' . date('YmdHis') . rand(1000, 9999),
                  'bank_approval' => 'BANK' . rand(100000, 999999),
                  'processed_at' => now()->toISOString()
              ]
          ]);

          // Create enrollment
          $this->createEnrollment($transaction);

          // Log transaction
          $transaction->logs()->create([
              'status' => 'success',
              'notes' => 'Payment completed successfully'
          ]);

          return [
              'success' => true,
              'message' => 'Payment completed successfully',
              'transaction' => $transaction->load(['course', 'learner'])
          ];
      }

      // Complete Credit Card Payment with 3DS (Public Method)
      public function completeCreditCard3DS($transactionId)
      {
          $transaction = PaymentTransaction::where('transaction_id', $transactionId)->firstOrFail();

          // Simulate 3DS verification delay
          sleep(2);

          // Update transaction with successful payment details
          $transaction->update([
              'status' => 'success',
              'payment_details' => array_merge($transaction->payment_details ?? [], [
                  'auth_code' => 'AUTH' . date('YmdHis') . rand(1000, 9999),
                  'approval_code' => 'APR' . date('YmdHis') . rand(1000, 9999),
                  'bank_approval' => 'BANK' . rand(100000, 999999),
                  '3ds_verified' => true,
                  'processed_at' => now()->toISOString()
              ])
          ]);

          // Create enrollment
          $this->createEnrollment($transaction);

          // Log transaction
          $transaction->logs()->create([
              'status' => 'success',
              'notes' => 'Credit card payment completed with 3DS verification'
          ]);

          return [
              'success' => true,
              'message' => 'Credit card payment completed successfully with 3DS verification',
              'transaction' => $transaction->load(['course', 'learner'])
          ];
      }

      // Complete Credit Card Payment (3DS Verification)
      private function completeCreditCardPayment($transaction, $paymentData = [])
      {
          // Simulate 3DS verification delay
          sleep(2);

          // Update transaction with successful payment details
          $transaction->update([
              'status' => 'success',
              'payment_details' => array_merge($transaction->payment_details ?? [], [
                  'auth_code' => 'AUTH' . date('YmdHis') . rand(1000, 9999),
                  'approval_code' => 'APR' . date('YmdHis') . rand(1000, 9999),
                  'bank_approval' => 'BANK' . rand(100000, 999999),
                  'processed_at' => now()->toISOString()
              ])
          ]);

          // Create enrollment
          $this->createEnrollment($transaction);

          // Log transaction
          $transaction->logs()->create([
              'status' => 'success',
              'notes' => 'Credit card payment completed successfully'
          ]);

          return [
              'success' => true,
              'message' => 'Credit card payment completed successfully',
              'transaction' => $transaction->load(['course', 'learner'])
          ];
      }

      private function createEnrollment($transaction)
      {
          // Check if already enrolled
          $existingEnrollment = \App\Models\Enrollment::where('learner_id', $transaction->learner_id)
                                                     ->where('course_id', $transaction->course_id)
                                                     ->first();

          if (!$existingEnrollment) {
              \App\Models\Enrollment::create([
                  'learner_id' => $transaction->learner_id,
                  'course_id' => $transaction->course_id
              ]);
          }
      }

      // Simulate payment status check
      public function checkPaymentStatus($transactionId)
      {
          $transaction = PaymentTransaction::where('transaction_id', $transactionId)->firstOrFail();

          if ($transaction->isExpired()) {
              $transaction->update(['status' => 'expired']);
              return ['status' => 'expired', 'message' => 'Payment expired'];
          }

          // Simulate random payment completion for demo
          if ($transaction->status === 'pending' && rand(1, 100) <= 20) { // 20% chance
              return $this->completePayment($transactionId);
          }

          return [
              'status' => $transaction->status,
              'transaction' => $transaction
          ];
      }
}