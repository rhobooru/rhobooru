<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\Setting;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Setting::class, function (Faker $faker) {
    return [
        'key' => $faker->unique()->words(3, true),
        'name' => $faker->unique()->words(3, true),
        'control' => $faker->randomElement(['textbox' ,'textarea', 'checkbox', 'select']),
    ];
});

$factory->state(Setting::class, 'system', [
    'system_setting' => true,
]);

$factory->state(Setting::class, 'user', [
    'system_setting' => false,
]);