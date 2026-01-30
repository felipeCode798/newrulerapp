<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GastoResource\Pages;
use App\Models\Gasto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GastoResource extends Resource
{
    protected static ?string $model = Gasto::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Finanzas';
    protected static ?string $modelLabel = 'Gasto';
    protected static ?string $pluralModelLabel = 'Gastos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Gasto')
                    ->schema([
                        Forms\Components\TextInput::make('descripcion')
                            ->label('Descripción')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        
                        Forms\Components\TextInput::make('valor')
                            ->label('Valor')
                            ->numeric()
                            ->required()
                            ->prefix('$')
                            ->default(0),
                        
                        Forms\Components\Select::make('tipo_pago')
                            ->label('Tipo de Pago')
                            ->options([
                                'cia' => 'CIA',
                                'abogado' => 'Abogado',
                                'general' => 'Gasto General',
                                'otro' => 'Otro',
                            ])
                            ->required()
                            ->reactive(),
                        
                        Forms\Components\Select::make('cia_id')
                            ->label('CIA')
                            ->relationship('cia', 'nombre')
                            ->searchable()
                            ->preload()
                            ->visible(fn ($get) => $get('tipo_pago') === 'cia'),
                        
                        Forms\Components\Select::make('abogado_id')
                            ->label('Abogado')
                            ->relationship('abogado', 'nombre')
                            ->searchable()
                            ->preload()
                            ->visible(fn ($get) => $get('tipo_pago') === 'abogado'),
                        
                        Forms\Components\Select::make('proceso_id')
                            ->label('Proceso Relacionado')
                            ->relationship('proceso', 'descripcion_servicio')
                            ->searchable()
                            ->preload()
                            ->columnSpan(2)
                            ->nullable(),
                        
                        Forms\Components\DatePicker::make('fecha_gasto')
                            ->label('Fecha del Gasto')
                            ->required()
                            ->default(now())
                            ->native(false)
                            ->columnSpan(2)
                            ->displayFormat('d/m/Y'),
                        
                        Forms\Components\Select::make('estado')
                            ->label('Estado')
                            ->options([
                                'pendiente' => 'Pendiente',
                                'pagado' => 'Pagado',
                            ])
                            ->default('pendiente')
                            ->required(),
                        
                        Forms\Components\FileUpload::make('comprobante')
                            ->label('Comprobante')
                            ->directory('gastos/comprobantes')
                            ->acceptedFileTypes(['image/*', 'application/pdf'])
                            ->columnSpan(3)
                            ->maxSize(5120),
                        
                        Forms\Components\Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->rows(3)
                            ->columnSpan(3),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('valor')
                    ->label('Valor')
                    ->money('COP')
                    ->sortable()
                    ->color('danger'),
                
                Tables\Columns\TextColumn::make('tipo_pago')
                    ->label('Tipo')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'cia' => 'CIA',
                        'abogado' => 'Abogado',
                        'general' => 'General',
                        'otro' => 'Otro',
                        default => ucfirst($state),
                    })
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'cia' => 'info',
                        'abogado' => 'warning',
                        'general' => 'gray',
                        'otro' => 'secondary',
                        default => 'primary',
                    }),
                
                Tables\Columns\TextColumn::make('cia.nombre')
                    ->label('CIA')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('abogado.nombre')
                    ->label('Abogado')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('proceso.descripcion_servicio')
                    ->label('Proceso')
                    ->searchable()
                    ->limit(20)
                    ->tooltip(fn ($state) => $state),
                
                Tables\Columns\TextColumn::make('fecha_gasto')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('estado')
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->getStateUsing(fn ($record) => $record->estado === 'pagado'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo_pago')
                    ->label('Tipo de Pago')
                    ->options([
                        'cia' => 'CIA',
                        'abogado' => 'Abogado',
                        'general' => 'General',
                        'otro' => 'Otro',
                    ]),
                
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'pagado' => 'Pagado',
                    ]),
                
                Tables\Filters\Filter::make('fecha_gasto')
                    ->label('Fecha del Gasto')
                    ->form([
                        Forms\Components\DatePicker::make('desde')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('hasta')
                            ->label('Hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['desde'], fn ($q, $date) => $q->whereDate('fecha_gasto', '>=', $date))
                            ->when($data['hasta'], fn ($q, $date) => $q->whereDate('fecha_gasto', '<=', $date));
                    }),
                
                Tables\Filters\Filter::make('valor')
                    ->label('Valor')
                    ->form([
                        Forms\Components\TextInput::make('min')
                            ->label('Mínimo')
                            ->numeric(),
                        Forms\Components\TextInput::make('max')
                            ->label('Máximo')
                            ->numeric(),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['min'], fn ($q, $amount) => $q->where('valor', '>=', $amount))
                            ->when($data['max'], fn ($q, $amount) => $q->where('valor', '<=', $amount));
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('marcar_pagado')
                    ->label('Marcar Pagado')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['estado' => 'pagado']))
                    ->visible(fn ($record) => $record->estado === 'pendiente'),
                
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('marcar_pagados')
                        ->label('Marcar como Pagados')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['estado' => 'pagado'])),
                    Tables\Actions\BulkAction::make('marcar_pendientes')
                        ->label('Marcar como Pendientes')
                        ->icon('heroicon-o-clock')
                        ->color('warning')
                        ->action(fn ($records) => $records->each->update(['estado' => 'pendiente'])),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGastos::route('/'),
            'create' => Pages\CreateGasto::route('/create'),
            'edit' => Pages\EditGasto::route('/{record}/edit'),
        ];
    }
}