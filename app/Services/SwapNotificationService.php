<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\SwapRequestNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;


enum SwapNotificationService: string
{
    case MESSAGE = 'Swap request has been sent';

    public static function sendNotification($swap, $id, $message): void
    {
        $user = User::find($id);

        $user->notifications()
            ->create([
                'type' => 'App\Notifications\SwapRequestNotification',
                'data' => [
                    'swap_id' => $swap->id,
                    'data' => $message,
                ],
                'notifi_by' => auth()->user()->id,
            ]);

        Notification::send($user, new SwapRequestNotification($swap, $message));
    }

}
