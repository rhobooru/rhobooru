<?php

namespace App\Helpers;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionsHelper
{
    public static function clearPermissionsCache()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public static function enterAllPermissions()
    {
        self::clearPermissionsCache();

        $permissions = \Config::get('permissions');

        foreach ($permissions as $group => $group_permissions) {
            foreach ($group_permissions as $action) {
                Permission::create(['name' => $group . '.' . $action]);
            }
        }
    }
}
