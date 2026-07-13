<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin is already in DatabaseSeeder, let's add more roles

        // 1. Couriers (Approved)
        User::factory()->count(5)->create([
            'role' => 'user',
            'is_courier' => true,
            'courier_status' => 'approved',
            'is_verified' => true,
        ])->each(function ($user) {
            Wallet::create(['user_id' => $user->id, 'balance_available' => rand(50, 500)]);
        });

        // 2. Couriers (Pending)
        User::factory()->count(3)->create([
            'role' => 'user',
            'is_courier' => false,
            'courier_status' => 'pending',
            'is_verified' => true,
        ])->each(function ($user) {
            Wallet::create(['user_id' => $user->id, 'balance_available' => 0]);
        });

        // 3. Regular Users (Senders)
        User::factory()->count(10)->create([
            'role' => 'user',
            'is_courier' => false,
            'courier_status' => 'none',
            'is_verified' => true,
        ])->each(function ($user) {
            Wallet::create(['user_id' => $user->id, 'balance_available' => rand(100, 1000)]);
        });
    }
}
