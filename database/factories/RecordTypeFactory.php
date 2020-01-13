<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\RecordType;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(RecordType::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->word,
        'description' => $faker->optional()->text,
    ];
});