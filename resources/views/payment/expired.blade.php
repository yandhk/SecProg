<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Payment Expired
        </h2>
    </x-slot>

    <div class="py-12">
    <div class="max-w-md mx-auto py-8 px-4">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="text-center mb-6">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-red-800 mb-2">Payment Expired</h1>
            <p class="text-red-600">Your payment session has expired</p>
        </div>

        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <h3 class="font-medium text-red-800 mb-2">What happened?</h3>
            <p class="text-red-700 text-sm">Payment sessions expire after 24 hours for security reasons.</p>
        </div>

        <!-- Transaction Info -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h3 class="font-medium mb-2">Transaction Details</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span>Transaction ID:</span>
                    <span class="font-mono text-xs">{{ $transaction->transaction_id }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Course:</span>
                    <span>{{ $transaction->course->title }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Expired:</span>
                    <span>{{ $transaction->expires_at->format('d M Y H:i') }}</span>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-3">
            <a href="{{ route('courses.show', $transaction->course) }}"
               class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition font-medium text-center block">
                Try Payment Again
            </a>

            <a href="{{ route('learner.dashboard') }}"
               class="w-full bg-gray-200 text-gray-700 py-3 rounded-lg hover:bg-gray-300 transition font-medium text-center block">
                Go to Dashboard
            </a>
        </div>
    </div>
</div>
</x-app-layout>