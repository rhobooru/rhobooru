<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
        //
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \App\Models\Tag::observe(\App\Observers\TagObserver::class);
        \App\Models\Record::observe(\App\Observers\RecordObserver::class);
        \App\Models\User::observe(\App\Observers\UserObserver::class);
    }
}
