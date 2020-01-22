<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\TagAssociationType;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(TagAssociationType::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->word(),
        'description' => $faker->text,
    ];
});