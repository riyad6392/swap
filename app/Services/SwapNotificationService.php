<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\SwapRequestNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Models\Notification as NotificationModel;


enum SwapNotificationService: string
{
    case MESSAGE = 'Swap request has been sent';

    public static function sendNotification($swap, $id, $message): void
    {

        $user = User::find($id);

        NotificationModel::create([
                'swap_id' => $swap->id,
                'requester_id' => auth()->user()->id,
                'exchanger_id' => $user->id,
                'data' => [
                    'swap_id' => $swap->id,
                    'data' => $message,
                ],
            ]);

        Notification::send($user, new SwapRequestNotification($swap, $message));
    }

}
