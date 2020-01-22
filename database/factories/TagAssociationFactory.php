<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\TagAssociation;
use App\Models\TagAssociationType;
use App\Models\Tag;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(TagAssociation::class, function (Faker $faker) {
    return [
        'tag_id_1' => factory(Tag::class)->create()->id,
        'tag_id_2' => factory(Tag::class)->create()->id,
        'tag_association_type_id' => factory(TagAssociationType::class)->create()->id,
    ];
});