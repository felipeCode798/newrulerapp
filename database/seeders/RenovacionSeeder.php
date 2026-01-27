<?php

namespace Database\Seeders;

use App\Models\Renovacion;
use Illuminate\Database\Seeder;

class RenovacionSeeder extends Seeder
{
    public function run(): void
    {
        $renovaciones = [
            [
                'nombre' => 'Renovación Categoría A',
                'precio_renovacion' => 80000,
                'precio_examen' => 50000,
                'precio_lamina' => 30000,
                'activo' => true,
            ],
            [
                'nombre' => 'Renovación Categoría B',
                'precio_renovacion' => 90000,
                'precio_examen' => 55000,
                'precio_lamina' => 32000,
                'activo' => true,
            ],
            [
                'nombre' => 'Renovación Categoría C',
                'precio_renovacion' => 100000,
                'precio_examen' => 60000,
                'precio_lamina' => 35000,
                'activo' => true,
            ],
        ];

        foreach ($renovaciones as $renovacion) {
            Renovacion::create($renovacion);
        }
    }
}
