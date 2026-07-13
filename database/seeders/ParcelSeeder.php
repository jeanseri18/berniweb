<?php

namespace Database\Seeders;

use App\Models\Parcel;
use App\Models\User;
use Illuminate\Database\Seeder;

class ParcelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $senders = User::where('is_courier', false)->where('role', 'user')->get();
        $couriers = User::where('is_courier', true)->where('courier_status', 'approved')->get();

        if ($senders->count() === 0) {
            return;
        }

        // Les annonces « marché relais » viennent des colis créés par les utilisateurs (app mobile).
        // On ne seed plus de colis publiés factices pour éviter le mélange avec les vraies données.

        // 1. Assigned/In Transit Parcels
        if ($couriers->count() > 0) {
            foreach ($senders->skip(5)->take(3) as $sender) {
                Parcel::create([
                    'sender_id' => $sender->id,
                    'courier_id' => $couriers->random()->id,
                    'description' => fake()->sentence(6),
                    'departure_address' => fake()->city,
                    'destination_address' => fake()->city,
                    'departure_date' => fake()->dateTimeBetween('-1 days', '+1 days'),
                    'recipient_name' => fake()->name,
                    'recipient_phone' => fake()->phoneNumber,
                    'price' => fake()->randomFloat(2, 10, 100),
                    'weight' => fake()->randomFloat(2, 1, 20),
                    'status' => fake()->randomElement(['assigned', 'picked_up', 'in_transit']),
                    'payment_status' => 'sequestered',
                ]);
            }
        }

        // 3. Delivered Parcels
        if ($couriers->count() > 0) {
            foreach ($senders->skip(8)->take(2) as $sender) {
                Parcel::create([
                    'sender_id' => $sender->id,
                    'courier_id' => $couriers->random()->id,
                    'description' => fake()->sentence(6),
                    'departure_address' => fake()->city,
                    'destination_address' => fake()->city,
                    'departure_date' => fake()->dateTimeBetween('-1 week', '-1 days'),
                    'recipient_name' => fake()->name,
                    'recipient_phone' => fake()->phoneNumber,
                    'price' => fake()->randomFloat(2, 10, 100),
                    'weight' => fake()->randomFloat(2, 1, 20),
                    'status' => 'delivered',
                    'payment_status' => 'released',
                ]);
            }
        }
    }
}
