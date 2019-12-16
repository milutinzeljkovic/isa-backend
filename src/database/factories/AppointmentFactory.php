<?php

use Faker\Generator as Faker;

$factory->define(Model::class, function (Faker $faker) {
    return [
        'date' => $faker->dateTimeBetween($startDate = 'now + 5 days', $endDate = 'now + 2 months'),
        'price' => $faker->randomElement(['1000', '1500', '4000', '10000', '25000']),
        'clinic_id' => $faker->randomElement(['1', '2', '3', '4', '5','6','7','8','9','10']),
        'operations_rooms_id' => $faker->randomElement(['1', '2', '3', '4', '5','6','7','8','9','10']),
        'appointment_type_id' => $faker->randomElement(['1', '2', '3', '4', '5','6','7','8','9','10']),
        'doctor_id' => $faker->randomElement(['1', '2', '3', '4', '5','6','7','8','9','10']),

    ];
});
