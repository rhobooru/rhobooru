<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\Record;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

$factory->define(Record::class, function (Faker $faker) {
    $defaultRecordTypeId = \App\Models\RecordType::first();
    if($defaultRecordTypeId !== null)
    {
        $defaultRecordTypeId = $defaultRecordTypeId->id;
    }

    $defaultContentRatingId = \App\Models\ContentRating::first();
    if($defaultContentRatingId !== null)
    {
        $defaultContentRatingId = $defaultContentRatingId->id;
    }

    return [
        'md5' => $faker->unique()->md5,
        'file_extension' => $faker->fileExtension,
        'record_type_id' => $defaultRecordTypeId ?? factory(\App\Models\RecordType::class)->create()->id,
        'content_rating_id' => $defaultContentRatingId ?? factory(\App\Models\ContentRating::class)->create()->id,
        'upload_complete' => true,
    ];
});

$factory->state(Record::class, 'approved', [
    'approved' => true,
]);

$factory->state(Record::class, 'unapproved', [
    'approved' => false,
]);