<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(User::class, function (Faker $faker) {
    return [
        'username' => $faker->name,
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' => Str::random(10),
        // 'site_theme_id' => factory(\App\Models\SiteTheme::class)->create()->id,
        // 'date_format_id' => factory(\App\Models\DateFormat::class)->create()->id,
        // 'record_fit_id' => factory(\App\Models\RecordFit::class)->create()->id,
        // 'maximum_content_rating_id' => factory(\App\Models\ContentRating::class)->create()->id,
    ];
});
