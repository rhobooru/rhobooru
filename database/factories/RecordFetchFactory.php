<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\RecordFetch;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(RecordFetch::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->word,
        'description' => $faker->optional()->text,
        'is_default' => false,
    ];
});
