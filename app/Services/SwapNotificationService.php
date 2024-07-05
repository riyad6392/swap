<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\SwapRequestNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Models\Notification as NotificationModel;


enum SwapNotificationService: string
{
    public static function sendNotification($model, array $id, $message): void
    {
        //dd($message);
        $insertNotification = $model->notifications()->create([
            'data' => [
                'swap_id' => $model->id,
                'data'    => $message,
            ],
        ]);

       info('Notification created successfully', [$insertNotification]);

        $users = User::whereIn('id', $id)->get();

        $users->each(function ($user) use ($insertNotification, $model, $message) {

            $user->notifications()->attach($insertNotification->id);
            Notification::send($user, new SwapRequestNotification($insertNotification));
        });

    }

}
