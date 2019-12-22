<?php

use Faker\Generator as Faker;
use Illuminate\Support\Str;


$factory->define(App\AppointmentType::class, function (Faker $faker) {
    return [
        'name' => Str::random(10)
    ];
});
