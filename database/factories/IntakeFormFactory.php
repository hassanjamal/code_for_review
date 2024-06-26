<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Client;
use App\FormTemplate;
use App\IntakeForm;
use Faker\Generator as Faker;

$factory->define(IntakeForm::class, function (Faker $faker) {
    return [
        'form_template_id' => function () {
            return optional(FormTemplate::first())->id ?? factory(FormTemplate::class)->create()->id;
        },
        'client_id' => function () {
            return factory(Client::class)->create()->id;
        },
    ];
});

$factory->state(IntakeForm::class, 'submitted', function ($faker) {
    return [
        'submitted_at' => now(),
    ];
});

$factory->state(IntakeForm::class, 'withKioskCode', function ($faker) {
    return [
        'kiosk_code' => sprintf("%03s-%03s", mt_rand(1, 999), mt_rand(1, 999)),
        'kiosk_code_expires_at' => now()->addHours(5),
    ];
});
