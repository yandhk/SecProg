<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UsersSeeder::class,             // 2 learners + 2 instructors
            PaymentMethodSeeder::class,     // payment methods
            InitializeWalletsSeeder::class, // wallet balances untuk learners
            CoursesSeeder::class,
        ]);
    }
}
