<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\SwapRequestNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Models\Notification as NotificationModel;


class SwapNotificationService: string
{

    public $swap = null;
    public $user = null;
    public $message = null;
    public function prepareData($swap, $user, $message): static
    {
        $this->swap = $swap;
        $this->user = $user;
        $this->message = $message;

        return $this;
    }
    public function sendNotification($swap, array $id, $message): void
    {
        $insertNotification = $swap->notifications()->create([
            'data' => [
                'swap_id' => $swap->id,
                'data'    => $message,
            ],
        ]);

       info('Notification created successfully', [$insertNotification]);

        $users = User::whereIn('id', $id)->get();

        $users->each(function ($user) use ($insertNotification, $swap, $message) {

            $user->notifications()->attach($insertNotification->id);
            Notification::send($user, new SwapRequestNotification($insertNotification));
        });

    }

    public function matchNotifiableType(){
        return $matchNotifiableType = [
            'App\Models\User' => [
                'message' => $this->message,
                'user'=> $this->user,
            ],
            'App\Models\Admin' => 'admin',
            'App\Models\Swap' => [
                'message' => $this->message,
                'swap' => $this->swap,
            ],
        ];
    }

}
