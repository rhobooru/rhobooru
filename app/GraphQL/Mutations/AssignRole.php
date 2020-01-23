<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use Spatie\Permission\Models\Role;

class AssignRole
{
    /**
     * Assign a role to a user.
     *
     * @param  mixed  $root
     * @param mixed  $args
     *
     * @return bool
     */
    public function __invoke($root, array $args): bool
    {
        $user_id = $args['user_id'];
        $role_id = $args['role_id'];

        $user = User::findOrFail($user_id);
        $role = Role::findOrFail($role_id);

        $user->assignRole($role);

        $user->refresh();

        return $user->hasRole($role);
    }
}
