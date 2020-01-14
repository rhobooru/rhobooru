<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\MediaFormat;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(MediaFormat::class, function (Faker $faker) {
    start:
    try {
        return [
            'extension' => $faker->unique()->fileExtension,
            'mime' => $faker->unique()->mimeType,
            'record_type_id' => \App\Models\RecordType::inRandomOrder()->first()->id,
        ];
    } catch (\Illuminate\Database\QueryException $e) {
        goto start;
    }
});