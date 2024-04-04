<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SwapRequestNotification extends Notification
{
    use Queueable;

    protected object $swap;
    protected string $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(object $swap, string $message)
    {
        $this->swap = $swap;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->id,
            'swap_id' => $this->swap->id,
            'data' => $this->message,
            'requester_id' => $this->swap->requested_user_id,
            'exchanger_id' => (int) $this->swap->exchanged_user_id,
        ]);
    }
}
