<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\Profile;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Profile::class, function (Faker $faker) {
    $user = factory(\App\Models\User::class)->create();
    $user->profile->delete();

    return [
        'user_id' => $user->id,
        'email' => $faker->unique()->optional()->safeEmail,
        'email_verified_at' => now(),
        'bio' => $faker->optional()->text,
        'site_theme_id' => factory(\App\Models\SiteTheme::class)->create()->id,
        'date_format_id' => factory(\App\Models\DateFormat::class)->create()->id,
        'record_fit_id' => factory(\App\Models\RecordFit::class)->create()->id,
        'maximum_content_rating_id' => factory(\App\Models\ContentRating::class)->create()->id,
    ];
});
