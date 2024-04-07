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
            $builder->whereHas('users', function ($query) {
                $query->where('user_id', auth()->id());
            });
        }
    }
}
