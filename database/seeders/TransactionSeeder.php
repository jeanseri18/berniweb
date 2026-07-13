<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Transactions are usually created by events, but we can seed some if needed later.
        // For now, WalletSeeder/UserSeeder handles initial balances.
    }
}
