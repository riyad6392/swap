<?php

namespace App\Traits;

trait ScopeTrait
{
    protected function defineGuard(): int|string|null
    {
        foreach (array_keys(config('auth.guards')) as $guard) {

            if (auth()->guard($guard)->check()) return $guard;
        }
        return null;
    }

}
