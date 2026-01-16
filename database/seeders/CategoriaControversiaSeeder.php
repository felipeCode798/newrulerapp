<?php

namespace Database\Seeders;

use App\Models\CategoriaControversia;
use Illuminate\Database\Seeder;

class CategoriaControversiaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            [
                'nombre' => 'Comparendo Tipo A',
                'codigo' => 'COMP-A',
                'precio_cliente' => 150000,
            ],
            [
                'nombre' => 'Comparendo Tipo B',
                'codigo' => 'COMP-B',
                'precio_cliente' => 200000,
            ],
            [
                'nombre' => 'Comparendo Tipo C',
                'codigo' => 'COMP-C',
                'precio_cliente' => 250000,
            ],
            [
                'nombre' => 'Comparendo Tipo D',
                'codigo' => 'COMP-D',
                'precio_cliente' => 300000,
            ],
            [
                'nombre' => 'Inmovilización',
                'codigo' => 'INM',
                'precio_cliente' => 350000,
            ],
        ];

        foreach ($categorias as $categoria) {
            CategoriaControversia::create($categoria);
        }
    }
}
