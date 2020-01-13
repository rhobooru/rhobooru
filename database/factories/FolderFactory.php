<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\Folder;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Folder::class, function (Faker $faker) {
    $user = factory(\App\Models\User::class)->create();
    $user->folders()->delete();

    return [
        'created_by_user_id' => $user->id,
        'updated_by_user_id' => $user->id,
        'folder_type_id' => \App\Models\FolderType::book()->first()->id,
        'access_type_id' => \App\Models\AccessType::unlisted()->first()->id,
        'name' => $faker->word(),
    ];
});