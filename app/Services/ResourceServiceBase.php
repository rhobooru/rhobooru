<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Exceptions\NotAuthenticatedException;
use App\Services\ServiceBase;

abstract class ResourceServiceBase extends ServiceBase
{
    protected $allowedIncludes = [];

    abstract function allowedFilters();

    protected $allowedSorts = [];

    /**
     * Permission to view any of this model.
     *
     * @return boolean
     */
    public function canViewAny()
    {
        return \Auth::check() && \Auth::user()->can('viewAny', $this->model());
    }

    /**
     * Permission to view this model.
     *
     * @return boolean
     */
    public function canView($instance)
    {
        return \Auth::check() && \Auth::user()->can('view', $instance);
    }

    /**
     * Permission to create this model.
     *
     * @return boolean
     */
    public function canCreate()
    {
        return \Auth::check() && \Auth::user()->can('create', $this->model());
    }

    /**
     * Permission to update this model.
     *
     * @return boolean
     */
    public function canUpdate($instance)
    {
        return \Auth::check() && \Auth::user()->can('update', $instance);
    }

    /**
     * Permission to delete this model.
     *
     * @return boolean
     */
    public function canDelete($instance)
    {
        return \Auth::check() && \Auth::user()->can('delete', $instance);
    }

    /**
     * Permission to restore this model.
     *
     * @return boolean
     */
    public function canRestore($instance)
    {
        return \Auth::check() && \Auth::user()->can('restore', $instance);
    }

    /**
     * Permission to force delete this model.
     *
     * @return boolean
     */
    public function canForceDelete($instance)
    {
        return \Auth::check() && \Auth::user()->can('forceDelete', $instance);
    }

    /**
     * Create and store a new instance of the model.
     * 
     * @param array $data
     * @param bool  $disableDefaultsAndOverrides
     * @return mixed
     */
    public function create(array $data, $disableDefaultsAndOverrides = false) 
    {
        if(!$this->canCreate()) throw new \App\Exceptions\NotAuthorizedException();

        // If our service has defined default values for creation...
        if(!$disableDefaultsAndOverrides && method_exists($this, 'createDefaults'))
        {
            // Map the $data values on top of the defaults.
            $data = array_merge($this->createDefaults(), $data);
        }

        // If our service has defined forced values for creation...
        if(!$disableDefaultsAndOverrides && method_exists($this, 'createOverrides'))
        {
            // Map the forced values on top of the $data.
            $data = array_merge($data, $this->createOverrides());
        }

        return $this->model->create($data);
    }
 
    /**
     * Update model instances.
     * 
     * @param $model
     * @param array $data
     * @param bool  $disableDefaultsAndOverrides
     * @return mixed
     */
    public function update($model, array $data, $disableDefaultsAndOverrides = false) 
    {
        if($model == null)
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException();

        // If our service has defined forced values for updating...
        if(!$disableDefaultsAndOverrides && method_exists($this, 'updateOverrides'))
        {
            // Map the forced values on top of the $data.
            $data = array_merge($data, $this->updateOverrides());
        }

        if(is_numeric($model))
        {
            $subject = $this->model->findOrFail($model);
        }
        else if($model instanceof \Illuminate\Database\Eloquent\Model)
        {
            $subject = $model;
        }

        if($subject == null)
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException();

        if (!$this->canUpdate($subject)) throw new \App\Exceptions\NotAuthorizedException();

        return $subject->update($data);
    }
 
    /**
     * Delete an instance of the model.
     * 
     * @param $model
     * @return bool|null
     */
    public function delete($model) 
    {
        if($model == null)
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException();

        if(is_numeric($model))
        {
            $subject = $this->model->find($model);
        }
        else if($model instanceof \Illuminate\Database\Eloquent\Model)
        {
            $subject = $model;
        }

        if($subject == null)
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException();

        if (!$this->canDelete($subject)) throw new \App\Exceptions\NotAuthorizedException();

        return $subject->delete();
    }
 
    /**
     * Find an instance of the model by PK.
     * 
     * @param $id
     * @return mixed
     */
    public function find($id) 
    {
        if($id == null)
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException();

        $query = QueryBuilder::for($this->model());
        
        if(is_array($this->allowedIncludes) && count($this->allowedIncludes) > 0)
        {
            $query->allowedIncludes($this->allowedIncludes);
        }

        $query->withoutGlobalScope(\Illuminate\Database\Eloquent\SoftDeletingScope::class);

        $subject = $query->find($id);

        if($subject == null)
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException();

        if (!$this->canView($subject)) throw new \App\Exceptions\NotAuthorizedException();

        return $subject;
    }
 
    /**
     * Retrieve a list of all model instances.
     * 
     * @return mixed
     */
    public function all() 
    {
        if (!$this->canViewAny()) throw new \App\Exceptions\NotAuthorizedException();

        $query = QueryBuilder::for($this->model());
        
        if(is_array($this->allowedSorts) && count($this->allowedSorts) > 0)
        {
            $query->allowedSorts($this->allowedSorts);
        }
        
        if(is_array($this->allowedIncludes) && count($this->allowedIncludes) > 0)
        {
            $query->allowedIncludes($this->allowedIncludes);
        }
        
        if(is_array($this->allowedFilters()) && count($this->allowedFilters()) > 0)
        {
            $query->allowedFilters($this->allowedFilters());
        }

        return $query->get();
    }
 
    /**
     * Retrieve a paginated list of all model instances.
     * 
     * @param int $perPage
     * @param array $columns
     * @return mixed
     */
    public function paginate($perPage = 15, $columns = array('*')) 
    {
        if (!$this->canViewAny()) throw new \App\Exceptions\NotAuthorizedException();

        return $this->model->paginate($perPage, $columns);
    }
}