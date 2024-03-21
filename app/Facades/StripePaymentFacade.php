<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class StripePaymentFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'StripePaymentService';
    }

}
