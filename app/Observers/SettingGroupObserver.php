<?php

namespace App\Observers;

use App\Models\SettingGroup;

class SettingGroupObserver
{
    /**
     * Handle the setting group "created" event.
     *
     * @param  \App\Models\SettingGroup  $settingGroup
     * @return void
     */
    public function created(SettingGroup $settingGroup)
    {
        //
    }

    /**
     * Handle the setting group "updated" event.
     *
     * @param  \App\Models\SettingGroup  $settingGroup
     * @return void
     */
    public function updated(SettingGroup $settingGroup)
    {
        //
    }

    /**
     * Handle the setting group "updating" event.
     *
     * @param  \App\Models\SettingGroup  $settingGroup
     * @return void
     */
    public function updating(SettingGroup $settingGroup)
    {
        if ($settingGroup->isDirty('setting_group_id')) {
            $settingGroup->setHighestOrderNumber();
        }
    }

    /**
     * Handle the setting group "deleted" event.
     *
     * @param  \App\Models\SettingGroup  $settingGroup
     * @return void
     */
    public function deleted(SettingGroup $settingGroup)
    {
        //
    }

    /**
     * Handle the setting group "restored" event.
     *
     * @param  \App\Models\SettingGroup  $settingGroup
     * @return void
     */
    public function restored(SettingGroup $settingGroup)
    {
        //
    }

    /**
     * Handle the setting group "force deleted" event.
     *
     * @param  \App\Models\SettingGroup  $settingGroup
     * @return void
     */
    public function forceDeleted(SettingGroup $settingGroup)
    {
        //
    }
}
