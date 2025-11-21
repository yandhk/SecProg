<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\WalletBalance;

class InitializeWalletsSeeder extends Seeder
{
    public function run(): void
    {
        // Get all learners
        $learners = User::where('user_type', 'learner')->get();

        foreach ($learners as $learner) {
            // Create wallet balance if doesn't exist
            WalletBalance::firstOrCreate(
                ['learner_id' => $learner->id],
                ['balance' => 50000] // Give demo users Rp 50,000 starting balance
            );
        }

        $this->command->info('Wallet balances initialized for ' . $learners->count() . ' learners.');
    }
}