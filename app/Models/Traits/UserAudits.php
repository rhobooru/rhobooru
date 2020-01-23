<?php

namespace App\Models\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

/**
 * Attaches created_by, updated_by, and deleted_by to a model
 * that tracks user IDs like last action dates.
 */
trait UserAudits
{
    /**
     * Called by models that use this trait whenever their boot() method is run.
     */
    public static function bootUserAudits()
    {
        static::creating(static function($model) {
            self::assignCreatingUser($model);
        });

        static::updating(static function($model) {
            self::assignUpdatingUser($model);
        });

        static::deleting(static function($model) {
            self::assignDeletingUser($model);
        });
    }

    /**
     * Assigns the current user as the created_by and updated_by user_id.
     *
     * @param object $model
     *
     * @return void
     */
    private static function assignCreatingUser($model)
    {
        $user_id = self::getUser();

        $model->created_by_user_id = $model->created_by_user_id ?? $user_id;

        $model->updated_by_user_id = $model->updated_by_user_id ?? $user_id;
    }

    /**
     * Assigns the current user as the updated_by user_id.
     *
     * @param object $model
     *
     * @return void
     */
    private static function assignUpdatingUser($model)
    {
        $model->updated_by_user_id = self::getUser();
    }

    /**
     * Assigns the current user as the deleted_by user_id.
     *
     * `deleting` doesn't actually save afterwards, so we need to set and save the new
     * column values. However, we don't want to call updating erroneously, so we need
     * to bypass the model and run a raw database query.
     *
     * @param object $model
     *
     * @return void
     */
    private static function assignDeletingUser($model)
    {
        $user_id = self::getUser();

        $query = $model->newQueryWithoutScopes()
            ->where($model->getKeyName(), $model->getKey());

        $columns = ['deleted_by_user_id' => $user_id];

        $query->update($columns);
    }

    /**
     * Retrieves the current user for auditing.
     *
     * @return int|null
     */
    private static function getUser(): ?int
    {
        // Else, return the anonymous user id.
        return Auth::id() ?? config('rhobooru.users.anonymous_user_id');
    }

    /**
     * Called by models that use this trait whenever the model is retrieved.
     */
    public function initializeUserAudits()
    {
        $this->addFillableFields();
    }

    protected function addFillableFields()
    {
        // Make sure code can manually set these values, if desired.
        array_push(
            $this->fillable,
            'created_by_user_id',
            'updated_by_user_id',
            'deleted_by_user_id'
        );
    }

    /**
     * Get the App\Models\User that created this record.
     */
    public function created_by()
    {
        return $this->belongsTo('App\Models\User', 'created_by_user_id');
    }

    /**
     * Get the App\Models\User that updated this record.
     */
    public function updated_by()
    {
        return $this->belongsTo('App\Models\User', 'updated_by_user_id');
    }

    /**
     * Get the App\Models\User that soft-deleted this record.
     */
    public function deleted_by()
    {
        return $this->belongsTo('App\Models\User', 'deleted_by_user_id');
    }

    /**
     * Get models created by the given user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed                                  $user
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedBy($query, $user)
    {
        if (is_numeric($user)) {
            return $query->where('created_by_user_id', $user);
        }

        if (is_a($user, User::class)) {
            return $query->where('created_by_user_id', $user->id);
        }

        throw new ModelNotFoundException('Invalid parameter `user`');
    }

    /**
     * Get models last updated by the given user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed                                  $user
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUpdatedBy($query, $user)
    {
        if (is_numeric($user)) {
            return $query->where('updated_by_user_id', $user);
        }

        if (is_a($user, User::class)) {
            return $query->where('updated_by_user_id', $user->id);
        }

        throw new ModelNotFoundException('Invalid parameter `user`');
    }

    /**
     * Get models last soft deleted by the given user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed                                  $user
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDeletedBy($query, $user)
    {
        if (is_numeric($user)) {
            return $query->withTrashed()
                ->where('deleted_by_user_id', $user);
        }

        if (is_a($user, User::class)) {
            return $query->withTrashed()
                ->where('deleted_by_user_id', $user->id);
        }

        throw new ModelNotFoundException('Invalid parameter `user`');
    }
}
