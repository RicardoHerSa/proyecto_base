<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
namespace Database\Factories;
use App\Models\Menu\Menu;
use Faker\Generator as Faker;

$factory->define(Menu::class, function (Faker $faker) {
    
    $title = $faker->title;
    $menus = Menu::all();
    
    return [
        'title' => $title,
        'menutype' => $title,
        'parent_id' => (count($menus) > 0) ? $faker->randomElement($menus->pluck('id')->toArray()) : 0,
        'link' => $title
    ];
});
