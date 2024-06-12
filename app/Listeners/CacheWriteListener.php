<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CacheWriteListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        if ($event->key == 'active_users_1'){
            \Log::info("Cache write for key: {$event->key}");
        }
//        \Log::info("Cache write for key: {$event->key} with value: {$event->value}");

    }
}
