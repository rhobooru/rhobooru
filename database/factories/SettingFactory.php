<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\SettinGroup;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(SettingGroup::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->word(),
    ];
});

$factory->state(Record::class, 'system', [
    'system_setting' => true,
]);

$factory->state(Record::class, 'user', [
    'system_setting' => false,
]);