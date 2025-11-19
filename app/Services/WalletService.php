<?php

namespace App\Services;

use App\Models\WalletBalance;
use App\Models\PaymentTransaction;
use App\Models\TransactionLog;

class WalletService
{
    // Get or create wallet balance for learner
      public function getOrCreateWallet($learnerId)
      {
          return WalletBalance::firstOrCreate(
              ['learner_id' => $learnerId],
              ['balance' => 0, 'last_updated' => now()]
          );
      }

      // Add funds to wallet (for demo purposes)
      public function addFunds($learnerId, $amount, $description = 'Top up')
      {
          $wallet = $this->getOrCreateWallet($learnerId);
          $wallet->addFunds($amount);

          // Log transaction
          PaymentTransaction::create([
              'transaction_id' => $this->generateTransactionId(),
              'learner_id' => $learnerId,
              'course_id' => null, // Wallet top-ups don't have courses
              'amount' => $amount,
              'fee_amount' => 0,
              'total_amount' => $amount,
              'status' => 'success',
              'payment_type' => 'wallet',
              'payment_details' => [
                  'description' => $description,
                  'type' => 'top_up'
              ],
              'expires_at' => now()->addHours(24), // Set expires_at even though it's completed
              'completed_at' => now()
          ]);

          return [
              'success' => true,
              'new_balance' => $wallet->balance,
              'message' => "Successfully added Rp " . number_format($amount, 0, ',', '.') . " to wallet"
          ];
      }

      // Pay with wallet balance
      public function payWithWallet($learnerId, $courseId, $coursePrice)
      {
          $wallet = $this->getOrCreateWallet($learnerId);

          if ($wallet->balance < $coursePrice) {
              return [
                  'success' => false,
                  'message' => 'Insufficient wallet balance',
                  'current_balance' => $wallet->balance,
                  'required' => $coursePrice,
                  'shortage' => $coursePrice - $wallet->balance
              ];
          }

          // Deduct from wallet
          if (!$wallet->deductFunds($coursePrice)) {
              return ['success' => false, 'message' => 'Failed to deduct funds from wallet'];
          }

          // Create transaction record
          $transaction = PaymentTransaction::create([
              'transaction_id' => $this->generateTransactionId(),
              'learner_id' => $learnerId,
              'course_id' => $courseId,
              'payment_type' => 'wallet',
              'amount' => $coursePrice,
              'fee_amount' => 0,
              'total_amount' => $coursePrice,
              'status' => 'success',
              'payment_details' => [
                  'wallet_balance_before' => $wallet->balance + $coursePrice,
                  'wallet_balance_after' => $wallet->balance,
                  'type' => 'course_purchase'
              ],
              'completed_at' => now()
          ]);

          // Create enrollment
          \App\Models\Enrollment::firstOrCreate([
              'learner_id' => $learnerId,
              'course_id' => $courseId
          ]);

          return [
              'success' => true,
              'transaction' => $transaction,
              'new_balance' => $wallet->balance,
              'message' => 'Course purchased successfully using wallet balance'
          ];
      }

      private function generateTransactionId()
      {
          return 'WAL-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(6));
      }
}