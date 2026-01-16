<?php

namespace Database\Seeders;

use App\Models\Curso;
use Illuminate\Database\Seeder;

class CursoSeeder extends Seeder
{
    public function run(): void
    {
        $cursos = [
            [
                'codigo' => 'A1',
                'categoria' => 'Motocicleta',
                'precio_cliente_50_transito' => 150000,
                'precio_cliente_50_recibir' => 200000,
                'precio_cliente_20_transito' => 100000,
                'precio_cliente_20_recibir' => 130000,
            ],
            [
                'codigo' => 'A2',
                'categoria' => 'Motocicleta Mayor Cilindrada',
                'precio_cliente_50_transito' => 180000,
                'precio_cliente_50_recibir' => 230000,
                'precio_cliente_20_transito' => 120000,
                'precio_cliente_20_recibir' => 150000,
            ],
            [
                'codigo' => 'B1',
                'categoria' => 'Automóvil',
                'precio_cliente_50_transito' => 200000,
                'precio_cliente_50_recibir' => 250000,
                'precio_cliente_20_transito' => 150000,
                'precio_cliente_20_recibir' => 180000,
            ],
            [
                'codigo' => 'C1',
                'categoria' => 'Camión Rígido',
                'precio_cliente_50_transito' => 250000,
                'precio_cliente_50_recibir' => 300000,
                'precio_cliente_20_transito' => 180000,
                'precio_cliente_20_recibir' => 220000,
            ],
        ];

        foreach ($cursos as $curso) {
            Curso::create($curso);
        }
    }
}
