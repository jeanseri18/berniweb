<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@berrni.com');
        $password = env('ADMIN_PASSWORD', 'password');
        $phone = env('ADMIN_PHONE', '+33000000000');

        $admin = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => env('ADMIN_NAME', 'Super Admin'),
                'password' => Hash::make($password),
                'phone' => $phone,
                'role' => 'admin',
                'is_verified' => true,
                'is_courier' => false,
                'courier_status' => 'none',
            ]
        );

        Wallet::firstOrCreate(
            ['user_id' => $admin->id],
            ['balance_available' => 0, 'balance_sequestered' => 0]
        );
    }
}

