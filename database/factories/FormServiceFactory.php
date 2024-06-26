<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\FormService;
use App\FormTemplate;
use Faker\Generator as Faker;

$factory->define(FormService::class, function (Faker $faker) {
    return [
        'form_template_id' => function () {
            return optional(FormTemplate::first())->id ?? factory(FormTemplate::class)->create()->id;
        },
        'service_id' => $faker->unique()->randomDigit,
    ];
});
