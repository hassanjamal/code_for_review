<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\FormSubmission;
use App\IntakeForm;
use Faker\Generator as Faker;

$factory->define(FormSubmission::class, function (Faker $faker) {
    return [
        'intake_form_id' => function () {
            return factory(IntakeForm::class)->create()->id;
        },
    ];
});
