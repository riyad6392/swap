<?php

namespace App\Providers;

use App\Listeners\CacheHitListener;
use App\Listeners\CacheKeyForgottenListener;
use App\Listeners\CacheMissListener;
use App\Listeners\CacheWriteListener;
use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        'Illuminate\Cache\Events\CacheHit' => [
            CacheHitListener::class,
        ],

        'Illuminate\Cache\Events\CacheMissed' => [
            CacheMissListener::class,
        ],

        'Illuminate\Cache\Events\KeyForgotten' => [
            CacheKeyForgottenListener::class,
        ],

        'Illuminate\Cache\Events\KeyWritten' => [
            CacheWriteListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
