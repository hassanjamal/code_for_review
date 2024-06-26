<?php

use App\Appointment;
use App\Client;
use App\Location;
use App\Property;
use App\Staff;
use Faker\Generator as Faker;

$factory->define(Appointment::class, function (Faker $faker) {
    return [
        'api_id' => (string) $faker->randomNumber(7),
        'property_id' => function () {
            return optional(Property::first())->id ?? factory(\App\Property::class)->create()->id;
        },
        'location_id' => function ($appointment) {
            return factory(Location::class)->create(['property_id' => $appointment['property_id']])->id;
        },
        'location_api_id' => function ($appointment) {
            return (string) Location::find($appointment['location_id'])->api_id;
        },
        'id' => function ($appointment) {
            return makeTripleCompositeKey($appointment['property_id'], $appointment['location_api_id'], $appointment['api_id']);
        },
        'staff_id' => function ($appointment) {
            return (string) factory(Staff::class)->create(['property_id' => $appointment['property_id']])->id;
        },
        'staff_api_id' => function ($appointment) {
            return Staff::find($appointment['staff_id'])->api_id;
        },
        'client_api_public_id' => function () {
            return (string) factory(Client::class)->create()->api_public_id;
        },
        'duration' => 60,
        'status' => $faker->randomElement(['arrived', 'completed', 'missed', 'other']),
        'start_date_time' => now(),
        'end_date_time' => now()->addMinutes(60),
        'notes' => $faker->words(2, true),
        'staff_requested' => $faker->boolean() ,
        'service_id' => $faker->randomElement(['2', '5', '7']),
        'service_name' => $faker->randomElement(['Office Visit', 'Massage', 'Chiropractic Adjustment']),
        'first_appointment' => $faker->boolean() ,
        'room_name' => $faker->randomElement(['room 1', 'room 2', 'room 3']),
    ];
});
