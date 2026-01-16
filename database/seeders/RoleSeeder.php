<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Crear roles
        $adminRole = Role::create(['name' => 'super_admin']);
        $clienteRole = Role::create(['name' => 'cliente']);
        $tramitadorRole = Role::create(['name' => 'tramitador']);
        $operadorRole = Role::create(['name' => 'operador']);

        // Crear permisos básicos
        $permissions = [
            // Clientes
            'view_cliente',
            'create_cliente',
            'edit_cliente',
            'delete_cliente',

            // Tramitadores
            'view_tramitador',
            'create_tramitador',
            'edit_tramitador',
            'delete_tramitador',

            // Cursos
            'view_curso',
            'create_curso',
            'edit_curso',
            'delete_curso',

            // Renovaciones
            'view_renovacion',
            'create_renovacion',
            'edit_renovacion',
            'delete_renovacion',

            // Escuelas
            'view_escuela',
            'create_escuela',
            'edit_escuela',
            'delete_escuela',

            // Categoría Licencias
            'view_categoria_licencia',
            'create_categoria_licencia',
            'edit_categoria_licencia',
            'delete_categoria_licencia',

            // Categoría Controversias
            'view_categoria_controversia',
            'create_categoria_controversia',
            'edit_categoria_controversia',
            'delete_categoria_controversia',

            // Procesos
            'view_proceso',
            'create_proceso',
            'edit_proceso',
            'delete_proceso',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Asignar todos los permisos al super_admin
        $adminRole->givePermissionTo(Permission::all());

        // Asignar permisos al operador
        $operadorRole->givePermissionTo([
            'view_cliente',
            'create_cliente',
            'view_tramitador',
            'view_curso',
            'view_renovacion',
            'view_escuela',
            'view_categoria_licencia',
            'view_categoria_controversia',
            'view_proceso',
            'create_proceso',
            'edit_proceso',
        ]);
    }
}
