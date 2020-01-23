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
            $query->where('system_account', false) // Don't return any system accounts.
                ->orWhere('anonymous_account', true); // Except for the anonymous account.
        });
    }
}
