<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\TwitterUser;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(TwitterUser::class, function (Faker $faker) {
    $now = Carbon::now();
    return [
        'id' => $faker->randomNumber(),
        'user_id' => 1,
        'token' => strval($faker->randomNumber()),
        'token_secret' => strval($faker->randomNumber()),
        'screen' => $faker->name,
        'created_at' => $now,
        'updated_at' => $now,
    ];
});
