<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\SiteTheme;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(SiteTheme::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->word,
        'is_default' => false,
    ];
});