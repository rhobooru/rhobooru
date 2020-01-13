<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

abstract class ServiceBase
{ 
    protected $model;
    
    public function __construct() {
        $this->makeModel();
    }
 
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    abstract function model();
 
    /**
     * @return \Illuminate\Database\Eloquent\Builder
     * @throws RepositoryException
     */
    public function makeModel() {
        $model = app()->make($this->model());
 
        if (!$model instanceof Model)
            throw new Exception("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
 
        return $this->model = $model->newQuery();
    }
}