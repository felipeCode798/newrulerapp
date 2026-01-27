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

        // Quitar los datos de relaciones para evitar problemas
        unset($data['cursos']);
        unset($data['renovaciones']);
        unset($data['controversias']);
        unset($data['categorias']);

        return $data;
    }

    protected function afterCreate(): void
    {
        // Guardar precios de cursos
        if (isset($this->data['cursos'])) {
            foreach ($this->data['cursos'] as $cursoData) {
                $this->record->cursos()->attach($cursoData['curso_id'], [
                    'precio_50_transito' => $cursoData['precio_50_transito'] ?? 0,
                    'precio_50_recibir' => $cursoData['precio_50_recibir'] ?? 0,
                    'precio_20_transito' => $cursoData['precio_20_transito'] ?? 0,
                    'precio_20_recibir' => $cursoData['precio_20_recibir'] ?? 0,
                ]);
            }
        }

        // Guardar precios de renovaciones
        if (isset($this->data['renovaciones'])) {
            foreach ($this->data['renovaciones'] as $renovacionData) {
                $this->record->renovaciones()->attach($renovacionData['renovacion_id'], [
                    'precio_renovacion' => $renovacionData['precio_renovacion'] ?? 0,
                    'precio_examen' => $renovacionData['precio_examen'] ?? 0,
                    'precio_lamina' => $renovacionData['precio_lamina'] ?? 0,
                ]);
            }
        }

        // Guardar precios de controversias
        if (isset($this->data['controversias'])) {
            foreach ($this->data['controversias'] as $controversiaData) {
                $this->record->controversias()->attach($controversiaData['categoria_controversia_id'], [
                    'precio_tramitador' => $controversiaData['precio_tramitador'] ?? 0,
                ]);
            }
        }

        // Guardar precios de categorías
        if (isset($this->data['categorias'])) {
            foreach ($this->data['categorias'] as $categoriaData) {
                $this->record->categorias()->attach($categoriaData['categoria_id'], [
                    'precio' => $categoriaData['precio'] ?? 0,
                ]);
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}