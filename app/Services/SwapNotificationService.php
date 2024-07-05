<?php

namespace App\Services;

use App\Http\Resources\SwapResource;
use App\Http\Resources\UserResourceForMessage;
use App\Models\User;
use App\Notifications\SwapRequestNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Models\Notification as NotificationModel;


class SwapNotificationService
{

    public $models = null;
    public $ids = null;
    public $message = null;
    public $modelNameSpace = null;

    public function prepareData($models, $ids, $message): static
    {
        $this->models = $models;
        $this->ids = $ids;
        $this->message = $message;
        $this->modelNameSpace = 'App\Models\\'.class_basename($this->models);

        return $this;
    }

    public function sendNotification(): void
    {
        $insertNotification = $this->models->notifications()->create([
            'data' => $this->matchNotifiableType()
        ]);

       info('Notification created successfully', [$insertNotification]);

        $users = User::whereIn('id', $this->ids)->get();

        $users->each(function ($user) use ($insertNotification) {
            $user->notifications()->attach($insertNotification->id);
            Notification::send(
                $user,
                new SwapRequestNotification($insertNotification)
            );
        });

    }

    public function matchNotifiableType()
    {
        return match ($this->modelNameSpace) {
            'App\Models\User' => [
                'message' => $this->message,
                'sender' => new UserResourceForMessage(auth()->user()),
            ],
            'App\Models\Admin' => 'admin',
            'App\Models\Swap' => [
                'message' => $this->message,
                'swap' => new SwapResource($this->models),
                'sender' => new UserResourceForMessage(auth()->user()),
            ],
        };

    }

}
