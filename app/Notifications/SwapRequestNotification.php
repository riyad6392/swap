<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SwapRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected object $insertNotification;
    protected string $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(object $insertNotification)
    {
        $this->insertNotification = $insertNotification;
//        $this->message = $message;
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
        return new BroadcastMessage((array)$this->insertNotification->toArray());
    }
}
