<?php

namespace App\GraphQL\Queries;

class Config
{
    public function __invoke(): array
    {
        return config('rhobooru');
    }
}
