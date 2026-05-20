<?php

namespace Database\Seeders;

use App\Models\CinetpayPayment;
use App\Models\KycSubmission;
use App\Models\Message;
use App\Models\Parcel;
use App\Models\ParcelOffer;
use App\Models\SosAlert;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Jeu de données de démo : utilisateurs ID 1 à 20 + enregistrements liés dans chaque table métier.
 * Mot de passe commun : password
 */
class DemoUsersSeeder extends Seeder
{
    private const DEMO_USER_IDS = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20];

    private const PASSWORD = 'password';

    public function run(): void
    {
        $this->purgeDemoData();
        $this->seedUsers();
        $this->seedWallets();
        $this->seedKycSubmissions();
        $parcelIds = $this->seedParcels();
        $this->seedParcelOffers($parcelIds);
        $this->seedMessages($parcelIds);
        $this->seedTransactions();
        $this->seedSosAlerts($parcelIds);
        $this->seedNotifications();
        $this->seedCinetpayPayments($parcelIds);
        $this->resetAutoIncrements();

        $stats = sprintf(
            'users=%d parcels=%d offers=%d messages=%d',
            User::count(),
            Parcel::count(),
            ParcelOffer::count(),
            Message::count()
        );
        $this->command?->info("DemoUsersSeeder OK (password: password) — {$stats}");
    }

    private function purgeDemoData(): void
    {
        $ids = self::DEMO_USER_IDS;
        $parcelIds = Parcel::query()
            ->where(function ($q) use ($ids) {
                $q->whereIn('sender_id', $ids)->orWhereIn('courier_id', $ids);
            })
            ->pluck('id');

        $walletIds = Wallet::whereIn('user_id', $ids)->pluck('id');

        if ($parcelIds->isNotEmpty()) {
            DB::table('cinetpay_payments')->whereIn('parcel_id', $parcelIds)->delete();
            SosAlert::whereIn('parcel_id', $parcelIds)->delete();
            Message::whereIn('parcel_id', $parcelIds)->delete();
            ParcelOffer::whereIn('parcel_id', $parcelIds)->delete();
            Transaction::whereIn('parcel_id', $parcelIds)->delete();
            Parcel::whereIn('id', $parcelIds)->delete();
        }

        if ($walletIds->isNotEmpty()) {
            Transaction::whereIn('wallet_id', $walletIds)->delete();
        }

        DB::table('notifications')
            ->where('notifiable_type', User::class)
            ->whereIn('notifiable_id', $ids)
            ->delete();

        CinetpayPayment::whereIn('user_id', $ids)->delete();
        KycSubmission::whereIn('user_id', $ids)->delete();
        Wallet::whereIn('user_id', $ids)->delete();
        User::whereIn('id', $ids)->delete();
    }

    private function seedUsers(): void
    {
        $password = Hash::make(self::PASSWORD);
        $now = now();

        $profiles = [
            1 => ['name' => 'Admin BERRNI', 'email' => 'admin@berrni.com', 'phone' => '+2250700000001', 'role' => 'admin', 'is_courier' => false, 'courier_status' => 'none', 'is_sender' => false],
            2 => ['name' => 'Amadou Diallo', 'email' => 'amadou.diallo@demo.ci', 'phone' => '+2250700000002', 'role' => 'user', 'is_courier' => false, 'courier_status' => 'none', 'is_sender' => true],
            3 => ['name' => 'Fatou Bamba', 'email' => 'fatou.bamba@demo.ci', 'phone' => '+2250700000003', 'role' => 'user', 'is_courier' => false, 'courier_status' => 'none', 'is_sender' => true],
            4 => ['name' => 'Ibrahim Koné', 'email' => 'ibrahim.kone@demo.ci', 'phone' => '+2250700000004', 'role' => 'user', 'is_courier' => false, 'courier_status' => 'none', 'is_sender' => true],
            5 => ['name' => 'Aïcha Touré', 'email' => 'aicha.toure@demo.ci', 'phone' => '+2250700000005', 'role' => 'user', 'is_courier' => false, 'courier_status' => 'none', 'is_sender' => true],
            6 => ['name' => 'Moussa Coulibaly', 'email' => 'moussa.coulibaly@demo.ci', 'phone' => '+2250700000006', 'role' => 'user', 'is_courier' => false, 'courier_status' => 'none', 'is_sender' => true],
            7 => ['name' => 'Mariam Sanogo', 'email' => 'mariam.sanogo@demo.ci', 'phone' => '+2250700000007', 'role' => 'user', 'is_courier' => false, 'courier_status' => 'none', 'is_sender' => true],
            8 => ['name' => 'Yao N\'Guessan', 'email' => 'yao.nguessan@demo.ci', 'phone' => '+2250700000008', 'role' => 'user', 'is_courier' => false, 'courier_status' => 'none', 'is_sender' => true],
            9 => ['name' => 'Adjoua Kouamé', 'email' => 'adjoua.kouame@demo.ci', 'phone' => '+2250700000009', 'role' => 'user', 'is_courier' => false, 'courier_status' => 'none', 'is_sender' => true],
            10 => ['name' => 'Koffi Assi', 'email' => 'koffi.assi@demo.ci', 'phone' => '+2250700000010', 'role' => 'user', 'is_courier' => true, 'courier_status' => 'approved', 'is_sender' => true],
            11 => ['name' => 'Aya Traoré', 'email' => 'aya.traore@demo.ci', 'phone' => '+2250700000011', 'role' => 'user', 'is_courier' => true, 'courier_status' => 'approved', 'is_sender' => true],
            12 => ['name' => 'Seydou Camara', 'email' => 'seydou.camara@demo.ci', 'phone' => '+2250700000012', 'role' => 'user', 'is_courier' => true, 'courier_status' => 'approved', 'is_sender' => true],
            13 => ['name' => 'N\'Guessan Ble', 'email' => 'nguessan.ble@demo.ci', 'phone' => '+2250700000013', 'role' => 'user', 'is_courier' => true, 'courier_status' => 'approved', 'is_sender' => true],
            14 => ['name' => 'Affoué Yao', 'email' => 'affoue.yao@demo.ci', 'phone' => '+2250700000014', 'role' => 'user', 'is_courier' => true, 'courier_status' => 'approved', 'is_sender' => true],
            15 => ['name' => 'Lassina Ouattara', 'email' => 'lassina.ouattara@demo.ci', 'phone' => '+2250700000015', 'role' => 'user', 'is_courier' => true, 'courier_status' => 'approved', 'is_sender' => true],
            16 => ['name' => 'Brice Kouadio', 'email' => 'brice.kouadio@demo.ci', 'phone' => '+2250700000016', 'role' => 'user', 'is_courier' => false, 'courier_status' => 'pending', 'is_sender' => true],
            17 => ['name' => 'Clarisse Aka', 'email' => 'clarisse.aka@demo.ci', 'phone' => '+2250700000017', 'role' => 'user', 'is_courier' => false, 'courier_status' => 'pending', 'is_sender' => true],
            18 => ['name' => 'Didier Gnahoré', 'email' => 'didier.gnahore@demo.ci', 'phone' => '+2250700000018', 'role' => 'user', 'is_courier' => false, 'courier_status' => 'pending', 'is_sender' => true],
            19 => ['name' => 'Estelle Dago', 'email' => 'estelle.dago@demo.ci', 'phone' => '+2250700000019', 'role' => 'user', 'is_courier' => false, 'courier_status' => 'rejected', 'is_sender' => true],
            20 => ['name' => 'Franck Boti', 'email' => 'franck.boti@demo.ci', 'phone' => '+2250700000020', 'role' => 'user', 'is_courier' => false, 'courier_status' => 'none', 'is_sender' => true],
        ];

        User::unguarded(function () use ($profiles, $password, $now) {
            foreach ($profiles as $id => $profile) {
                User::create(array_merge($profile, [
                    'id' => $id,
                    'password' => $password,
                    'is_verified' => true,
                    'is_suspended' => false,
                    'email_verified_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }
        });
    }

    private function seedWallets(): void
    {
        $balances = [
            1 => [0, 0],
            2 => [45000, 5000],
            3 => [12000, 0],
            4 => [8000, 15000],
            5 => [25000, 0],
            6 => [5000, 0],
            7 => [32000, 8000],
            8 => [15000, 0],
            9 => [9000, 3000],
            10 => [18000, 12000],
            11 => [22000, 0],
            12 => [11000, 6000],
            13 => [30000, 4000],
            14 => [7500, 0],
            15 => [28000, 10000],
            16 => [0, 0],
            17 => [2000, 0],
            18 => [1000, 0],
            19 => [500, 0],
            20 => [6000, 0],
        ];

        foreach ($balances as $userId => [$available, $sequestered]) {
            Wallet::create([
                'user_id' => $userId,
                'balance_available' => $available,
                'balance_sequestered' => $sequestered,
            ]);
        }
    }

    private function seedKycSubmissions(): void
    {
        $placeholder = 'storage/kyc-documents/demo-placeholder.jpg';
        $modes = ['voiture', 'train', 'avion'];

        foreach ([10, 11, 12, 13, 14, 15] as $userId) {
            $mode = $modes[$userId % 3];
            KycSubmission::create([
                'user_id' => $userId,
                'id_card_front' => $placeholder,
                'id_card_back' => $placeholder,
                'selfie' => $placeholder,
                'selfie_with_id' => $placeholder,
                'transport_type' => $mode,
                'transport_mode' => $mode,
                'transport_model' => 'Toyota Corolla',
                'transport_plate' => 'AB-'.str_pad((string) $userId, 4, '0', STR_PAD_LEFT),
                'zone_hint' => 'Abidjan — Cocody',
                'availability_hint' => 'Lun–Sam 8h–20h',
                'payment_kind' => 'wallet',
                'status' => 'approved',
            ]);
        }

        foreach ([16, 17, 18] as $userId) {
            KycSubmission::create([
                'user_id' => $userId,
                'id_card_front' => $placeholder,
                'id_card_back' => $placeholder,
                'selfie' => $placeholder,
                'selfie_with_id' => $placeholder,
                'transport_type' => 'voiture',
                'transport_mode' => 'voiture',
                'transport_model' => 'Moto Yamaha',
                'transport_plate' => null,
                'zone_hint' => 'Bouaké centre',
                'availability_hint' => 'Week-ends',
                'payment_kind' => 'momo',
                'momo_number' => '+22507'.str_pad((string) $userId, 8, '0', STR_PAD_LEFT),
                'status' => 'pending',
            ]);
        }

        KycSubmission::create([
            'user_id' => 19,
            'id_card_front' => $placeholder,
            'id_card_back' => $placeholder,
            'selfie' => $placeholder,
            'selfie_with_id' => $placeholder,
            'transport_type' => 'train',
            'transport_mode' => 'train',
            'status' => 'rejected',
            'admin_notes' => 'Documents illisibles — merci de renvoyer un dossier complet.',
        ]);
    }

    /** @return array<int, int> parcel_id => sender_id */
    private function seedParcels(): array
    {
        $routes = [
            ['Abidjan, Plateau', 'Bouaké'],
            ['Abidjan, Yopougon', 'Yamoussoukro'],
            ['Bouaké', 'Korhogo'],
            ['San-Pédro', 'Abidjan, Marcory'],
            ['Daloa', 'Abidjan, Cocody'],
            ['Abidjan, Cocody', 'San-Pédro'],
            ['Man', 'Abidjan, Adjamé'],
            ['Gagnoa', 'Bouaké'],
        ];

        $parcelMap = [];
        $categories = ['documents', 'vetements', 'electronique', 'autre', 'alimentaire'];

        // —— Colis envoyés (publiés) : 2 par expéditeur 2–9 ——
        foreach (range(2, 9) as $senderId) {
            for ($n = 0; $n < 2; $n++) {
                $route = $routes[($senderId + $n) % count($routes)];
                $parcel = Parcel::create([
                    'sender_id' => $senderId,
                    'courier_id' => null,
                    'description' => "Colis envoyé #{$senderId}-{$n} — paquet scellé, remise en main propre",
                    'category' => $categories[($senderId + $n) % count($categories)],
                    'departure_address' => $route[0],
                    'destination_address' => $route[1],
                    'departure_date' => now()->addDays($n + 1),
                    'recipient_name' => 'Dest. '.$senderId.'-'.$n,
                    'recipient_phone' => '+22505'.str_pad((string) ($senderId * 10 + $n), 8, '0', STR_PAD_LEFT),
                    'recipient_note' => 'Merci d’appeler 30 min avant arrivée',
                    'price' => 4000 + ($senderId * 400) + ($n * 500),
                    'weight' => 1.5 + ($n * 1.2),
                    'fragile' => ($senderId + $n) % 2 === 0,
                    'status' => 'published',
                    'payment_status' => 'pending',
                ]);
                $parcelMap[$parcel->id] = $senderId;
            }
        }

        // Colis annulé (sans proposition)
        $cancelled = Parcel::create([
            'sender_id' => 9,
            'courier_id' => null,
            'description' => 'Colis annulé avant accord',
            'departure_address' => 'Abidjan',
            'destination_address' => 'Man',
            'departure_date' => now()->subDay(),
            'recipient_name' => 'Annulé',
            'recipient_phone' => '+22505030099',
            'price' => 4000,
            'weight' => 1.0,
            'status' => 'cancelled',
            'payment_status' => 'refunded',
        ]);
        $parcelMap[$cancelled->id] = 9;

        return $parcelMap;
    }

    private function seedParcelOffers(array $parcelMap): void
    {
        $couriers = [10, 11, 12, 13, 14, 15];
        $published = Parcel::where('status', 'published')->whereNull('courier_id')->orderBy('id')->get();

        // Propositions envoyées par chaque relais sur chaque colis publié
        foreach ($published as $parcelIndex => $parcel) {
            foreach ($couriers as $ci => $courierId) {
                $base = (float) $parcel->price;
                ParcelOffer::create([
                    'parcel_id' => $parcel->id,
                    'courier_id' => $courierId,
                    'amount' => max(2000, $base - (400 * $ci) + ($parcelIndex % 3) * 200),
                    'status' => 'pending',
                    'turns_used' => min($ci, 2),
                    'created_at' => now()->subHours(12 - $ci),
                    'updated_at' => now()->subHours(10 - $ci),
                ]);
            }
        }

        // Acceptation sur 1 colis par expéditeur 4–7 → messagerie + livraison démo
        // Expéditeurs 2, 3, 8, 9 gardent des colis publiés avec propositions pending
        $acceptCouriers = [10, 11, 12, 13];
        foreach ([4, 5, 6, 7] as $i => $senderId) {
            $parcel = $published->firstWhere('sender_id', $senderId);
            if ($parcel) {
                $this->acceptOfferForParcel($parcel, $acceptCouriers[$i]);
            }
        }

        // Expéditeur 2 : 1 colis accepté (messages), 1 colis reste publié (6 propositions pending)
        $sender2Parcels = $published->where('sender_id', 2)->values();
        if ($sender2Parcels->count() >= 2) {
            $this->acceptOfferForParcel($sender2Parcels->get(1), 10);
        } elseif ($sender2Parcels->count() === 1) {
            $this->acceptOfferForParcel($sender2Parcels->first(), 10);
        }

        // Expéditeur 3 : 1 colis publié avec propositions uniquement
        // (les 2 colis de 3 : le 1er reste published si pas dans 4-7 — 3 is not in 4-7, both stay published)
        // Expéditeur 8 et 9 : tous les colis restent published avec offres

        // Rejet d’une proposition sur un colis encore publié
        $stillPublished = Parcel::where('status', 'published')->whereNull('courier_id')->first();
        if ($stillPublished) {
            $rejectOffer = ParcelOffer::where('parcel_id', $stillPublished->id)->where('courier_id', 14)->first();
            $rejectOffer?->update(['status' => 'rejected']);
        }
    }

    /**
     * Simule l’acceptation d’une proposition (comme OfferController::accept).
     */
    private function acceptOfferForParcel(Parcel $parcel, int $courierId): void
    {
        $offer = ParcelOffer::where('parcel_id', $parcel->id)
            ->where('courier_id', $courierId)
            ->first();

        if (!$offer) {
            return;
        }

        $offer->update(['status' => 'accepted', 'amount' => $offer->amount]);

        ParcelOffer::where('parcel_id', $parcel->id)
            ->where('id', '!=', $offer->id)
            ->where('status', 'pending')
            ->update(['status' => 'rejected']);

        $parcel->update([
            'courier_id' => $courierId,
            'status' => 'assigned',
            'price' => $offer->amount,
            'payment_status' => 'sequestered',
        ]);

        Message::create([
            'parcel_id' => $parcel->id,
            'sender_id' => $courierId,
            'content' => 'Proposition acceptée — la messagerie est ouverte pour organiser la course.',
            'is_system_message' => true,
            'created_at' => now()->subHours(6),
        ]);
    }

    private function seedMessages(array $parcelMap): void
    {
        $threads = [
            [
                ['from' => 'courier', 'text' => 'Bonjour ! Je peux transporter votre colis sur mon trajet. À quelle heure le dépôt ?'],
                ['from' => 'sender', 'text' => 'Bonjour, vers 9h à Plateau. Le destinataire est prévenu.'],
                ['from' => 'courier', 'text' => 'Parfait, RDV 9h. Je vous écris dès la prise en charge.'],
                ['from' => 'sender', 'text' => 'Merci beaucoup !'],
                ['from' => 'courier', 'text' => 'Colis récupéré, en route vers Bouaké.'],
            ],
            [
                ['from' => 'courier', 'text' => 'Bonjour, ma proposition est en ligne pour votre trajet.'],
                ['from' => 'sender', 'text' => 'Bonjour, le tarif me convient. Détails ici ?'],
                ['from' => 'courier', 'text' => 'Oui : dépôt gare Yopougon, OK pour vous ?'],
                ['from' => 'sender', 'text' => 'Oui, devant la station Total. T-shirt bleu.'],
            ],
            [
                ['from' => 'courier', 'text' => 'Disponible demain matin pour votre colis.'],
                ['from' => 'sender', 'text' => 'Parfait, environ 3 kg, fragile.'],
                ['from' => 'courier', 'text' => 'Sac renforcé prévu. Code remis à l’arrivée.'],
                ['from' => 'sender', 'text' => 'Super, merci.'],
            ],
            [
                ['from' => 'sender', 'text' => 'Bonjour, avez-vous besoin du numéro du destinataire ?'],
                ['from' => 'courier', 'text' => 'Oui s’il vous plaît, et la meilleure plage horaire.'],
                ['from' => 'sender', 'text' => 'Je vous envoie ça par message. Disponible 14h–18h.'],
            ],
        ];

        $withCourier = Parcel::whereNotNull('courier_id')->orderBy('id')->get();

        foreach ($withCourier as $idx => $parcel) {
            $senderId = $parcel->sender_id;
            $courierId = $parcel->courier_id;
            $script = $threads[$idx % count($threads)];

            if (! $parcel->messages()->where('is_system_message', true)->exists()) {
                Message::create([
                    'parcel_id' => $parcel->id,
                    'sender_id' => $courierId,
                    'content' => 'Proposition acceptée — la messagerie est ouverte pour organiser la course.',
                    'is_system_message' => true,
                    'created_at' => now()->subDays(2),
                ]);
            }

            foreach ($script as $i => $msg) {
                Message::create([
                    'parcel_id' => $parcel->id,
                    'sender_id' => $msg['from'] === 'courier' ? $courierId : $senderId,
                    'content' => $msg['text'],
                    'is_system_message' => false,
                    'read_at' => $i < 2 ? now()->subHours(4 - $i) : null,
                    'created_at' => now()->subHours(5 - $i),
                ]);
            }

            if ($idx === 0) {
                $parcel->update(['status' => 'in_transit', 'verification_code' => '847291']);
                Message::create([
                    'parcel_id' => $parcel->id,
                    'sender_id' => $courierId,
                    'content' => 'Mi-parcours — arrivée prévue vers 18h.',
                    'is_system_message' => false,
                    'created_at' => now()->subHour(),
                ]);
            } elseif ($idx === 1) {
                $parcel->update(['status' => 'picked_up']);
            } elseif ($idx === 2) {
                $parcel->update(['status' => 'delivered', 'payment_status' => 'released']);
                Message::create([
                    'parcel_id' => $parcel->id,
                    'sender_id' => $senderId,
                    'content' => 'Colis reçu, merci pour la livraison !',
                    'is_system_message' => false,
                    'created_at' => now()->subMinutes(30),
                ]);
            }
        }
    }

    private function seedTransactions(): void
    {
        $types = ['deposit', 'sequester', 'release', 'commission', 'withdrawal'];

        foreach (self::DEMO_USER_IDS as $userId) {
            $wallet = Wallet::where('user_id', $userId)->first();
            if (!$wallet) {
                continue;
            }

            Transaction::create([
                'wallet_id' => $wallet->id,
                'amount' => 10000 + ($userId * 1000),
                'type' => 'deposit',
                'description' => 'Dépôt initial démo user #'.$userId,
            ]);

            if ($userId >= 2 && $userId <= 9) {
                $parcel = Parcel::where('sender_id', $userId)->where('payment_status', 'sequestered')->first();
                if ($parcel) {
                    Transaction::create([
                        'wallet_id' => $wallet->id,
                        'amount' => $parcel->price,
                        'type' => 'sequester',
                        'parcel_id' => $parcel->id,
                        'description' => 'Séquestre colis #'.$parcel->id,
                    ]);
                }
            }

            if ($userId >= 10 && $userId <= 15) {
                Transaction::create([
                    'wallet_id' => $wallet->id,
                    'amount' => 1500,
                    'type' => 'commission',
                    'description' => 'Commission plateforme démo',
                ]);
            }
        }
    }

    private function seedSosAlerts(array $parcelMap): void
    {
        $inTransit = Parcel::where('status', 'in_transit')->first();
        if ($inTransit) {
            SosAlert::create([
                'user_id' => $inTransit->sender_id,
                'parcel_id' => $inTransit->id,
                'reason' => "Retard important non expliqué\n\nLe relais n'a pas répondu depuis 48h.",
                'status' => 'open',
            ]);
        }

        $delivered = Parcel::where('status', 'delivered')->first();
        if ($delivered) {
            SosAlert::create([
                'user_id' => $delivered->sender_id,
                'parcel_id' => $delivered->id,
                'reason' => 'Situation résolue après médiation.',
                'status' => 'resolved',
                'resolution_notes' => 'Contact établi — colis reçu conformément.',
            ]);
        }
    }

    private function seedNotifications(): void
    {
        $now = now();

        foreach (range(2, 9) as $userId) {
            $parcels = Parcel::where('sender_id', $userId)->get();
            foreach ($parcels as $pi => $parcel) {
                DB::table('notifications')->insert([
                    'id' => (string) Str::uuid(),
                    'type' => 'App\\Notifications\\ParcelPublishedNotification',
                    'notifiable_type' => User::class,
                    'notifiable_id' => $userId,
                    'data' => json_encode([
                        'title' => 'Colis publié',
                        'message' => 'Votre colis est visible pour les relais. '.$parcel->departure_address.' → '.$parcel->destination_address,
                        'parcel_id' => $parcel->id,
                    ], JSON_UNESCAPED_UNICODE),
                    'read_at' => $pi > 0 ? $now : null,
                    'created_at' => $now->copy()->subHours($userId + $pi),
                    'updated_at' => $now,
                ]);
            }

            $offerCount = ParcelOffer::whereIn('parcel_id', $parcels->pluck('id'))->where('status', 'pending')->count();
            if ($offerCount > 0) {
                DB::table('notifications')->insert([
                    'id' => (string) Str::uuid(),
                    'type' => 'App\\Notifications\\GenericDatabaseNotification',
                    'notifiable_type' => User::class,
                    'notifiable_id' => $userId,
                    'data' => json_encode([
                        'title' => 'Nouvelles propositions',
                        'message' => "{$offerCount} relais ont envoyé une proposition sur vos colis.",
                    ], JSON_UNESCAPED_UNICODE),
                    'read_at' => null,
                    'created_at' => $now->subHours(2),
                    'updated_at' => $now,
                ]);
            }
        }

        foreach (range(10, 15) as $courierId) {
            DB::table('notifications')->insert([
                'id' => (string) Str::uuid(),
                'type' => 'App\\Notifications\\GenericDatabaseNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $courierId,
                'data' => json_encode([
                    'title' => 'Nouvelle opportunité',
                    'message' => 'Un colis correspond à votre trajet habituel Abidjan → Bouaké.',
                ], JSON_UNESCAPED_UNICODE),
                'read_at' => null,
                'created_at' => $now->subMinutes($courierId * 5),
                'updated_at' => $now,
            ]);
        }
    }

    private function seedCinetpayPayments(array $parcelMap): void
    {
        foreach ($parcelMap as $parcelId => $senderId) {
            $parcel = Parcel::find($parcelId);
            if (!$parcel || !in_array($parcel->status, ['published', 'assigned', 'in_transit'], true)) {
                continue;
            }

            $status = match ($parcel->payment_status) {
                'sequestered' => 'paid',
                'pending' => 'pending',
                default => 'initiated',
            };

            CinetpayPayment::create([
                'user_id' => $senderId,
                'parcel_id' => $parcelId,
                'amount' => $parcel->price,
                'currency' => 'XOF',
                'status' => $status,
                'provider' => 'cinetpay',
                'provider_payment_id' => 'DEMO-CP-'.$parcelId,
                'checkout_url' => 'https://checkout.cinetpay.com/demo/'.$parcelId,
                'paid_at' => $status === 'paid' ? now()->subHours(1) : null,
            ]);
        }
    }

    private function resetAutoIncrements(): void
    {
        $driver = DB::getDriverName();
        if (!in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        $tables = [
            'users' => 21,
            'wallets' => Wallet::max('id') + 1,
            'kyc_submissions' => KycSubmission::max('id') + 1,
            'parcels' => Parcel::max('id') + 1,
            'parcel_offers' => ParcelOffer::max('id') + 1,
            'messages' => Message::max('id') + 1,
            'transactions' => Transaction::max('id') + 1,
            'sos_alerts' => SosAlert::max('id') + 1,
            'cinetpay_payments' => CinetpayPayment::max('id') + 1,
        ];

        foreach ($tables as $table => $next) {
            if ($next > 0) {
                DB::statement("ALTER TABLE `{$table}` AUTO_INCREMENT = ".(int) $next);
            }
        }
    }
}
