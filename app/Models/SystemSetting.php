<?php

namespace App\Models;

use Config;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'setting_id',
        'value',
    ];

    /**
     * Get the setting definition to which this setting instance belongs.
     */
    public function setting()
    {
        return $this->belongsTo('App\Models\Setting');
    }

    /**
     * Get the settings config key.
     *
     * @return string
     */
    public function getConfigKeyAttribute(): string
    {
        return $this->setting->config_key;
    }

    /**
     * Persists the setting in the in-memory config.
     *
     * @return void
     */
    public function persist()
    {
        Config::set($this->config_key, $this->value);
    }

    /**
     * Checks that the new value is valid for this setting's
     * options.
     *
     * @param mixed $new_value
     *
     * @return void
     */
    public function validate($new_value)
    {
        return $this->setting->validate($new_value);
    }

    /**
     * Persists all settings to the in-memory config.
     *
     * @return void
     */
    public static function persistAll()
    {
        $settings = SystemSetting::all();

        // Bind all settings to the in-memory config.
        foreach ($settings as $setting) {
            $setting->persist();
        }
    }
}
