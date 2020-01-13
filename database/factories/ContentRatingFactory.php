<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\ContentRating;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(ContentRating::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->word(),
        'short_name' => $faker->unique()->word(),
        'description' => $faker->optional()->text,
        'order' => $faker->unique()->numberBetween(1, 65535),
    ];
});