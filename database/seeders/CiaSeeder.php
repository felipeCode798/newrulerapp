<?php

namespace Database\Seeders;

use App\Models\Cia;
use App\Models\CiaPrecio;
use App\Models\CategoriaControversia;
use Illuminate\Database\Seeder;

class CiaSeeder extends Seeder
{
    public function run(): void
    {
        // Primero, crear algunas CIAs
        $cias = [
            [
                'nombre' => 'CIA Norte de Bogotá',
                'codigo' => 'CIA-NBOG',
                'direccion' => 'Calle 100 # 15-20, Bogotá D.C.',
                'telefono' => '6011234567',
                'email' => 'norte@ciabogota.com',
                'contacto' => 'Carlos Martínez',
                'celular_contacto' => '3101234567',
                'observaciones' => 'CIA especializada en tránsito',
                'activo' => true,
            ],
            [
                'nombre' => 'CIA Centro de Medellín',
                'codigo' => 'CIA-CMED',
                'direccion' => 'Carrera 50 # 10-30, Medellín',
                'telefono' => '6042345678',
                'email' => 'centro@ciamedellin.com',
                'contacto' => 'Ana Gómez',
                'celular_contacto' => '3102345678',
                'observaciones' => 'Atención rápida y eficiente',
                'activo' => true,
            ],
            [
                'nombre' => 'CIA Sur de Cali',
                'codigo' => 'CIA-SCAL',
                'direccion' => 'Avenida 6N # 25-40, Cali',
                'telefono' => '6023456789',
                'email' => 'sur@ciacali.com',
                'contacto' => 'Roberto Sánchez',
                'celular_contacto' => '3103456789',
                'observaciones' => 'Convenio con escuelas de conducción',
                'activo' => true,
            ],
            [
                'nombre' => 'CIA Oriental de Barranquilla',
                'codigo' => 'CIA-OBAR',
                'direccion' => 'Calle 72 # 45-10, Barranquilla',
                'telefono' => '6054567890',
                'email' => 'oriental@ciabarranquilla.com',
                'contacto' => 'María Rodríguez',
                'celular_contacto' => '3104567890',
                'observaciones' => 'Horario extendido los sábados',
                'activo' => true,
            ],
            [
                'nombre' => 'CIA Occidental de Cartagena',
                'codigo' => 'CIA-OCAR',
                'direccion' => 'Avenida Pedro de Heredia # 30-15, Cartagena',
                'telefono' => '6055678901',
                'email' => 'occidental@ciacartagena.com',
                'contacto' => 'Javier Pérez',
                'celular_contacto' => '3105678901',
                'observaciones' => 'Servicio VIP disponible',
                'activo' => true,
            ],
        ];

        foreach ($cias as $ciaData) {
            $cia = Cia::create($ciaData);
            
            // Asignar precios por categoría de controversia si existen
            $this->crearPreciosCia($cia);
        }

        $this->command->info('✅ 5 CIAs creadas exitosamente');
    }

    private function crearPreciosCia(Cia $cia): void
    {
        // Solo crear precios si hay categorías de controversia
        if (CategoriaControversia::exists()) {
            $categorias = CategoriaControversia::all();
            
            foreach ($categorias as $categoria) {
                // Precio base aleatorio entre 50,000 y 200,000
                $precioBase = rand(50000, 200000);
                
                // Ajustar precio según la CIA
                $multiplicador = match($cia->codigo) {
                    'CIA-NBOG' => 1.1,  // Bogotá más caro
                    'CIA-CMED' => 1.0,
                    'CIA-SCAL' => 0.9,  // Cali más económico
                    'CIA-OBAR' => 0.95,
                    'CIA-OCAR' => 1.05,
                    default => 1.0,
                };
                
                $precioFinal = round($precioBase * $multiplicador, -3); // Redondear a miles
                
                CiaPrecio::create([
                    'cia_id' => $cia->id,
                    'categoria_controversia_id' => $categoria->id,
                    'precio' => $precioFinal,
                    'observaciones' => 'Precio estándar ' . date('Y'),
                ]);
            }
            
            $this->command->info("   💰 Precios creados para CIA: {$cia->nombre}");
        }
    }
}