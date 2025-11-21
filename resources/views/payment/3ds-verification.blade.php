<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            3DS Verification - {{ $transaction->transaction_id }}
        </h2>
    </x-slot>

    <div class="py-12">
    <div class="max-w-md mx-auto py-8 px-4">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="text-center mb-6">
            <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold mb-2">3DS Verification</h1>
            <p class="text-gray-600">Complete your payment with 3D Secure verification</p>
        </div>

        <!-- Transaction Details -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h3 class="font-medium mb-2">Transaction Details</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span>Transaction ID:</span>
                    <span class="font-mono">{{ $transaction->transaction_id }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Course:</span>
                    <span>{{ $transaction->course->title }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Amount:</span>
                    <span class="font-bold">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- 3DS Form -->
        <form action="{{ route('payment.3ds.process', $transaction->transaction_id) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Enter OTP</label>
                <div class="relative">
                    <input type="text" name="otp" maxlength="6" pattern="[0-9]{6}" required
                           class="w-full px-3 py-3 border border-gray-300 rounded-lg text-center text-2xl font-mono tracking-widest focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="••••••">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">Demo: Use <strong>123456</strong> for testing</p>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition font-medium">
                Verify & Complete Payment
            </button>
        </form>

        <!-- Help Section -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
            <h4 class="font-medium text-blue-800 mb-2">How it works:</h4>
            <ol class="list-decimal list-inside space-y-1 text-sm text-blue-700">
                <li>Check your mobile banking app for OTP</li>
                <li>Enter the 6-digit code above</li>
                <li>Click verify to complete payment</li>
            </ol>
        </div>

        <div class="text-center mt-6">
            <a href="{{ route('payment.cancel', $transaction->transaction_id) }}"
               class="text-gray-600 hover:text-gray-800 text-sm">
                Cancel Payment
            </a>
        </div>
    </div>
</div>
</x-app-layout>