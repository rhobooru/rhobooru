<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\SettingGroup;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(SettingGroup::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->word(),
    ];
});