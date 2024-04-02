<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\SwapRequestNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;


enum SwapNotificationService: string
{
    case MESSAGE = 'Swap request has been sent';
    public static function sendNotification($swap): void
    {
        User::find($swap->exchanged_user_id)
            ->notifications()
            ->create([
                'type' => 'App\Notifications\SwapRequestNotification',
                'data' => [
                    'swap_id' => $swap->id,
                    'data' => self::MESSAGE,
                ],
                'notifi_by' => auth()->user()->id(),
            ]);

        Notification::send(User::find($swap->requested_user_id), new SwapRequestNotification($swap));
    }

}
