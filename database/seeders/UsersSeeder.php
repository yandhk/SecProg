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
                'email' => 'zakaria@learner.acad',
                'password' => Hash::make('Zakaria123'),
                'user_type' => 'learner',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tasha Mirelle',
                'email' => 'tmirelle@learner.acad',
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
                'email' => 'dims@ins.acad',
                'password' => Hash::make('Dims123123'),
                'user_type' => 'instructor',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hasan Darojat, Ph.D.',
                'email' => 'hasdar@ins.acad',
                'password' => Hash::make('sansDar'),
                'user_type' => 'instructor',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bagas as Admin',
                'email' => 'bagas@admin.acad',
                'password' => Hash::make('Gasngenggg'),
                'user_type' => 'admin',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
