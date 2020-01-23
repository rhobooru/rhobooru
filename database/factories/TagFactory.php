<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\Tag;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Tag::class, function (Faker $faker) {
    $firstUserId = \App\Models\User::first();
    if($firstUserId !== null)
    {
        $firstUserId = $firstUserId->id;
    }

    if(Auth::check())
    {
        $firstUserId = Auth::id();
    }

    return [
        'name' => $faker->unique()->md5,
        'description' => $faker->optional()->text,
        'created_by_user_id' => $firstUserId ?? factory(\App\Models\User::class)->create()->id,
        'updated_by_user_id' => $firstUserId ?? factory(\App\Models\User::class)->create()->id,
    ];
});