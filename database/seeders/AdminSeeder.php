<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Seed the application's database with an admin user.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@job2you.com'],
            [
                'name' => 'Admin Job2You',
                'email' => 'admin@job2you.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'phone' => '+261 34 00 000 00',
                'role' => 'admin',
                'status' => 'active',
                'plan' => 'free',
                'sms_quota' => 2,
                'sms_sent' => 0,
            ]
        );
    }
}
