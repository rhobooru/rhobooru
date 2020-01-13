<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\DateFormat;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(DateFormat::class, function (Faker $faker) {
    $formats = [
        'yyyy-MM-dd',
        'MM/dd/yy',
        'hh:mm:ss',
        'MM/dd/yyyy',
        'MMMM dd, yyyy',
        'MMMM dd, yy',
    ];

    return [
        'format' => $faker->unique()->word,
        'is_default' => false,
    ];
});