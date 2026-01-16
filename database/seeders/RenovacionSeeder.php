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
                'tipo' => 'solo_examen',
                'precio_cliente' => 80000,
            ],
            [
                'nombre' => 'Renovación Categoría A con Lámina',
                'tipo' => 'examen_lamina',
                'precio_cliente' => 120000,
            ],
            [
                'nombre' => 'Renovación Categoría B',
                'tipo' => 'solo_examen',
                'precio_cliente' => 100000,
            ],
            [
                'nombre' => 'Renovación Categoría B con Lámina',
                'tipo' => 'examen_lamina',
                'precio_cliente' => 150000,
            ],
            [
                'nombre' => 'Renovación Categoría C',
                'tipo' => 'solo_examen',
                'precio_cliente' => 120000,
            ],
            [
                'nombre' => 'Renovación Categoría C con Lámina',
                'tipo' => 'examen_lamina',
                'precio_cliente' => 180000,
            ],
        ];

        foreach ($renovaciones as $renovacion) {
            Renovacion::create($renovacion);
        }
    }
}
