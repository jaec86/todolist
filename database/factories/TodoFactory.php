<?php

use App\Todo;
use App\User;
use Faker\Generator as Faker;

$factory->define(Todo::class, function (Faker $faker) {
    return [
        'user_id' => factory(User::class),
        'title' => $faker->sentence(),
        'description' => $faker->paragraph(),
        'tags' => null,
        'priority' => 1,
        'done' => 0
    ];
});
