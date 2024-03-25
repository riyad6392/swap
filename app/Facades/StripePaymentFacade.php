<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class StripePaymentFacade extends Facade
{
    /**
     * @method static createCustomer(array $data) Create a new customer.
     * @method static createPrice(array $data) Create a new price.
     */
    protected static function getFacadeAccessor()
    {
        return 'StripePaymentService';
    }

}
