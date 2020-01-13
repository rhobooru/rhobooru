<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    public function __construct()
    { }

    /**
     * Handle the user "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        $user->createUserRelationships();
    }

    /**
     * Handle the user "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        //
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        $user->softDeleteUserRelationships();
    }

    /**
     * Handle the user "restored" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        $user->restoreUserRelationships();
    }

    /**
     * Handle the user "force deleting" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function forceDeleting(User $user)
    {
        $user->forceDeleteUserRelationships();
    }

    /**
     * Handle the user "deleting" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleting(User $user)
    {
        if($user->isForceDeleting()) 
        {
            $this->forceDeleting($user);
        }
    }
}
