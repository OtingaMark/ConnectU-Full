<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DemoPlatformNotification extends Notification
{
    use Queueable;

    /**
     * Initialize class dependencies.
     */
    public function __construct(
        private readonly string $eventType,
        private readonly string $title,
        private readonly string $message,
        private readonly array $meta = []
    ) {
    }

    /**
     * Define which notification channels should be used.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Convert the notification payload into an array representation.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'event_type' => $this->eventType,
            'title' => $this->title,
            'message' => $this->message,
            'meta' => $this->meta,
        ];
    }
}
