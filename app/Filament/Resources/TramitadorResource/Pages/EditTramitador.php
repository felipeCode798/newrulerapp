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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Cargar precios de cursos existentes
        $cursosData = [];
        foreach ($this->record->cursos as $curso) {
            $cursosData[] = [
                'curso_id' => $curso->id,
                'precio_50_transito' => $curso->pivot->precio_50_transito ?? 0,
                'precio_50_recibir' => $curso->pivot->precio_50_recibir ?? 0,
                'precio_20_transito' => $curso->pivot->precio_20_transito ?? 0,
                'precio_20_recibir' => $curso->pivot->precio_20_recibir ?? 0,
            ];
        }
        $data['cursos'] = $cursosData;

        // Cargar precios de renovaciones existentes
        $renovacionesData = [];
        foreach ($this->record->renovaciones as $renovacion) {
            $renovacionesData[] = [
                'renovacion_id' => $renovacion->id,
                'precio_renovacion' => $renovacion->pivot->precio_renovacion ?? 0,
                'precio_examen' => $renovacion->pivot->precio_examen ?? 0,
                'precio_lamina' => $renovacion->pivot->precio_lamina ?? 0,
            ];
        }
        $data['renovaciones'] = $renovacionesData;

        // Cargar precios de controversias existentes
        $controversiasData = [];
        foreach ($this->record->controversias as $controversia) {
            $controversiasData[] = [
                'categoria_controversia_id' => $controversia->id,
                'precio_tramitador' => $controversia->pivot->precio_tramitador ?? 0,
            ];
        }
        $data['controversias'] = $controversiasData;

        // Cargar precios de categorías existentes
        $categoriasData = [];
        foreach ($this->record->categorias as $categoria) {
            $categoriasData[] = [
                'categoria_id' => $categoria->id,
                'precio' => $categoria->pivot->precio ?? 0,
            ];
        }
        $data['categorias'] = $categoriasData;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Actualizar usuario asociado
        $this->record->user->update([
            'name' => $data['nombre'],
            'email' => $data['email'],
        ]);

        // Guardar los datos de relaciones en $this->data para usar en afterSave
        $this->data = $data;

        // Quitar los datos de relaciones para evitar problemas con el modelo principal
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
                if (isset($cursoData['curso_id'])) {
                    $cursosData[$cursoData['curso_id']] = [
                        'precio_50_transito' => $cursoData['precio_50_transito'] ?? 0,
                        'precio_50_recibir' => $cursoData['precio_50_recibir'] ?? 0,
                        'precio_20_transito' => $cursoData['precio_20_transito'] ?? 0,
                        'precio_20_recibir' => $cursoData['precio_20_recibir'] ?? 0,
                    ];
                }
            }
            $this->record->cursos()->sync($cursosData);
        } else {
            $this->record->cursos()->detach();
        }

        // Sincronizar precios de renovaciones
        if (isset($this->data['renovaciones'])) {
            $renovacionesData = [];
            foreach ($this->data['renovaciones'] as $renovacionData) {
                if (isset($renovacionData['renovacion_id'])) {
                    $renovacionesData[$renovacionData['renovacion_id']] = [
                        'precio_renovacion' => $renovacionData['precio_renovacion'] ?? 0,
                        'precio_examen' => $renovacionData['precio_examen'] ?? 0,
                        'precio_lamina' => $renovacionData['precio_lamina'] ?? 0,
                    ];
                }
            }
            $this->record->renovaciones()->sync($renovacionesData);
        } else {
            $this->record->renovaciones()->detach();
        }

        // Sincronizar precios de controversias
        if (isset($this->data['controversias'])) {
            $controversiasData = [];
            foreach ($this->data['controversias'] as $controversiaData) {
                if (isset($controversiaData['categoria_controversia_id'])) {
                    $controversiasData[$controversiaData['categoria_controversia_id']] = [
                        'precio_tramitador' => $controversiaData['precio_tramitador'] ?? 0,
                    ];
                }
            }
            $this->record->controversias()->sync($controversiasData);
        } else {
            $this->record->controversias()->detach();
        }

        // Sincronizar precios de categorías
        if (isset($this->data['categorias'])) {
            $categoriasData = [];
            foreach ($this->data['categorias'] as $categoriaData) {
                if (isset($categoriaData['categoria_id'])) {
                    $categoriasData[$categoriaData['categoria_id']] = [
                        'precio' => $categoriaData['precio'] ?? 0,
                    ];
                }
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