<?php

namespace App\Providers;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class SettingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     *
     * @codeCoverageIgnore
     */
    public function register()
    {
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('SystemSetting', SystemSetting::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     *
     * @codeCoverageIgnore
     */
    public function boot()
    {
        $this->loadAllSystemSettings();
    }

    /**
     * Presists all system settings into the session
     * config.
     *
     * @return void
     */
    public function loadAllSystemSettings()
    {
        // Ensure the table exists.
        if (count(Schema::getColumnListing('system_settings')) == 0) {
            return;
        }

        SystemSetting::persistAll();
    }
}
