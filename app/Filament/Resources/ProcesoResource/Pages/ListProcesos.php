<?php

namespace App\Filament\Resources\ProcesoResource\Pages;

use App\Filament\Resources\ProcesoResource;
use App\Exports\ProcesosExport;
use App\Exports\ProcesoFacturaManual; 
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms;
use Illuminate\Support\Facades\Date;

class ListProcesos extends ListRecords
{
    protected static string $resource = ProcesoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('exportar_filtros')
                ->label('Exportar Filtros')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->modalHeading('Exportar Procesos Filtrados')
                ->modalSubmitActionLabel('Exportar')
                ->form([
                    Forms\Components\Select::make('formato')
                        ->label('Formato de exportación')
                        ->options([
                            'csv' => 'Excel/CSV (.csv)',
                            'pdf' => 'PDF/HTML (.html)',
                        ])
                        ->default('csv')
                        ->required(),
                    
                    Forms\Components\TextInput::make('nombre_archivo')
                        ->label('Nombre del archivo')
                        ->default('procesos_' . Date::now()->format('Y-m-d_H-i-s'))
                        ->required(),
                ])
                ->action(function (array $data) {
                    $query = $this->getFilteredTableQuery();
                    $procesos = $query->get();
                    
                    if ($procesos->isEmpty()) {
                        throw new \Exception('No hay datos para exportar con los filtros aplicados.');
                    }
                    
                    if ($data['formato'] === 'csv') {
                        return (new \App\Exports\ProcesosExport($procesos))->download($data['nombre_archivo']);
                    } else {
                        // Para HTML/PDF con estilo factura
                        $pdf = new ProcesoFacturaManual($procesos);
                        return $pdf->download($data['nombre_archivo']);
                    }
                })
                ->modalWidth('md')
                ->modalDescription('Exporta los procesos actualmente filtrados'),
        ];
    }

    protected function getTableFiltersFormWidth(): string
    {
        return '4xl';
    }

    protected function getTableFiltersFormColumns(): int
    {
        return 3;
    }
}