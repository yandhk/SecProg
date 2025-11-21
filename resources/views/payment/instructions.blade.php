<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Payment Instructions - {{ $transaction->transaction_id }}
        </h2>
    </x-slot>

    <div class="py-12">
    <div class="max-w-4xl mx-auto py-8 px-4">
      <!-- Transaction Header -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-8">
          <div class="flex items-center justify-between mb-4">
              <h1 class="text-2xl font-bold">Payment Instructions</h1>
              <div class="text-sm text-gray-500">
                  Transaction ID: {{ $transaction->transaction_id }}
              </div>
          </div>

          <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
              <div class="flex items-center">
                  <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75
  1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1
   0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                  </svg>
                  <span class="text-yellow-800">
                      Please complete your payment before: <strong>{{ $transaction->expires_at->format('d M Y H:i')
  }}</strong>
                  </span>
              </div>
          </div>
      </div>

      <!-- Course Information -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-8">
          <h3 class="text-lg font-semibold mb-4">Course Information</h3>

          <div class="flex flex-col md:flex-row gap-4">
              <div>
                  <h4 class="font-medium">{{ $transaction->course->title }}</h4>
                  <p class="text-gray-600 text-sm">Instructor: {{ $transaction->course->instructor->name }}</p>
              </div>

              <div class="text-right">
                  <div class="text-sm text-gray-500">Course Price</div>
                  <div class="text-lg font-semibold">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</div>
              </div>

              <div class="text-right">
                  <div class="text-sm text-gray-500">Fee</div>
                  <div class="text-lg font-semibold">Rp {{ number_format($transaction->fee_amount, 0, ',', '.')
  }}</div>
              </div>

              <div class="text-right">
                  <div class="text-sm text-gray-500">Total Amount</div>
                  <div class="text-xl font-bold text-blue-600">Rp {{ number_format($transaction->total_amount, 0,
  ',', '.') }}</div>
              </div>
          </div>
      </div>

      <!-- Payment Instructions Based on Method -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-8">
          <h3 class="text-lg font-semibold mb-4">
              {{ $transaction->paymentMethod->name }} Payment Instructions
          </h3>

          @switch($transaction->paymentMethod->type)
              @case('credit_card')
                  <div class="space-y-4">
                      <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                          <h4 class="font-medium mb-2">Credit Card Payment</h4>
                          <p class="text-gray-700 mb-4">Please complete your payment using the credit card
  information provided.</p>

                          @if($transaction->payment_details['3ds_required'] ?? false)
                              <div class="bg-orange-50 border border-orange-200 rounded p-3">
                                  <span class="text-orange-800">⚠️ 3DS verification required. Click below to
  continue.</span>
                              </div>
                          @endif
                      </div>

                      @if($transaction->payment_details['3ds_required'] ?? false)
                          <a href="{{ route('payment.3ds.verify', $transaction->transaction_id) }}"
                             class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition
  text-center block">
                              Complete 3DS Verification
                          </a>
                      @else
                          <button onclick="simulatePayment()"
                                  class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700
  transition">
                              Simulate Successful Payment
                          </button>
                      @endif
                  </div>
                  @break

              @case('qris')
                  <div class="space-y-4">
                      <div class="text-center">
                          <div class="bg-white border-2 border-gray-800 inline-block p-4 rounded-lg">
                              <!-- QR Code Placeholder - You can use a QR code library -->
                              <div class="w-48 h-48 bg-gray-200 flex items-center justify-center">
                                  <div class="text-center">
                                      <div class="text-4xl mb-2">📱</div>
                                      <div class="text-sm font-mono">{{ $transaction->payment_details['qr_code']
  }}</div>
                                  </div>
                              </div>
                          </div>

                          <p class="mt-4 text-gray-600">Scan this QR code with your e-wallet app</p>
                          <p class="text-sm text-gray-500">QR Code will expire in 24 hours</p>
                      </div>

                      <div class="bg-gray-50 rounded-lg p-4">
                          <h4 class="font-medium mb-2">How to Pay:</h4>
                          <ol class="list-decimal list-inside space-y-1 text-sm text-gray-700">
                              <li>Open your e-wallet app (GoPay, OVO, Dana, etc.)</li>
                              <li>Scan the QR code above</li>
                              <li>Confirm the payment amount</li>
                              <li>Enter your PIN to complete</li>
                          </ol>
                      </div>

                      <button onclick="simulatePayment()"
                              class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition">
                          Simulate QRIS Payment
                      </button>
                  </div>
                  @break

              @case('virtual_account')
                  <div class="space-y-4">
                      <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                          <h4 class="font-medium mb-4">Virtual Account Details</h4>

                          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                              <div>
                                  <label class="text-sm text-gray-600">Bank</label>
                                  <div class="text-lg font-semibold">{{ $transaction->payment_details['bank_name']
  }}</div>
                              </div>

                              <div>
                                  <label class="text-sm text-gray-600">Virtual Account Number</label>
                                  <div class="text-lg font-mono font-semibold">{{
  $transaction->payment_details['va_number'] }}</div>
                              </div>

                              <div>
                                  <label class="text-sm text-gray-600">Amount</label>
                                  <div class="text-lg font-semibold text-red-600">Rp {{
  number_format($transaction->total_amount, 0, ',', '.') }}</div>
                              </div>

                              <div>
                                  <label class="text-sm text-gray-600">Expires</label>
                                  <div class="text-sm">{{ $transaction->payment_details['expires_at'] }}</div>
                              </div>
                          </div>
                      </div>

                      <div class="bg-gray-50 rounded-lg p-4">
                          <h4 class="font-medium mb-2">Payment Instructions:</h4>
                          <ol class="list-decimal list-inside space-y-1 text-sm text-gray-700">
                              <li>Open your {{ $transaction->payment_details['bank_name'] }} mobile app or ATM</li>
                              <li>Select "Transfer" → "Transfer to Virtual Account"</li>
                              <li>Enter the Virtual Account number: <strong>{{
  $transaction->payment_details['va_number'] }}</strong></li>
                              <li>Enter the exact amount: <strong>Rp {{ number_format($transaction->total_amount, 0,
   ',', '.') }}</strong></li>
                              <li>Confirm and complete the transfer</li>
                          </ol>
                      </div>

                      <button onclick="simulatePayment()"
                              class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition">
                          Simulate VA Payment
                      </button>
                  </div>
                  @break

              @case('ewallet')
                  <div class="space-y-4">
                      <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
                          <h4 class="font-medium mb-4">{{ ucfirst($transaction->payment_details['wallet_type']) }}
  Payment</h4>

                          <div class="text-center mb-4">
                              <div class="text-6xl mb-2">
                                  @switch($transaction->payment_details['wallet_type'])
                                      @case('gopay')
                                          🟢
                                          @break
                                      @case('ovo')
                                          🟣
                                          @break
                                      @case('dana')
                                          🔵
                                          @break
                                      @case('shopeepay')
                                          🟠
                                          @break
                                  @endswitch
                              </div>

                              <div class="text-lg font-semibold">{{
  ucfirst($transaction->payment_details['wallet_type']) }}</div>
                              <div class="text-sm text-gray-600">Wallet ID: {{
  $transaction->payment_details['wallet_id'] }}</div>
                              <div class="text-lg font-bold text-red-600 mt-2">Rp {{
  number_format($transaction->total_amount, 0, ',', '.') }}</div>
                          </div>
                      </div>

                      <div class="bg-gray-50 rounded-lg p-4">
                          <h4 class="font-medium mb-2">Payment Instructions:</h4>
                          <ol class="list-decimal list-inside space-y-1 text-sm text-gray-700">
                              <li>Open your {{ ucfirst($transaction->payment_details['wallet_type']) }} app</li>
                              <li>Look for "Pay" or "Scan QR" menu</li>
                              <li>Enter your {{ ucfirst($transaction->payment_details['wallet_type']) }} number or
  scan QR code</li>
                              <li>Enter the payment amount: <strong>Rp {{ number_format($transaction->total_amount,
  0, ',', '.') }}</strong></li>
                              <li>Confirm payment with your PIN</li>
                          </ol>
                      </div>

                      <button onclick="simulatePayment()"
                              class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition">
                          Simulate {{ ucfirst($transaction->payment_details['wallet_type']) }} Payment
                      </button>
                  </div>
                  @break

              @case('bank_transfer')
                  <div class="space-y-4">
                      <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                          <h4 class="font-medium mb-4">Bank Transfer Details</h4>

                          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                              <div>
                                  <label class="text-sm text-gray-600">Bank</label>
                                  <div class="text-lg font-semibold">{{ $transaction->payment_details['bank_name']
  }}</div>
                              </div>

                              <div>
                                  <label class="text-sm text-gray-600">Account Number</label>
                                  <div class="text-lg font-mono font-semibold">{{
  $transaction->payment_details['account_number'] }}</div>
                              </div>

                              <div>
                                  <label class="text-sm text-gray-600">Account Name</label>
                                  <div class="text-lg font-semibold">{{
  $transaction->payment_details['account_name'] }}</div>
                              </div>

                              <div>
                                  <label class="text-sm text-gray-600">Amount</label>
                                  <div class="text-lg font-semibold text-red-600">Rp {{
  number_format($transaction->total_amount, 0, ',', '.') }}</div>
                              </div>
                          </div>

                          <div class="bg-yellow-50 border border-yellow-200 rounded p-3">
                              <strong>Important:</strong> Please include transaction ID <strong>{{
  $transaction->payment_details['notes'] }}</strong> in the transfer notes/remarks
                          </div>
                      </div>

                      <div class="bg-gray-50 rounded-lg p-4">
                          <h4 class="font-medium mb-2">Payment Instructions:</h4>
                          <ol class="list-decimal list-inside space-y-1 text-sm text-gray-700">
                              <li>Transfer from any bank to the account above</li>
                              <li>Transfer the exact amount: <strong>Rp {{ number_format($transaction->total_amount,
   0, ',', '.') }}</strong></li>
                              <li>Include transaction ID <strong>{{ $transaction->payment_details['notes']
  }}</strong> in the transfer notes</li>
                              <li>Keep your transfer receipt for verification</li>
                              <li>Payment will be verified within 1x24 hours</li>
                          </ol>
                      </div>

                      <button onclick="simulatePayment()"
                              class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition">
                          Simulate Bank Transfer
                      </button>
                  </div>
                  @break
          @endswitch
      </div>

      <!-- Action Buttons -->
      <div class="flex gap-4">
          <button onclick="checkPaymentStatus()"
                  class="flex-1 bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition">
              Check Payment Status
          </button>

          <a href="{{ route('payment.cancel', $transaction->transaction_id) }}"
             id="cancelBtn"
             class="flex-1 bg-gray-300 text-gray-700 py-3 rounded-lg hover:bg-gray-400 transition text-center">
              Cancel Payment
          </a>
      </div>
  </div>

  <script>
  function simulatePayment() {
          // Clear auto-check interval during simulation
          if (statusInterval) {
              clearInterval(statusInterval);
          }

          // Show loading state
          const simulateBtn = document.getElementById('simulateBtn');
          if (simulateBtn) {
              const originalText = simulateBtn.textContent;
              simulateBtn.textContent = 'Simulating...';
              simulateBtn.disabled = true;
          }

          // Call payment completion API
          fetch('{{ route("payment.check.status", $transaction->transaction_id) }}?simulate=1')
              .then(response => response.json())
              .then(data => {
                  if (data.status === 'success') {
                      window.location.href = '{{ route("payment.success", $transaction->transaction_id) }}';
                  } else {
                      // Fallback: redirect anyway for demo purposes
                      window.location.href = '{{ route("payment.success", $transaction->transaction_id) }}';
                  }
              })
              .catch(error => {
                  // Fallback: redirect anyway for demo purposes
                  console.log('Simulation fallback:', error);
                  window.location.href = '{{ route("payment.success", $transaction->transaction_id) }}';
              });
  }

  function checkPaymentStatus() {
      fetch('{{ route("payment.check.status", $transaction->transaction_id) }}')
          .then(response => response.json())
          .then(data => {
              if (data.status === 'success') {
                  if (statusInterval) {
                      clearInterval(statusInterval);
                  }
                  window.location.href = '{{ route("payment.success", $transaction->transaction_id) }}';
              } else if (data.status === 'expired') {
                  if (statusInterval) {
                      clearInterval(statusInterval);
                  }
                  window.location.href = '{{ route("payment.cancel", $transaction->transaction_id) }}';
              } else {
                  console.log('Payment status: ' + data.status);
              }
          })
          .catch(error => {
              console.error('Error checking payment status:', error);
          });
  }

  function cancelPayment() {
      // Clear auto-check interval
      if (statusInterval) {
          clearInterval(statusInterval);
      }

      // Redirect to cancel page
      window.location.href = '{{ route("payment.cancel", $transaction->transaction_id) }}';
  }

  function fixCancelButton() {
      const cancelBtn = document.querySelector('a[href*="payment.cancel"]');
      if (cancelBtn) {
          cancelBtn.onclick = function(e) {
              e.preventDefault();
              cancelPayment();
          };
      }
  }

  // Start auto-check payment status
  document.addEventListener('DOMContentLoaded', function() {
      fixCancelButton();
      statusInterval = setInterval(checkPaymentStatus, 5000); // Check every 5 seconds
  });
  </script>
</x-app-layout>