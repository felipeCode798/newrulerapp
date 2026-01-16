<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Usuario Super Admin
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('super_admin');

        // Usuario Operador
        $operador = User::create([
            'name' => 'Operador Sistema',
            'email' => 'operador@sistema.com',
            'password' => Hash::make('password'),
        ]);
        $operador->assignRole('operador');
    }
}
