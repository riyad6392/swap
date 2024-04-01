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

    protected string $message = 'You have a new swap request.';
    protected object $swap;

    /**
     * Create a new notification instance.
     */
    public function __construct(object $swap)
    {
        $this->swap = $swap;
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
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
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
            'notifiable_id' => $this->swap->requested_user_id,
            'notififor_id' => (int) $this->swap->exchanged_user_id,
        ]);
    }
}
