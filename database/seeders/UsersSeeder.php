<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        // 2 Learners
        DB::table('users')->insert([
            [
                'name' => 'Ahmad Zakaria',
                'email' => 'zakaria@learner.com',
                'password' => Hash::make('Zakaria123'),
                'user_type' => 'learner',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tasha Mirelle',
                'email' => 'tmirelle@learner.com',
                'password' => Hash::make('mirelle123'),
                'user_type' => 'learner',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 2 Instructors
        DB::table('users')->insert([
            [
                'name' => 'Dimas Zuhri',
                'email' => 'dims@ins.com',
                'password' => Hash::make('Dims123123'),
                'user_type' => 'instructor',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hasan Darojat, Ph.D.',
                'email' => 'hasdar@ins.com',
                'password' => Hash::make('sansDar'),
                'user_type' => 'instructor',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
