<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            My Wallet
        </h2>
    </x-slot>

    <div class="py-12">
    <div class="max-w-4xl mx-auto py-8 px-4">
    <!-- Wallet Balance Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h1 class="text-2xl font-bold mb-6">My Wallet</h1>

        <div class="bg-green-50 border border-green-200 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-green-600 font-medium mb-1">Available Balance</div>
                    <div class="text-3xl font-bold text-green-700">
                        Rp {{ number_format($wallet->balance, 0, ',', '.') }}
                    </div>
                    <div class="text-sm text-green-600 mt-1">
                        Last updated: {{ $wallet->last_updated ? $wallet->last_updated->format('d M Y H:i') : 'Never' }}
                    </div>
                </div>

                <div class="text-6xl text-green-600">
                    💳
                </div>
            </div>
        </div>
    </div>

    <!-- Add Funds Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-lg font-semibold mb-4">Add Funds</h2>

        <form action="{{ route('wallet.add.funds') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Amount (IDR)</label>
                <div class="flex gap-2">
                    <input type="number" name="amount" min="10000" max="10000000" step="1000" required
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Min: Rp 10,000">
                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                        Add Funds
                    </button>
                </div>
                <p class="text-xs text-gray-500 mt-1">Demo feature: Add funds to your wallet for testing</p>
            </div>
        </form>
    </div>

    <!-- Wallet Transaction History -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold mb-4">Transaction History</h2>

        @if($walletTransactions->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Transaction ID
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Description
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Amount
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($walletTransactions as $transaction)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $transaction->transaction_id }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                @switch($transaction->payment_details['type'] ?? 'top_up')
                                    @case('top_up')
                                        Wallet Top Up
                                        @break
                                    @case('course_purchase')
                                        Course Purchase
                                        @break
                                    @default
                                        Wallet Transaction
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if(($transaction->payment_details['type'] ?? 'top_up') === 'top_up')
                                    <span class="text-green-600">+Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-red-600">-Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $transaction->completed_at ? $transaction->completed_at->format('d M Y H:i') : $transaction->created_at->format('d M Y H:i') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $walletTransactions->links() }}
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <div class="text-4xl mb-2">📋</div>
                <p>No transactions yet.</p>
                <p class="text-sm">Add funds to your wallet or purchase courses to see transaction history.</p>
            </div>
        @endif
    </div>
</div>
</x-app-layout>