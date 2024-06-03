<?php

namespace App\Providers;

use App\Services\StripePaymentService;
use App\Services\SwapMessageService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('StripePaymentService', function(){
            return new StripePaymentService();
        });

        $this->app->bind('SwapMessageService', function(){
            return new SwapMessageService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
//        JsonResource::withoutWrapping();

    }
}
