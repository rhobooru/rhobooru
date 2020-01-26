<?php

namespace App\Providers;

use App\Models\Record;
use App\Models\Setting;
use App\Models\SettingGroup;
use App\Models\Tag;
use App\Models\User;
use App\Observers\RecordObserver;
use App\Observers\SettingGroupObserver;
use App\Observers\SettingObserver;
use App\Observers\TagObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Tag::observe(TagObserver::class);
        Record::observe(RecordObserver::class);
        User::observe(UserObserver::class);
        Setting::observe(SettingObserver::class);
        SettingGroup::observe(SettingGroupObserver::class);
    }
}
