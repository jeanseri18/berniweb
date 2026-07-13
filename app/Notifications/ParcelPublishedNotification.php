<?php

namespace App\Notifications;

use App\Models\Parcel;
use Illuminate\Notifications\Notification;

class ParcelPublishedNotification extends Notification
{
    public function __construct(public Parcel $parcel)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $route = trim($this->parcel->departure_address).' → '.trim($this->parcel->destination_address);

        return [
            'title' => 'Colis publié',
            'message' => 'Votre colis est visible pour les relais. '.$route,
            'parcel_id' => $this->parcel->id,
            'route' => $route,
        ];
    }
}
