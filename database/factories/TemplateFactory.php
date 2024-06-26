<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Staff;
use App\Template;
use Faker\Generator as Faker;

$factory->define(Template::class, function (Faker $faker) {
    return [
        'creator_id' => function () {
            return factory(Staff::class)->create()->id;
        },
        'creator_type' => 'staff',
        'name' => $faker->text(25),
        'content' => $faker->paragraphs(1, true),
    ];
});
