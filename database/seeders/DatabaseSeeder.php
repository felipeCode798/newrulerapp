<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            CursoSeeder::class,
            RenovacionSeeder::class,
            CategoriaLicenciaSeeder::class,
            CategoriaControversiaSeeder::class,
            EscuelaSeeder::class,
            CategoriaSeeder::class,
            CiaSeeder::class,
            AbogadoSeeder::class,
        ]);
    }
}
