<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
  {
      public function up(): void
      {
          // Wallet balances for learners
          Schema::create('wallet_balances', function (Blueprint $table) {
              $table->id();
              $table->foreignId('learner_id')->constrained('users')->onDelete('cascade');
              $table->decimal('balance', 15, 2)->default(0);
              $table->timestamp('last_updated')->nullable();
              $table->timestamps();
              $table->unique('learner_id');
          });

          // Available payment methods
          Schema::create('payment_methods', function (Blueprint $table) {
              $table->id();
              $table->string('name'); // "BCA Virtual Account", "GoPay", "Visa Debit"
              $table->string('type'); // credit_card, qris, virtual_account, ewallet, bank_transfer
              $table->string('provider'); // visa, mastercard, gopay, bca, mandiri
              $table->decimal('fee_percentage', 5, 2)->default(0);
              $table->decimal('fee_fixed', 10, 2)->default(0);
              $table->boolean('is_active')->default(true);
              $table->json('config')->nullable(); // payment method specific settings
              $table->timestamps();
          });

          // Payment transactions
          Schema::create('payment_transactions', function (Blueprint $table) {
              $table->id();
              $table->string('transaction_id')->unique(); // TRX-20241119-XXXXX
              $table->foreignId('learner_id')->constrained('users')->onDelete('cascade');
              $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
              $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods');

              $table->decimal('amount', 10, 2); // course price
              $table->decimal('fee_amount', 10, 2)->default(0); // payment gateway fee
              $table->decimal('total_amount', 10, 2); // amount + fee

              $table->string('status'); // pending, processing, success, failed, expired, refunded
              $table->string('payment_type'); // full, wallet

              $table->json('payment_details')->nullable(); // card_number, va_number, qr_code, etc
              $table->json('gateway_response')->nullable(); // simulated gateway response

              $table->timestamp('expires_at'); // 24 hours from creation
              $table->timestamp('completed_at')->nullable();
              $table->timestamps();

              $table->index(['learner_id', 'status']);
              $table->index(['status', 'created_at']);
          });

          // Transaction status logs
          Schema::create('transaction_logs', function (Blueprint $table) {
              $table->id();
              $table->foreignId('transaction_id')->constrained('payment_transactions')->onDelete('cascade');
              $table->string('status');
              $table->json('log_data')->nullable();
              $table->text('notes')->nullable();
              $table->timestamps();
              $table->index(['transaction_id', 'timestamp']);
          });
      }

      public function down(): void
      {
          Schema::dropIfExists('transaction_logs');
          Schema::dropIfExists('payment_transactions');
          Schema::dropIfExists('payment_methods');
          Schema::dropIfExists('wallet_balances');
      }
  };