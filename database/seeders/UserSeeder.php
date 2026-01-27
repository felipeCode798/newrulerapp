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
            'email' => 'felipe@gmail.com',
            'password' => Hash::make('123456'),
        ]);
        $admin->assignRole('super_admin');

        // Usuario Operador
        $operador = User::create([
            'name' => 'Operador Sistema',
            'email' => 'operador@sistema.com',
            'password' => Hash::make('123456'),
        ]);
        $operador->assignRole('operador');
    }
}
