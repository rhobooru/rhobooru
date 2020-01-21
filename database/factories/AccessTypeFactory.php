<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\AccessType;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(AccessType::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->word(),
        'static_name' => $faker->unique()->word(),
    ];
});