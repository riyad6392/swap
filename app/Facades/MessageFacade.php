<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class MessageFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'SwapMessageService';
    }

}
