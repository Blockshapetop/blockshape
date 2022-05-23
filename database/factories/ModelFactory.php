<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

use App\Product;
use App\ReferenceGroup;
use App\ReferenceProduct;
use App\User;
use App\Recipe;
use App\RecipeProduct;

$factory->define(User::class, function ($faker) {
    return [
        'name'           => $faker->name,
        'email'          => $faker->email,
        'phone'          => $faker->phoneNumber,
        'birth_date'     => $faker->date(),
        'password'       => str_random(10),
        'role'           => 'member',
        'active'         => rand(0, 1),
        'status'         => rand(0, 1),
        'remember_token' => str_random(10),
    ];
});

$factory->define(Recipe::class, function ($faker) {
    return [

    ];
});

$factory->define(Product::class, function ($faker) {
    return [
        'name' => $faker->sentence(3)
    ];
});

$units = ['g', 'ml', 'piece'];
$factory->define(RecipeProduct::class, function ($faker) use ($units) {
    $unit = $units[array_rand($units)];
    $weight = 'piece' == $unit ? 1 : (round($faker->numberBetween(100, 250) / 10) * 10);

    return [
        'product_id' => 'factory:App\Product',
        'weight'     => (int) $weight,
        'units'      => $unit
    ];
});

$factory->define(ReferenceGroup::class, function ($faker) {
    return [
        'name' => $faker->word
    ];
});

$factory->define(ReferenceProduct::class, function ($faker) {
    return [
        'group_id'      => factory(ReferenceGroup::class),
        'name'          => $faker->sentence(rand(1, 2)),
        'proteins'      => ($faker->numberBetween(0, 100) / 100),
        'lipids'        => ($faker->numberBetween(0, 100) / 100),
        'disaccharides' => ($faker->numberBetween(0, 100) / 100),
        'starch'        => ($faker->numberBetween(0, 100) / 100),
        'energy_value'  => $faker->numberBetween(30, 400)
    ];
});
















