<?php

namespace App\Filament\Resources\ProcesoResource\Pages;

use App\Filament\Resources\ProcesoResource;
use App\Exports\ProcesosExportManual;
use App\Exports\ProcesosExportPDFManual;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms;
use Illuminate\Support\Facades\Date;

class ListProcesos extends ListRecords
{
    protected static string $resource = ProcesoResource::class;

    // Asegúrate de que estos métodos estén presentes
    protected function getHeaderWidgets(): array
    {
        return [
            ProcesoResource\Widgets\ProcesoStatsOverview::class,
        ];
    }

    // CAMBIA ESTE MÉTODO DE protected A public
    public function getHeaderWidgetsColumns(): int|array
    {
        return 4; // 4 widgets por fila
    }

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
                            'xlsx' => 'Excel (.xlsx)',
                            'csv' => 'CSV (.csv)',
                            'pdf' => 'PDF (.pdf)',
                        ])
                        ->default('xlsx')
                        ->required(),
                    
                    Forms\Components\TextInput::make('nombre_archivo')
                        ->label('Nombre del archivo')
                        ->default('procesos_' . Date::now()->format('Y-m-d_H-i-s'))
                        ->required(),
                    
                    Forms\Components\Toggle::make('incluir_detalle')
                        ->label('Incluir detalle en PDF')
                        ->default(true)
                        ->helperText('Incluye información detallada de cada proceso')
                        ->visible(fn (Forms\Get $get) => $get('formato') === 'pdf'),
                ])
                ->action(function (array $data) {
                    $query = $this->getFilteredTableQuery();
                    $procesos = $query->get();
                    
                    if ($procesos->isEmpty()) {
                        throw new \Exception('No hay datos para exportar con los filtros aplicados.');
                    }
                    
                    if ($data['formato'] === 'pdf') {
                        $export = new ProcesosExportPDFManual($procesos, 'Reporte de Procesos');
                        return $export->download($data['nombre_archivo']);
                    } else {
                        $export = new ProcesosExportManual($procesos);
                        return $export->download($data['nombre_archivo'], $data['formato']);
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