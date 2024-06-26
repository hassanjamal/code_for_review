<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Client;
use App\Document;
use App\Staff;
use Faker\Generator as Faker;

$factory->define(Document::class, function (Faker $faker) {
    return [
        'staff_id' => function () {
            return (string)factory(Staff::class)->create()->id;
        },
        'client_id' => function ($document) {
            return (string)factory(Client::class)->create([
                'property_id' => Staff::whereId($document['staff_id'])->first()->property_id,
            ])->id;
        },
        'name' => $faker->words(3, true),
        'type' => 'file',
    ];
});
