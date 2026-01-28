<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CuadreResource\Pages;
use App\Models\Cuadre;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class CuadreResource extends Resource
{
    protected static ?string $model = Cuadre::class;
    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationGroup = 'Finanzas';
    protected static ?string $modelLabel = 'Cuadre';
    protected static ?string $pluralModelLabel = 'Cuadres';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Periodo del Cuadre')
                    ->schema([
                        Forms\Components\DatePicker::make('fecha_inicio')
                            ->label('Fecha Inicio')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                            
                        Forms\Components\DatePicker::make('fecha_fin')
                            ->label('Fecha Fin')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->after('fecha_inicio'),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Resultados del Cuadre')
                    ->schema([
                        Forms\Components\TextInput::make('total_pagos')
                            ->label('Total Pagos')
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated()
                            ->default(0),
                            
                        Forms\Components\TextInput::make('total_gastos')
                            ->label('Total Gastos')
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated()
                            ->default(0),
                            
                        Forms\Components\TextInput::make('diferencia')
                            ->label('Diferencia (Pagos - Gastos)')
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated()
                            ->default(0),
                    ])
                    ->columns(3),
                    
                Forms\Components\Textarea::make('observaciones')
                    ->label('Observaciones')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fecha_inicio')
                    ->label('Desde')
                    ->date('d/m/Y')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('fecha_fin')
                    ->label('Hasta')
                    ->date('d/m/Y')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('total_pagos')
                    ->label('Total Pagos')
                    ->money('COP')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('total_gastos')
                    ->label('Total Gastos')
                    ->money('COP')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('diferencia')
                    ->label('Diferencia')
                    ->money('COP')
                    ->color(fn ($record) => $record->diferencia >= 0 ? 'success' : 'danger')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Generado por')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de Cuadre')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('fecha')
                    ->form([
                        Forms\Components\DatePicker::make('fecha_desde')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('fecha_hasta')
                            ->label('Hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['fecha_desde'], fn ($q, $date) => $q->whereDate('fecha_inicio', '>=', $date))
                            ->when($data['fecha_hasta'], fn ($q, $date) => $q->whereDate('fecha_fin', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('calcular')
                    ->label('Calcular')
                    ->icon('heroicon-o-calculator')
                    ->color('success')
                    ->action(function (Cuadre $record) {
                        $totalPagos = \App\Models\Pago::whereBetween('fecha_pago', [$record->fecha_inicio, $record->fecha_fin])
                            ->sum('valor');
                            
                        $totalGastos = \App\Models\Gasto::whereBetween('fecha_gasto', [$record->fecha_inicio, $record->fecha_fin])
                            ->sum('valor');
                            
                        $record->update([
                            'total_pagos' => $totalPagos,
                            'total_gastos' => $totalGastos,
                            'diferencia' => $totalPagos - $totalGastos,
                        ]);
                        
                        Notification::make()
                            ->title('Cuadre Calculado')
                            ->body("Total Pagos: $" . number_format($totalPagos, 2) . "\nTotal Gastos: $" . number_format($totalGastos, 2) . "\nDiferencia: $" . number_format($totalPagos - $totalGastos, 2))
                            ->success()
                            ->send();
                    }),
                    
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCuadres::route('/'),
            'create' => Pages\CreateCuadre::route('/create'),
            'edit' => Pages\EditCuadre::route('/{record}/edit'),
        ];
    }

    protected static function handleRecordCreation(array $data): Cuadre
    {
        $data['user_id'] = Auth::id();
        return static::getModel()::create($data);
    }
}