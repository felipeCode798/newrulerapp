<?php

namespace App\Filament\Resources\ProcesoResource\Pages;

use App\Filament\Resources\ProcesoResource;
use App\Models\EstadoCuenta;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateProceso extends CreateRecord
{
    protected static string $resource = ProcesoResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::id();

        // Calcular total general
        $totalGeneral = 0;

        // No necesitamos calcular aquí porque los repeaters manejan sus propias relaciones

        return $data;
    }

    protected function afterCreate(): void
    {
        // Guardar estados de cuenta
        if (isset($this->data['estados_cuenta']) && is_array($this->data['estados_cuenta'])) {
            foreach ($this->data['estados_cuenta'] as $archivo) {
                EstadoCuenta::create([
                    'proceso_id' => $this->record->id,
                    'archivo' => $archivo,
                ]);
            }
        }

        // Calcular y actualizar el total general
        $this->calcularTotalGeneral();
    }

    protected function calcularTotalGeneral(): void
    {
        $total = 0;

        // Sumar cursos
        $total += $this->record->cursos()->sum('valor_recibir');

        // Sumar renovaciones
        $total += $this->record->renovaciones()->sum('valor_total');

        // Sumar licencias
        $total += $this->record->licencias()->sum('valor_total_licencia');

        // Sumar traspasos
        $total += $this->record->traspasos()->sum('total_recibir');

        // Sumar runts
        $total += $this->record->runts()->sum('valor_recibir');

        // Sumar controversias
        $total += $this->record->controversias()->sum('valor_controversia');

        // Actualizar el total general
        $this->record->update(['total_general' => $total]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
