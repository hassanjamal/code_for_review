<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

$factory->define(User::class, function (Faker $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' => Str::random(10),
    ];
});

$factory->state(User::class, 'super-admin', function () {
    return [
        'first_name' => 'Super Admin',
    ];
});


$factory->afterCreatingState(User::class, 'super-admin', function ($staff, $faker) {
    $staff->assignRole(Role::findByName('Super Admin', 'web'));
});
