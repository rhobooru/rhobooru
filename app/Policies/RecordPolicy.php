<?php

namespace App\Policies;

use App\Models\Record;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RecordPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any records.
     *
     * @param  \App\Models\User  $user
     *
     * @return mixed
     */
    public function viewAny(?User $user)
    {
    }

    /**
     * Determine whether the user can view the record.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Record  $record
     *
     * @return mixed
     */
    public function view(?User $user, Record $record)
    {
    }

    /**
     * Determine whether the user can create records.
     *
     * @param  \App\Models\User  $user
     *
     * @return mixed
     */
    public function create(?User $user)
    {
        $user = $user ?? User::anonymous();

        return $user->can('record.create');
    }

    /**
     * Determine whether the user can update the record.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Record  $record
     *
     * @return mixed
     */
    public function update(?User $user, Record $record)
    {
        $user = $user ?? User::anonymous();

        if ($record->created_by_user_id === $user->id) {
            return $user->can('record.update own');
        }

        return $user->can('record.update other');
    }

    /**
     * Determine whether the user can delete the record.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Record  $record
     *
     * @return mixed
     */
    public function delete(?User $user, Record $record)
    {
    }

    /**
     * Determine whether the user can restore the record.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Record  $record
     *
     * @return mixed
     */
    public function restore(?User $user, Record $record)
    {
    }

    /**
     * Determine whether the user can permanently delete the record.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Record  $record
     *
     * @return mixed
     */
    public function forceDelete(?User $user, Record $record)
    {
    }

    /**
     * Determine whether the user can add a tag to a record.
     *
     * @param  \App\Models\User     $user
     * @param  \App\Models\Record   $record
     * @param  array                $args
     *
     * @return bool
     */
    public function addTagToRecord(?User $user, Record $record): bool
    {
        $user = $user ?? User::anonymous();

        if ($record->created_by_user_id === $user->id) {
            return $user->can('record.add tag to own');
        }

        return $user->can('record.add tag to other');
    }

    /**
     * Determine whether the user can remove a tag from a record.
     *
     * @param  \App\Models\User     $user
     * @param  \App\Models\Record   $record
     * @param  array                $args
     *
     * @return bool
     */
    public function removeTagFromRecord(?User $user, Record $record): bool
    {
        $user = $user ?? User::anonymous();

        if ($record->created_by_user_id === $user->id) {
            return $user->can('record.remove tag from own');
        }

        return $user->can('record.remove tag from other');
    }
}
