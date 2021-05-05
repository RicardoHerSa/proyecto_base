<?php

use App\Menu;
use Illuminate\Database\Seeder;

class MenusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $m1 = factory(Menu::class)->create([
            'title' => 'Opción 1',
            'menutype' => 'Menu raiz',
            'parent_id' => '0',
            'link' => 'prueba',
        ]);
        factory(Menu::class)->create([
            'title' => 'Opción 2',
            'menutype' => 'Menu raiz',
            'parent_id' => '0',
            'link' => 'prueba',
        ]);
        $m3 = factory(Menu::class)->create([
            'title' => 'Opción 3',
            'menutype' => 'Menu raiz',
            'parent_id' => '0',
            'link' => 'prueba',
        ]);
        $m4 = factory(Menu::class)->create([
            'title' => 'Opción 4',
            'menutype' => 'Menu raiz',
            'parent_id' => '0',
            'link' => 'prueba',
        ]);
        factory(Menu::class)->create([
            'title' => 'Opción 1.1',
            'menutype' => $m1->title,
            'parent_id' => $m1->id,
            'link' => 'prueba',
        ]);
        factory(Menu::class)->create([
            'title' => 'Opción 1.2',
            'menutype' => $m1->title,
            'parent_id' => $m1->id,
            'link' => 'prueba',
        ]);
        factory(Menu::class)->create([
            'title' => 'Opción 3.1',
            'menutype' => $m3->title,
            'parent_id' => $m3->id,
            'link' => 'prueba',
        ]);
        $m32 = factory(Menu::class)->create([
            'title' => 'Opción 3.2',
            'menutype' => $m3->title,
            'parent_id' => $m3->id,
            'link' => 'prueba',
        ]);
        factory(Menu::class)->create([
            'title' => 'Opción 4.1',
            'menutype' => $m4->title,
            'parent_id' => $m4->id,
            'link' => 'prueba',
        ]);
        factory(Menu::class)->create([
            'title' => 'Opción 3.2.1',
            'menutype' => $m32->title,
            'parent_id' => $m32->id,
            'link' => 'prueba',
        ]);
        factory(Menu::class)->create([
            'title' => 'Opción 3.2.2',
            'menutype' => $m32->title,
            'parent_id' => $m32->id,
            'link' => 'prueba',
        ]);
        factory(Menu::class)->create([
            'title' => 'Opción 3.2.3',
            'menutype' => $m32->title,
            'parent_id' => $m32->id,
            'link' => 'prueba',
        ]);
    }
}
