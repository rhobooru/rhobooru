<?php

namespace App\GraphQL\Queries;

class Config
{
    public function __invoke(): Array
    {
        return config('rhobooru');
    }
}