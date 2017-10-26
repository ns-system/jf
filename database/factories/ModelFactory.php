<?php

/*
  |--------------------------------------------------------------------------
  | モデルファクトリー
  |--------------------------------------------------------------------------
  |
  | ここに全部のモデルファクトリーを定義してください。モデルファクトリーは
  | テストのためにデータベースの初期値を用意したモデルを作成する便利な方法です。
  | モデルがどのように見えれば良いのかをファクトリーに指示するだけです。
  |
 */

$factory->define(App\User::class, function (Faker\Generator $faker) {
    $first_name = $faker->firstName;
    $last_name  = $faker->lastName;
    return [
        'first_name'     => $first_name,
        'last_name'      => $last_name,
        'name'           => $first_name . ' ' . $last_name,
        'email'          => $faker->safeEmail,
        'password'       => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\SinrenUser::class, function (Faker\Generator $faker) {
    return [
        'user_id'     => $faker->randomDigitNotNull,
        'division_id' => $faker->randomDigitNotNull,
    ];
});

$factory->define(App\Emotion::class, function (Faker\Generator $faker) {
    $start = date('Y-m-01');
    $end   = date('Y-m-t');
    return [
        'user_id'    => $faker->randomDigitNotNull,
        'emotion'    => rand(1, 3),
        'entered_on' => $faker->dateTimeBetween($start, $end)->format('Y-m-d'),
        'comment'    => $faker->sentence,
    ];
});
$factory->define(App\SinrenDivision::class, function (Faker\Generator $faker) {
    return [
        'division_name'     => $faker->randomDigitNotNull,
        'division_id' => $faker->randomDigitNotNull,
    ];
});