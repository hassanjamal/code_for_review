<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Client;
use App\Property;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Client::class, function (Faker $faker) {
    return [

        'property_id' => function () {
            return optional(Property::first())->id ?? factory(\App\Property::class)->create()->id;
        },
        'api_id' => $faker->randomNumber(8),
        'api_public_id' => function ($client) {
            return $client['api_id'];
        },
        'id' => function ($client) {
            return makeDoubleCompositeKey($client['property_id'], $client['api_id']);
        },
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'middle_name' => 'MiddleName',
        'gender' => $faker->randomElement(['male', 'female']),
        'email' => $faker->safeEmail,
        'birth_date' => Carbon::now()->subYears(30)->toDateTimeString(),
        'referred_by' => $faker->firstName,
        'first_appointment_date' => Carbon::now()->addDays(5)->toDateTimeString(),
        'photo_url' => $faker->imageUrl(),
        'status' => 'Non-Member',
    ];
});
