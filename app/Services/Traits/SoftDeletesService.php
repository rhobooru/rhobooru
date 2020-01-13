<?php

namespace App\Services\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

use App\Exceptions\NotAuthenticatedException;

trait SoftDeletesService
{
    private function softDeletesFilters()
    {
        return [
            AllowedFilter::scope('with_trashed'),
            AllowedFilter::scope('only_trashed'),
        ];
    }

    /**
     * Permanently delete the model instance.
     * 
     * @param $model
     * @return bool|null
     */
    public function forceDelete($model) 
    {
        if($model == null)
        {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException();
        }

        if(is_numeric($model))
        {
            $subject = $this->model->withTrashed()->find($model);
        }
        else if($model instanceof \Illuminate\Database\Eloquent\Model)
        {
            $subject = $model;
        }

        if($subject == null)
        {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException();
        }

        if (!$this->canForceDelete($subject)) throw new \App\Exceptions\NotAuthorizedException();

        return $subject->forceDelete();
    }

    /**
     * Restore a soft-deleted model instance.
     * 
     * @param $model
     * @return bool|null
     */
    public function restore($model)
    {
        if($model == null)
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException();

        if(is_numeric($model))
        {
            $subject = $this->model->withTrashed()->findOrFail($model);
        }
        else if($model instanceof \Illuminate\Database\Eloquent\Model)
        {
            $subject = $model;
        }

        if($subject == null)
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException();

        if (!$this->canRestore($subject)) throw new \App\Exceptions\NotAuthorizedException();

        return $subject->forceDelete();
    }

    /**
     * Check if the model instance is soft-deleted.
     * 
     * @param $model
     * @return bool|null
     */
    public function isSoftDeleted($model)
    {
        if($model == null)
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException();

        if(is_numeric($model))
        {
            $subject = $this->model->withTrashed()->find($model);
        }
        else if($model instanceof \Illuminate\Database\Eloquent\Model)
        {
            $subject = $model;
        }

        if($subject == null)
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException();

        if (!$this->canView($subject)) 
            throw new \App\Exceptions\NotAuthorizedException();

        return $subject->trashed();
    }

    /**
     * Allow the query to include soft-deleted model instances.
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function withSoftDeleted($query)
    {
        return $query->withTrashed();
    }

    /**
     * Restrict the query to soft-deleted model instances.
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function onlySoftDeleted($query)
    {
        return $query->onlyTrashed();
    }
}