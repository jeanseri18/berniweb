<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class AdminBroadcastNotification extends Notification
{
    public function __construct(
        public string $title,
        public string $message,
    ) {
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
        return [
            'title' => $this->title,
            'message' => $this->message,
            'body' => $this->message,
            'source' => 'admin_broadcast',
        ];
    }
}
