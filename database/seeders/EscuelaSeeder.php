<?php

namespace Database\Seeders;

use App\Models\Escuela;
use App\Models\PinEscuela;
use Illuminate\Database\Seeder;

class EscuelaSeeder extends Seeder
{
    public function run(): void
    {
        $escuelas = [
            [
                'nombre' => 'Escuela de Conducción Vial',
                'numero_pines' => 10,
                'direccion' => 'Calle 50 #25-30',
                'telefono' => '3001234567',
                'valor_carta_escuela' => 200000,
            ],
            [
                'nombre' => 'Autoescuela del Norte',
                'numero_pines' => 15,
                'direccion' => 'Carrera 10 #80-45',
                'telefono' => '3109876543',
                'valor_carta_escuela' => 180000,
            ],
            [
                'nombre' => 'Centro de Enseñanza Automotriz',
                'numero_pines' => 12,
                'direccion' => 'Avenida 5 #30-20',
                'telefono' => '3205551234',
                'valor_carta_escuela' => 220000,
            ],
        ];

        foreach ($escuelas as $escuelaData) {
            $escuela = Escuela::create($escuelaData);

            // Crear PINs para cada escuela
            for ($i = 1; $i <= $escuela->numero_pines; $i++) {
                PinEscuela::create([
                    'escuela_id' => $escuela->id,
                    'pin' => $escuela->id . '-PIN-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'estado' => 'disponible',
                ]);
            }
        }
    }
}
