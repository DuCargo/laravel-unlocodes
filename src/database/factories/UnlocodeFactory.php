<?php

$factory->define(\Dc\Unlocodes\Unlocode::class, function (Faker\Generator $faker) {
    return [
        'countrycode' => 'NL',
        'placecode' => 'RTM',
        'name' => 'Rotterdam',
        'longitude' => null,
        'latitude' => null,
        'subdivision' => '',
        'status' => '',
        'date' => '',
        'IATA' => '',
    ];
});
