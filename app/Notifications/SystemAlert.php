<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SystemAlert extends Notification
{
    use Queueable;

    protected $message;
    protected $type;
    protected $link;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $message, string $type = 'success', string $link = null)
    {
        $this->message = $message;
        $this->type = $type;
        $this->link = $link;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->message,
            'type'    => $this->type,
            'link'    => $this->link,
        ];
    }
}
