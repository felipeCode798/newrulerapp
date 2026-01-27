<?php

namespace App\Filament\Resources\TramitadorResource\Pages;

use App\Filament\Resources\TramitadorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTramitador extends EditRecord
{
    protected static string $resource = TramitadorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Actualizar usuario asociado
        $this->record->user->update([
            'name' => $data['nombre'],
            'email' => $data['email'],
        ]);

        // Quitar los datos de relaciones para evitar problemas
        unset($data['cursos']);
        unset($data['renovaciones']);
        unset($data['controversias']);
        unset($data['categorias']);

        return $data;
    }

    protected function afterSave(): void
    {
        // Sincronizar precios de cursos
        if (isset($this->data['cursos'])) {
            $cursosData = [];
            foreach ($this->data['cursos'] as $cursoData) {
                $cursosData[$cursoData['curso_id']] = [
                    'precio_50_transito' => $cursoData['precio_50_transito'] ?? 0,
                    'precio_50_recibir' => $cursoData['precio_50_recibir'] ?? 0,
                    'precio_20_transito' => $cursoData['precio_20_transito'] ?? 0,
                    'precio_20_recibir' => $cursoData['precio_20_recibir'] ?? 0,
                ];
            }
            $this->record->cursos()->sync($cursosData);
        } else {
            $this->record->cursos()->detach();
        }

        // Sincronizar precios de renovaciones
        if (isset($this->data['renovaciones'])) {
            $renovacionesData = [];
            foreach ($this->data['renovaciones'] as $renovacionData) {
                $renovacionesData[$renovacionData['renovacion_id']] = [
                    'precio_renovacion' => $renovacionData['precio_renovacion'] ?? 0,
                    'precio_examen' => $renovacionData['precio_examen'] ?? 0,
                    'precio_lamina' => $renovacionData['precio_lamina'] ?? 0,
                ];
            }
            $this->record->renovaciones()->sync($renovacionesData);
        } else {
            $this->record->renovaciones()->detach();
        }

        // Sincronizar precios de controversias
        if (isset($this->data['controversias'])) {
            $controversiasData = [];
            foreach ($this->data['controversias'] as $controversiaData) {
                $controversiasData[$controversiaData['categoria_controversia_id']] = [
                    'precio_tramitador' => $controversiaData['precio_tramitador'] ?? 0,
                ];
            }
            $this->record->controversias()->sync($controversiasData);
        } else {
            $this->record->controversias()->detach();
        }

        // Sincronizar precios de categorías
        if (isset($this->data['categorias'])) {
            $categoriasData = [];
            foreach ($this->data['categorias'] as $categoriaData) {
                $categoriasData[$categoriaData['categoria_id']] = [
                    'precio' => $categoriaData['precio'] ?? 0,
                ];
            }
            $this->record->categorias()->sync($categoriasData);
        } else {
            $this->record->categorias()->detach();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}