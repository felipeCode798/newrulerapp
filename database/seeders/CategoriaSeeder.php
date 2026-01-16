<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            [
                'nombre' => 'Tránsito',
                'codigo' => 'TRANSITO',
                'descripcion' => 'Cursos relacionados con tránsito',
            ],
            [
                'nombre' => 'Renovación',
                'codigo' => 'RENOVACION',
                'descripcion' => 'Renovación de licencias',
            ],
            [
                'nombre' => 'Multas',
                'codigo' => 'MULTAS',
                'descripcion' => 'Gestión de multas de tránsito',
            ],
            [
                'nombre' => 'Embargos',
                'codigo' => 'EMBARGOS',
                'descripcion' => 'Procesos de embargo vehicular',
            ],
            [
                'nombre' => 'Controversias',
                'codigo' => 'CONTROVERSIAS',
                'descripcion' => 'Resolución de controversias',
            ],
        ];

        foreach ($categorias as $categoria) {
            Categoria::create($categoria);
        }
    }
}
