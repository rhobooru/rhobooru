<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\SettingGroup;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(SettingGroup::class, function (Faker $faker) {
    return [
        'key' => $faker->unique()->words(3, true),
        'name' => $faker->unique()->words(3, true),
    ];
});
