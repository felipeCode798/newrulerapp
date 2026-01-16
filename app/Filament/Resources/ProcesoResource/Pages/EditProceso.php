<?php

namespace App\Filament\Resources\ProcesoResource\Pages;

use App\Filament\Resources\ProcesoResource;
use App\Models\EstadoCuenta;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProceso extends EditRecord
{
    protected static string $resource = ProcesoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Cargar estados de cuenta existentes
        $estadosCuenta = $this->record->estadoCuentas()->pluck('archivo')->toArray();
        $data['estados_cuenta'] = $estadosCuenta;

        return $data;
    }

    protected function afterSave(): void
    {
        // Actualizar estados de cuenta
        if (isset($this->data['estados_cuenta'])) {
            // Eliminar estados de cuenta anteriores
            $this->record->estadoCuentas()->delete();

            // Guardar nuevos estados de cuenta
            if (is_array($this->data['estados_cuenta'])) {
                foreach ($this->data['estados_cuenta'] as $archivo) {
                    EstadoCuenta::create([
                        'proceso_id' => $this->record->id,
                        'archivo' => $archivo,
                    ]);
                }
            }
        }

        // Recalcular total general
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
