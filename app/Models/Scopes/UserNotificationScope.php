<?php

namespace App\Models\Scopes;

use App\Traits\ScopeTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class UserNotificationScope implements Scope
{
    use ScopeTrait;
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if ($this->defineGuard() == 'api') {
//            $builder->where('exchanger_id', auth()->id());
        }
    }

//    protected function defineGuard(): int|string|null
//    {
//        foreach (array_keys(config('auth.guards')) as $guard) {
//
//            if (auth()->guard($guard)->check()) return $guard;
//        }
//        return null;
//    }
}
