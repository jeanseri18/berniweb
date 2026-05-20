<?php

namespace Database\Seeders;

use App\Models\KycSubmission;
use App\Models\User;
use Illuminate\Database\Seeder;

class KycSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users with pending courier status
        $pendingCouriers = User::where('courier_status', 'pending')->get();

        foreach ($pendingCouriers as $user) {
            KycSubmission::create([
                'user_id' => $user->id,
                'id_card_front' => 'https://via.placeholder.com/600x400.png?text=ID+Front', // Placeholder
                'id_card_back' => 'https://via.placeholder.com/600x400.png?text=ID+Back',
                'selfie' => 'https://via.placeholder.com/400x400.png?text=Selfie',
                'selfie_with_id' => 'https://via.placeholder.com/400x400.png?text=Selfie+ID',
                'transport_type' => fake()->randomElement(['Voiture', 'Moto', 'Camion', 'Train']),
                'status' => 'pending',
            ]);
        }
    }
}
