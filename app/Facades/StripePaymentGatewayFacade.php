<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class StripePaymentGatewayFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'greeting';
    }

}
