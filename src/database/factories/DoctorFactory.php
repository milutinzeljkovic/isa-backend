<?php

use Faker\Generator as Faker;

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'lastName' => $faker->lastName,
        'email' => $faker->safeEmail,
        'address' => $faker->address,
        'password' => \Hash::make('secret'),
        'ensurance_id' => $faker->numerify('#############'),
        'activated' => '1',
        'has_loggedin' => '1',
        'confirmed' => '1',
        'city' => $faker->city,
        'state' => $faker->country,
        'phone_number' => $faker->numerify('##########'),

    ];
});
