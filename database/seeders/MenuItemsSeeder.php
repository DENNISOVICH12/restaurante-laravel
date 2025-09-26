<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuItemsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('menu_items')->insert([
            ['nombre' => 'Limonada',      'descripcion' => 'Natural',            'precio' => 9.99, 'imagen' => 'limonada.jpg',      'categoria' => 'bebida', 'disponible' => 1],
            ['nombre' => 'Jugo Naranja',  'descripcion' => 'Recien exprimido',  'precio' => 10.50,'imagen' => 'naranja.jpg',       'categoria' => 'bebida', 'disponible' => 1],
            ['nombre' => 'Hamburguesa',   'descripcion' => 'Clásica',           'precio' => 22.00,'imagen' => 'hamburguesa.jpg',    'categoria' => 'plato',  'disponible' => 1],
            ['nombre' => 'Pizza Mozz',    'descripcion' => 'Porción',           'precio' => 15.00,'imagen' => 'pizza.jpg',          'categoria' => 'plato',  'disponible' => 1],
            ['nombre' => 'Brownie',       'descripcion' => 'Con helado',        'precio' => 8.00, 'imagen' => 'brownie.jpg',        'categoria' => 'postre', 'disponible' => 1],
        ]);
    }
}
