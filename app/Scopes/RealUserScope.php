<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class RealUserScope implements Scope
{
    /**
     * Only return real users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     *
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->where(static function($query) {
            // Don't return any system accounts,
            // except for the anonymous account.
            $query->where('system_account', false)
                ->orWhere('anonymous_account', true);
        });
    }
}
