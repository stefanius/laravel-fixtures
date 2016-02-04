<?php

$factory->define(App\Entities\Country::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'language' => 'N/A',
        'description' => $faker->text(200),
    ];
});