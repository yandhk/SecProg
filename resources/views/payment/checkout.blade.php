<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Checkout - {{ $course->title }}
        </h2>
    </x-slot>

    <div class="py-12">
  <div class="max-w-4xl mx-auto py-8 px-4">
      <!-- Course Summary -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-8">
          <h1 class="text-2xl font-bold mb-4">Checkout</h1>

          <div class="flex flex-col md:flex-row gap-6">
              <div class="flex-shrink-0">
                  @if($course->thumbnail)
                      <img src="{{ asset('storage/' . $course->thumbnail) }}"
                           alt="{{ $course->title }}"
                           class="w-32 h-32 object-cover rounded-lg">
                  @else
                      <div class="w-32 h-32 bg-gray-200 rounded-lg flex items-center justify-center">
                          <span class="text-gray-500">No Image</span>
                      </div>
                  @endif
              </div>

              <div class="flex-grow">
                  <h2 class="text-xl font-semibold mb-2">{{ $course->title }}</h2>
                  <p class="text-gray-600 mb-4">{{ \Illuminate\Support\Str::limit($course->description, 150) }}</p>
                  <div class="flex items-center justify-between">
                      <div class="text-2xl font-bold text-blue-600">
                          Rp {{ number_format($course->price, 0, ',', '.') }}
                      </div>
                      <div class="text-sm text-gray-500">
                          Instructor: {{ $course->instructor->name ?? 'Unknown Instructor' }}
                      </div>
                  </div>
              </div>
          </div>
      </div>

      <!-- Wallet Balance Section -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-8">
          <h3 class="text-lg font-semibold mb-4 flex items-center">
              <svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                  <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110
  2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>
              </svg>
              Your Wallet Balance
          </h3>

          <div class="flex items-center justify-between mb-4">
              <div class="text-3xl font-bold text-green-600">
                  Rp {{ number_format($walletBalance->balance, 0, ',', '.') }}
              </div>

              @if($walletBalance->balance >= $course->price)
                  <form action="{{ route('payment.pay.wallet', $course->id) }}" method="POST">
                      @csrf
                      <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700
  transition">
                          Pay with Wallet
                      </button>
                  </form>
              @else
                  <div class="text-right">
                      <div class="text-sm text-red-600 mb-2">
                          Insufficient balance (Need Rp {{ number_format($course->price - $walletBalance->balance,
  0, ',', '.') }} more)
                      </div>
                      <button onclick="showAddFundsModal()" class="bg-blue-600 text-white px-6 py-3 rounded-lg
  hover:bg-blue-700 transition">
                          Add Funds
                      </button>
                  </div>
              @endif
          </div>
      </div>

      <!-- Payment Methods -->
      <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold mb-6">Choose Payment Method</h3>

          <form action="{{ route('payment.initiate', $course->id) }}" method="POST" id="paymentForm">
              @csrf

              @foreach($paymentMethods as $type => $methods)
                  <div class="mb-8">
                      <h4 class="font-medium mb-4 text-gray-700 text-uppercase">{{ ucfirst(str_replace('_', ' ',
  $type)) }}</h4>

                      @foreach($methods as $method)
                          <div class="payment-method-option border rounded-lg p-4 mb-3 cursor-pointer
  hover:border-blue-500 transition"
                               onclick="selectPaymentMethod({{ $method->id }})">

                              <label class="flex items-center cursor-pointer">
                                  <input type="radio" name="payment_method_id" value="{{ $method->id }}"
                                         class="mr-4 payment-radio" required>

                                  <div class="flex-grow">
                                      <div class="flex items-center justify-between">
                                          <div class="flex items-center">
                                              <span class="font-medium">{{ $method->name }}</span>
                                              @if($method->fee_percentage > 0 || $method->fee_fixed > 0)
                                                  <span class="ml-2 text-sm text-gray-500">
                                                      (+{{ $method->fee_percentage }}%{{ $method->fee_fixed > 0 ? '
  + Rp ' . number_format($method->fee_fixed, 0, ',', '.') : '' }})
                                                  </span>
                                              @endif
                                          </div>

                                          @if($method->fee_percentage > 0 || $method->fee_fixed > 0)
                                              <div class="text-sm text-gray-600">
                                                  Fee: Rp {{ number_format(($course->price * $method->fee_percentage
   / 100) + $method->fee_fixed, 0, ',', '.') }}
                                              </div>
                                          @endif
                                      </div>
                                  </div>
                              </label>
                          </div>
                      @endforeach
                  </div>
              @endforeach

              <div class="border-t pt-6 mt-8">
                  <div class="flex items-center justify-between mb-6">
                      <div class="text-lg">Total Amount:</div>
                      <div class="text-2xl font-bold text-blue-600" id="totalAmount">
                          Rp {{ number_format($course->price, 0, ',', '.') }}
                      </div>
                  </div>

                  <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700
  transition font-medium">
                      Proceed to Payment
                  </button>
              </div>
          </form>
      </div>
  </div>

  <!-- Add Funds Modal -->
  <div id="addFundsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
      <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
          <h3 class="text-lg font-semibold mb-4">Add Funds to Wallet</h3>

          <form action="{{ route('wallet.add.funds') }}" method="POST">
              @csrf
              <div class="mb-4">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Amount (IDR)</label>
                  <input type="number" name="amount" min="10000" max="10000000" step="1000" required
                         class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500
  focus:border-transparent"
                         placeholder="Min: Rp 10,000">
              </div>

              <div class="flex gap-3">
                  <button type="submit" class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700
  transition">
                      Add Funds
                  </button>
                  <button type="button" onclick="hideAddFundsModal()"
                          class="flex-1 bg-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-400 transition">
                      Cancel
                  </button>
              </div>
          </form>
      </div>
  </div>

  <script>
  function selectPaymentMethod(methodId) {
      document.querySelectorAll('.payment-method-option').forEach(el => {
          el.classList.remove('border-blue-500', 'bg-blue-50');
      });

      const selected = document.querySelector(`input[value="${methodId}"]`).closest('.payment-method-option');
      selected.classList.add('border-blue-500', 'bg-blue-50');
      selected.querySelector('input[type="radio"]').checked = true;

      calculateTotal();
  }

  function calculateTotal() {
      const selectedMethod = document.querySelector('input[name="payment_method_id"]:checked');
      if (!selectedMethod) return;

      const coursePrice = parseFloat({{ $course->price }});
      const methodId = selectedMethod.value;

      // Fetch payment method details (you'd typically have this in JavaScript)
      const fees = @json($paymentMethods->flatten()->mapWithKeys(function($method) {
          return [$method->id => [
              'percentage' => $method->fee_percentage,
              'fixed' => $method->fee_fixed
          ]];
      }));

      const fee = fees[methodId];
      const feePercentage = parseFloat(fee.percentage) || 0;
      const feeFixed = parseFloat(fee.fixed) || 0;
      const total = coursePrice + (coursePrice * feePercentage / 100) + feeFixed;

      document.getElementById('totalAmount').textContent =
          'Rp ' + total.toLocaleString('id-ID');
  }

  function showAddFundsModal() {
      document.getElementById('addFundsModal').classList.remove('hidden');
  }

  function hideAddFundsModal() {
      document.getElementById('addFundsModal').classList.add('hidden');
  }
  </script>
</x-app-layout>