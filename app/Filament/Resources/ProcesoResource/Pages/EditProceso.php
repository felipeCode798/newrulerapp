<?php

namespace App\Filament\Resources\ProcesoResource\Pages;

use App\Filament\Resources\ProcesoResource;
use App\Models\EstadoCuenta;
use Filament\Actions;
use Filament\Notifications\Notification;
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
        $this->record->calcularTotalGeneral();

        // Mostrar notificación
        Notification::make()
            ->title('Proceso actualizado')
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        // Redirigir a la tabla de procesos
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return null; // Ya mostramos nuestra propia notificación
    }
}