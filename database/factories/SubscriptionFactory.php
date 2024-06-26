<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Subscription;
use Faker\Generator as Faker;

$factory->define(Subscription::class, function (Faker $faker) {
    return [
        'location_id' => function () {
            return factory(\App\Location::class)->create()->id;
        },
        'name' => 'Monthly Subscription',
        'stripe_id' => 'sub_FvoVJ5TkR7Z5mn', // Id from the fake payment gateway.
        'stripe_plan' => config('services.stripe.subscription_plan_id'),
        'stripe_status' => 'active',
        'quantity' => 1,
    ];
});
