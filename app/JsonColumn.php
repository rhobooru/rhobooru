<?php

namespace App;

class JsonAttribute
{
    public $name;

    public $description;

    public $default;

    public $value;

    public $types;

    public $nullable = false;

    public function decode(): string
    {
        
    }

    public function encode(): string
    {

    }
}

class JsonGroup
{
    public $name;

    public $description;

    public $attributes;

    public function decode(): string
    {
        
    }

    public function encode(): string
    {

    }
}

class JsonColumn
{
    public $table;

    public $column;

    public $groups;
}