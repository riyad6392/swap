<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class UserSpecificDataScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if ($this->defineGuard() == 'api') {
            $builder->where('user_id', auth()->id());
        }
    }

    protected function defineGuard(): int|string|null
    {
        foreach (array_keys(config('auth.guards')) as $guard) {

            if (auth()->guard($guard)->check()) return $guard;
        }
        return null;
    }
}
