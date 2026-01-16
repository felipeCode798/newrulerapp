<?php

namespace App\Filament\Resources\TramitadorResource\Pages;

use App\Filament\Resources\TramitadorResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateTramitador extends CreateRecord
{
    protected static string $resource = TramitadorResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Crear usuario
        $user = User::create([
            'name' => $data['nombre'],
            'email' => $data['email'],
            'password' => Hash::make($data['cedula']),
        ]);

        // Asignar rol tramitador
        $user->assignRole('tramitador');

        $data['user_id'] = $user->id;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
