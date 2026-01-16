<?php

namespace App\Filament\Resources\ProcesoResource\Pages;

use App\Filament\Resources\ProcesoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewProceso extends ViewRecord
{
    protected static string $resource = ProcesoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Información General')
                    ->schema([
                        Infolists\Components\TextEntry::make('tipo_usuario')
                            ->label('Tipo de Usuario')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'cliente' => 'Cliente',
                                'tramitador' => 'Tramitador',
                            })
                            ->color(fn (string $state): string => match ($state) {
                                'cliente' => 'success',
                                'tramitador' => 'info',
                            }),

                        Infolists\Components\TextEntry::make('cliente.nombre')
                            ->label('Cliente')
                            ->visible(fn ($record) => $record->tipo_usuario === 'cliente'),

                        Infolists\Components\TextEntry::make('cliente.cedula')
                            ->label('Cédula Cliente')
                            ->visible(fn ($record) => $record->tipo_usuario === 'cliente'),

                        Infolists\Components\TextEntry::make('tramitador.nombre')
                            ->label('Tramitador')
                            ->visible(fn ($record) => $record->tipo_usuario === 'tramitador'),

                        Infolists\Components\TextEntry::make('total_general')
                            ->label('Total General')
                            ->money('COP')
                            ->size('lg')
                            ->weight('bold')
                            ->color('success'),

                        Infolists\Components\TextEntry::make('createdBy.name')
                            ->label('Creado Por'),

                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Fecha de Creación')
                            ->dateTime(),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Cursos')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('cursos')
                            ->schema([
                                Infolists\Components\TextEntry::make('curso.categoria')
                                    ->label('Curso'),
                                Infolists\Components\TextEntry::make('cedula')
                                    ->label('Cédula'),
                                Infolists\Components\TextEntry::make('porcentaje')
                                    ->label('Porcentaje')
                                    ->suffix('%'),
                                Infolists\Components\TextEntry::make('valor_transito')
                                    ->label('Valor Tránsito')
                                    ->money('COP'),
                                Infolists\Components\TextEntry::make('valor_recibir')
                                    ->label('Valor a Recibir')
                                    ->money('COP'),
                            ])
                            ->columns(5)
                    ])
                    ->visible(fn ($record) => $record->cursos()->count() > 0)
                    ->collapsible(),

                Infolists\Components\Section::make('Renovaciones')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('renovaciones')
                            ->schema([
                                Infolists\Components\TextEntry::make('cedula')
                                    ->label('Cédula'),
                                Infolists\Components\TextEntry::make('renovaciones_seleccionadas')
                                    ->label('Renovaciones')
                                    ->formatStateUsing(function ($state) {
                                        if (is_array($state)) {
                                            $renovaciones = \App\Models\Renovacion::whereIn('id', $state)->pluck('nombre')->toArray();
                                            return implode(', ', $renovaciones);
                                        }
                                        return '-';
                                    })
                                    ->columnSpan(2),
                                Infolists\Components\TextEntry::make('valor_total')
                                    ->label('Valor Total')
                                    ->money('COP'),
                            ])
                            ->columns(4)
                    ])
                    ->visible(fn ($record) => $record->renovaciones()->count() > 0)
                    ->collapsible(),

                Infolists\Components\Section::make('Licencias')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('licencias')
                            ->schema([
                                Infolists\Components\TextEntry::make('cedula')
                                    ->label('Cédula'),
                                Infolists\Components\TextEntry::make('categorias_seleccionadas')
                                    ->label('Categorías')
                                    ->formatStateUsing(function ($state) {
                                        if (is_array($state)) {
                                            $categorias = \App\Models\CategoriaLicencia::whereIn('id', $state)->pluck('nombre')->toArray();
                                            return implode(', ', $categorias);
                                        }
                                        return '-';
                                    }),
                                Infolists\Components\TextEntry::make('escuela.nombre')
                                    ->label('Escuela'),
                                Infolists\Components\TextEntry::make('enrolamiento')
                                    ->label('Enrolamiento')
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'cruce_pin' => 'Cruce de PIN',
                                        'guardado' => 'Guardado',
                                        'pagado' => 'Pagado',
                                    }),
                                Infolists\Components\TextEntry::make('pinEscuela.pin')
                                    ->label('PIN')
                                    ->visible(fn ($record) => $record->enrolamiento === 'cruce_pin'),
                                Infolists\Components\TextEntry::make('valor_carta_escuela')
                                    ->label('Carta Escuela')
                                    ->money('COP'),
                                Infolists\Components\TextEntry::make('examen_medico')
                                    ->label('Examen Médico')
                                    ->badge(),
                                Infolists\Components\TextEntry::make('valor_examen_medico')
                                    ->label('Valor Examen')
                                    ->money('COP'),
                                Infolists\Components\TextEntry::make('impresion')
                                    ->label('Impresión')
                                    ->badge(),
                                Infolists\Components\TextEntry::make('valor_impresion')
                                    ->label('Valor Impresión')
                                    ->money('COP'),
                                Infolists\Components\TextEntry::make('sin_curso')
                                    ->label('Sin Curso')
                                    ->badge(),
                                Infolists\Components\TextEntry::make('valor_sin_curso')
                                    ->label('Valor Sin Curso')
                                    ->money('COP'),
                                Infolists\Components\TextEntry::make('valor_total_licencia')
                                    ->label('Total Licencia')
                                    ->money('COP')
                                    ->weight('bold')
                                    ->color('success')
                                    ->columnSpan(2),
                            ])
                            ->columns(4)
                    ])
                    ->visible(fn ($record) => $record->licencias()->count() > 0)
                    ->collapsible(),

                Infolists\Components\Section::make('Traspasos')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('traspasos')
                            ->schema([
                                Infolists\Components\TextEntry::make('cedula')
                                    ->label('Cédula'),
                                Infolists\Components\TextEntry::make('nombre_propietario')
                                    ->label('Propietario'),
                                Infolists\Components\TextEntry::make('nombre_comprador')
                                    ->label('Comprador'),
                                Infolists\Components\TextEntry::make('cedula_comprador')
                                    ->label('Cédula Comprador'),
                                Infolists\Components\TextEntry::make('derecho_traspaso')
                                    ->label('Derecho Traspaso')
                                    ->money('COP'),
                                Infolists\Components\TextEntry::make('porcentaje')
                                    ->label('Porcentaje')
                                    ->money('COP'),
                                Infolists\Components\TextEntry::make('honorarios')
                                    ->label('Honorarios')
                                    ->money('COP'),
                                Infolists\Components\TextEntry::make('comision')
                                    ->label('Comisión')
                                    ->money('COP'),
                                Infolists\Components\TextEntry::make('total_recibir')
                                    ->label('Total a Recibir')
                                    ->money('COP')
                                    ->weight('bold')
                                    ->color('success'),
                            ])
                            ->columns(3)
                    ])
                    ->visible(fn ($record) => $record->traspasos()->count() > 0)
                    ->collapsible(),

                Infolists\Components\Section::make('RUNT')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('runts')
                            ->schema([
                                Infolists\Components\TextEntry::make('nombre')
                                    ->label('Nombre'),
                                Infolists\Components\TextEntry::make('cedula')
                                    ->label('Cédula'),
                                Infolists\Components\TextEntry::make('numero')
                                    ->label('Número'),
                                Infolists\Components\TextEntry::make('comision')
                                    ->label('Comisión')
                                    ->money('COP'),
                                Infolists\Components\TextEntry::make('pago')
                                    ->label('Pago')
                                    ->money('COP'),
                                Infolists\Components\TextEntry::make('honorarios')
                                    ->label('Honorarios')
                                    ->money('COP'),
                                Infolists\Components\TextEntry::make('valor_recibir')
                                    ->label('Total a Recibir')
                                    ->money('COP')
                                    ->weight('bold')
                                    ->color('success'),
                            ])
                            ->columns(3)
                    ])
                    ->visible(fn ($record) => $record->runts()->count() > 0)
                    ->collapsible(),

                Infolists\Components\Section::make('Controversias')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('controversias')
                            ->schema([
                                Infolists\Components\TextEntry::make('cedula')
                                    ->label('Cédula'),
                                Infolists\Components\TextEntry::make('categoriaControversia.nombre')
                                    ->label('Categoría'),
                                Infolists\Components\TextEntry::make('valor_controversia')
                                    ->label('Valor')
                                    ->money('COP'),
                                Infolists\Components\TextEntry::make('fecha_hora_cita')
                                    ->label('Fecha y Hora Cita')
                                    ->dateTime(),
                                Infolists\Components\TextEntry::make('codigo_controversia')
                                    ->label('Código'),
                                Infolists\Components\TextEntry::make('venta_controversia')
                                    ->label('Venta')
                                    ->money('COP'),
                                Infolists\Components\ImageEntry::make('documento_identidad')
                                    ->label('Doc. Identidad')
                                    ->visible(fn ($state) => $state !== null),
                                Infolists\Components\ImageEntry::make('poder')
                                    ->label('Poder')
                                    ->visible(fn ($state) => $state !== null),
                            ])
                            ->columns(4)
                    ])
                    ->visible(fn ($record) => $record->controversias()->count() > 0)
                    ->collapsible(),

                Infolists\Components\Section::make('Estados de Cuenta')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('estadoCuentas')
                            ->schema([
                                Infolists\Components\ImageEntry::make('archivo')
                                    ->label('Archivo'),
                            ])
                            ->columns(3)
                    ])
                    ->visible(fn ($record) => $record->estadoCuentas()->count() > 0)
                    ->collapsible(),
            ]);
    }
}
