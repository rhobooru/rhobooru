<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Auth;
use App\Models\User;

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
        // Update our *_by_user_id columns whenever a 
        // timestamp column would be changed.

        static::creating(function($model) {
            $user_id = self::getUser();

            if($model->created_by_user_id == null)
                $model->created_by_user_id = $user_id;

            if($model->updated_by_user_id == null)
                $model->updated_by_user_id = $user_id;
        });

        static::updating(function($model) {
            $user_id = self::getUser();

            $model->updated_by_user_id = $user_id;
        });

        // deleting doesn't actually save afterwards, so we need to set and save the new column values.
        // However, we don't want to call updating erroneously, so we need to bypass the model
        // and run a raw database query.
        static::deleting(function($model) {
            $user_id = self::getUser();

            $query = $model->newQueryWithoutScopes()->where($model->getKeyName(), $model->getKey());

            $columns = ['deleted_by_user_id' => $user_id];

            $query->update($columns);
        });
    }

    /**
     * Retrieves the current user for auditing.
     *
     * @return integer:null
     */
    private static function getUser(): ?int
    {
        // If the user is logged in...
        if(Auth::guard('api')->check() || Auth::guard('web')->check())
        {
            // Return that user.
            return Auth::id();
        }

        // Else, return the anonymous user id.
        return config('rhobooru.users.anonymous_user_id');
    }

    /**
     * Called by models that use this trait whenever the model is retrieved.
     */
    public function initializeUserAudits()
    {
        // Make sure code can manually set these values, if desired.
        array_push($this->fillable, 
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
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedBy($query, $user)
    {
        if(is_numeric($user))
        {
            return $query->where('created_by_user_id', $user);
        }
        else if(is_a($user, User::class))
        {
            return $query->where('created_by_user_id', $user->id);
        }
        else
        {
            throw new Exception("Invalid parameter `user`");
        }
    }

    /**
     * Get models last updated by the given user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed                                  $user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUpdatedBy($query, $user)
    {
        if(is_numeric($user))
        {
            return $query->where('updated_by_user_id', $user);
        }
        else if(is_a($user, User::class))
        {
            return $query->where('updated_by_user_id', $user->id);
        }
        else
        {
            throw new Exception("Invalid parameter `user`");
        }
    }

    /**
     * Get models last soft deleted by the given user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed                                  $user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDeletedBy($query, $user)
    {
        if(is_numeric($user))
        {
            return $query->withTrashed()->where('deleted_by_user_id', $user);
        }
        else if(is_a($user, User::class))
        {
            return $query->withTrashed()->where('deleted_by_user_id', $user->id);
        }
        else
        {
            throw new Exception("Invalid parameter `user`");
        }
    }
}