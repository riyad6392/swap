<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\SwapRequestNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;


enum SwapNotificationService
{
    public static function sendNotification($swap): void
    {
        User::find(Auth::user()->id)
            ->notifications()
            ->create([
                'id' => uniqid(),
                'type' => 'App\Notifications\SwapRequestNotification',
                'notififor_id'=> $swap->requested_user_id,
                'data' => 'Swap request has been sent successfully',
                'notififor_id' => $swap->exchanged_user_id,
            ]);

        Notification::send(User::find($swap->requested_user_id), new SwapRequestNotification($swap));
    }

}
