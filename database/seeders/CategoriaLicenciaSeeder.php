<?php

namespace Database\Seeders;

use App\Models\CategoriaLicencia;
use Illuminate\Database\Seeder;

class CategoriaLicenciaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            [
                'nombre' => 'Categoría A1',
                'codigo' => 'A1',
                'examen_medico' => 50000,
                'lamina' => 30000,
                'honorarios' => 80000,
                'sin_curso' => 40000,
            ],
            [
                'nombre' => 'Categoría A2',
                'codigo' => 'A2',
                'examen_medico' => 50000,
                'lamina' => 30000,
                'honorarios' => 100000,
                'sin_curso' => 50000,
            ],
            [
                'nombre' => 'Categoría B1',
                'codigo' => 'B1',
                'examen_medico' => 60000,
                'lamina' => 35000,
                'honorarios' => 120000,
                'sin_curso' => 60000,
            ],
            [
                'nombre' => 'Categoría B2',
                'codigo' => 'B2',
                'examen_medico' => 60000,
                'lamina' => 35000,
                'honorarios' => 150000,
                'sin_curso' => 70000,
            ],
            [
                'nombre' => 'Categoría C1',
                'codigo' => 'C1',
                'examen_medico' => 70000,
                'lamina' => 40000,
                'honorarios' => 180000,
                'sin_curso' => 80000,
            ],
        ];

        foreach ($categorias as $categoria) {
            CategoriaLicencia::create($categoria);
        }
    }
}
