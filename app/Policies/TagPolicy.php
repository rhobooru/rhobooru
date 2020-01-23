<?php

namespace App\Policies;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TagPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any tag.
     *
     * @param  \App\Models\User|null  $user
     *
     * @return mixed
     */
    public function viewAny(?User $user)
    {
        $user = $user ?? User::anonymous();

        return $user->can('tag.view any');
    }

    /**
     * Determine whether the user can view the tag.
     *
     * @param  \App\Models\User|null  $user
     * @param  \App\Models\Tag  $tag
     *
     * @return mixed
     */
    public function view(?User $user, Tag $tag)
    {
        $user = $user ?? User::anonymous();

        if (! $user->can('tag.view any')) {
            return false;
        }

        if ($tag->deleted_at !== null && ! $user->can('tag.view deleted')) {
            return false;
        }

        if ($tag->created_by_user_id === $user->id) {
            return $user->can('tag.view own');
        }

        return $user->can('tag.view other');
    }

    /**
     * Determine whether the user can create tag.
     *
     * @param  \App\Models\User  $user
     *
     * @return mixed
     */
    public function create(?User $user)
    {
        $user = $user ?? User::anonymous();

        return $user->can('tag.create');
    }

    /**
     * Determine whether the user can update the tag.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Tag  $tag
     *
     * @return mixed
     */
    public function update(?User $user, Tag $tag)
    {
        $user = $user ?? User::anonymous();

        if ($tag->created_by_user_id === $user->id) {
            return $user->can('tag.update own');
        }

        return $user->can('tag.update other');
    }

    /**
     * Determine whether the user can delete the tag.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Tag  $tag
     *
     * @return mixed
     */
    public function delete(?User $user, Tag $tag)
    {
        $user = $user ?? User::anonymous();

        if ($tag->created_by_user_id === $user->id) {
            return $user->can('tag.delete own');
        }

        return $user->can('tag.delete other');
    }

    /**
     * Determine whether the user can restore the tag.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Tag  $tag
     *
     * @return mixed
     */
    public function restore(?User $user, Tag $tag)
    {
        $user = $user ?? User::anonymous();

        return $user->can('tag.restore');
    }

    /**
     * Determine whether the user can permanently delete the tag.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Tag  $tag
     *
     * @return mixed
     */
    public function forceDelete(?User $user, Tag $tag)
    {
        $user = $user ?? User::anonymous();

        if ($tag->cached_record_count > 0) {
            return false;
        }

        return $user->can('tag.force delete');
    }
}
