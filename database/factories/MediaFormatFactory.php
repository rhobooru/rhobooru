<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\MediaFormat;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(MediaFormat::class, function (Faker $faker) {
    $extant_formats = MediaFormat::pluck('extension')->toArray();

    $ext = $extant_formats[0];

    while(in_array($ext, $extant_formats))
    {
        $ext = $faker->unique()->fileExtension;
    }

    return [
        'extension' => $ext,
        'mime' => $faker->unique()->mimeType,
        'record_type_id' => \App\Models\RecordType::inRandomOrder()->first()->id,
    ];
});