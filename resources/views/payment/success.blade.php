<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Payment Successful
        </h2>
    </x-slot>

    <div class="py-12">
    <div class="max-w-4xl mx-auto py-8 px-4">
    <!-- Success Message -->
    <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-8">
        <div class="text-center">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-green-800 mb-2">Payment Successful!</h1>
            <p class="text-green-600">You are now enrolled in this course.</p>
        </div>
    </div>

    <!-- Transaction Details -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">Transaction Details</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="space-y-3">
                    <div>
                        <span class="text-gray-500 text-sm">Transaction ID:</span>
                        <p class="font-mono font-semibold">{{ $transaction->transaction_id }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500 text-sm">Payment Method:</span>
                        <p class="font-semibold">{{ $transaction->paymentMethod->name ?? 'Wallet' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500 text-sm">Amount:</span>
                        <p class="font-semibold">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</p>
                    </div>
                    @if($transaction->fee_amount > 0)
                    <div>
                        <span class="text-gray-500 text-sm">Fee:</span>
                        <p class="font-semibold">Rp {{ number_format($transaction->fee_amount, 0, ',', '.') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <div>
                <div class="space-y-3">
                    <div>
                        <span class="text-gray-500 text-sm">Total Amount:</span>
                        <p class="text-xl font-bold text-green-600">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500 text-sm">Status:</span>
                        <p class="font-semibold text-green-600">Completed</p>
                    </div>
                    <div>
                        <span class="text-gray-500 text-sm">Date:</span>
                        <p class="font-semibold">{{ $transaction->completed_at ? $transaction->completed_at->format('d M Y H:i') : $transaction->updated_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Information -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">Course Information</h2>

        <div class="flex flex-col md:flex-row gap-6">
            <div class="flex-shrink-0">
                @if($transaction->course->thumbnail)
                    <img src="{{ asset('storage/' . $transaction->course->thumbnail) }}"
                         alt="{{ $transaction->course->title }}"
                         class="w-32 h-32 object-cover rounded-lg">
                @else
                    <div class="w-32 h-32 bg-gray-200 rounded-lg flex items-center justify-center">
                        <span class="text-gray-500">No Image</span>
                    </div>
                @endif
            </div>

            <div class="flex-grow">
                <h3 class="text-xl font-semibold mb-2">{{ $transaction->course->title }}</h3>
                <p class="text-gray-600 mb-4">{{ \Illuminate\Support\Str::limit($transaction->course->description, 200) }}</p>
                <div class="text-sm text-gray-500">
                    Instructor: {{ $transaction->course->instructor->name ?? 'Unknown Instructor' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex flex-col md:flex-row gap-4">
            <a href="{{ route('learner.dashboard') }}"
               class="flex-1 bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition font-medium text-center">
                Go to Dashboard
            </a>

            <a href="{{ route('courses.show', $transaction->course) }}"
               class="flex-1 bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition font-medium text-center">
                Start Learning
            </a>
        </div>

        <div class="text-center mt-6">
            <a href="{{ route('courses.index') }}"
               class="text-blue-600 hover:text-blue-800">
                ← Browse More Courses
            </a>
        </div>
    </div>
</div>
</x-app-layout>