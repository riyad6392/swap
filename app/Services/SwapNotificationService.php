<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\SwapRequestNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Models\Notification as NotificationModel;


enum SwapNotificationService: string
{

    public static function sendNotification($swap, array $id, $message): void
    {
        $insertNotification = $swap->notifications()->create([
            'data' => [
                'swap_id' => $swap->id,
                'data'    => $message,
            ],
        ]);

        $users = User::whereIn('id', $id)->get();

        $users->each(function ($user) use ($insertNotification, $swap, $message) {

            $user->notifications()->attach($insertNotification->id);
            Notification::send($user, new SwapRequestNotification($swap, $message));
        });

    }

}
