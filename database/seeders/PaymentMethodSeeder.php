<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $paymentMethods = [
              // Credit Cards
              [
                  'name' => 'Visa Debit/Credit',
                  'type' => 'credit_card',
                  'provider' => 'visa',
                  'fee_percentage' => 2.5,
                  'fee_fixed' => 1000,
                  'config' => ['3ds_enabled' => true, 'min_3ds_amount' => 100000]
              ],
              [
                  'name' => 'Mastercard Debit/Credit',
                  'type' => 'credit_card',
                  'provider' => 'mastercard',
                  'fee_percentage' => 2.5,
                  'fee_fixed' => 1000,
                  'config' => ['3ds_enabled' => true, 'min_3ds_amount' => 100000]
              ],
              [
                  'name' => 'GPN Debit',
                  'type' => 'credit_card',
                  'provider' => 'gpn',
                  'fee_percentage' => 1.5,
                  'fee_fixed' => 1000,
                  'config' => ['3ds_enabled' => false]
              ],
            // QRIS
              [
                  'name' => 'QRIS',
                  'type' => 'qris',
                  'provider' => 'qris',
                  'fee_percentage' => 0.7,
                  'fee_fixed' => 0,
                  'config' => ['qr_expiry_hours' => 24]
              ],
    // Virtual Accounts
              [
                  'name' => 'BCA Virtual Account',
                  'type' => 'virtual_account',
                  'provider' => 'bca',
                  'fee_percentage' => 0,
                  'fee_fixed' => 5000,
                  'config' => ['va_prefix' => '88061']
              ],
              [
                  'name' => 'BNI Virtual Account',
                  'type' => 'virtual_account',
                  'provider' => 'bni',
                  'fee_percentage' => 0,
                  'fee_fixed' => 5000,
                  'config' => ['va_prefix' => '88028']
              ],
              [
                  'name' => 'BRI Virtual Account',
                  'type' => 'virtual_account',
                  'provider' => 'bri',
                  'fee_percentage' => 0,
                  'fee_fixed' => 5000,
                  'config' => ['va_prefix' => '88037']
              ],
              [
                  'name' => 'Mandiri Virtual Account',
                  'type' => 'virtual_account',
                  'provider' => 'mandiri',
                  'fee_percentage' => 0,
                  'fee_fixed' => 5000,
                  'config' => ['va_prefix' => '88012']
              ],

              // E-Wallets
              [
                  'name' => 'GoPay',
                  'type' => 'ewallet',
                  'provider' => 'gopay',
                  'fee_percentage' => 2,
                  'fee_fixed' => 1000,
                  'config' => ['deeplink_enabled' => true]
              ],
              [
                  'name' => 'OVO',
                  'type' => 'ewallet',
                  'provider' => 'ovo',
                  'fee_percentage' => 2,
                  'fee_fixed' => 1000,
                  'config' => ['deeplink_enabled' => true]
              ],
              [
                  'name' => 'DANA',
                  'type' => 'ewallet',
                  'provider' => 'dana',
                  'fee_percentage' => 2,
                  'fee_fixed' => 1000,
                  'config' => ['deeplink_enabled' => true]
              ],
              [
                  'name' => 'ShopeePay',
                  'type' => 'ewallet',
                  'provider' => 'shopeepay',
                  'fee_percentage' => 2,
                  'fee_fixed' => 1000,
                  'config' => ['deeplink_enabled' => true]
              ],

              // Bank Transfer
              [
                  'name' => 'Bank Transfer',
                  'type' => 'bank_transfer',
                  'provider' => 'bank_transfer',
                  'fee_percentage' => 0,
                  'fee_fixed' => 0,
                  'config' => ['verification_required' => true]
              ]
          ];

          foreach ($paymentMethods as $method) {
              PaymentMethod::create($method);
          }
    }
}