<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PagoResource\Pages;
use App\Models\Pago;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PagoResource extends Resource
{
    protected static ?string $model = Pago::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Finanzas';
    protected static ?string $modelLabel = 'Pago';
    protected static ?string $pluralModelLabel = 'Pagos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Pago')
                    ->schema([
                        Forms\Components\Select::make('proceso_id')
                            ->label('Proceso')
                            ->relationship('proceso', 'descripcion_servicio')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(2),
                        
                        Forms\Components\TextInput::make('valor')
                            ->label('Valor del Pago')
                            ->numeric()
                            ->required()
                            ->prefix('$')
                            ->default(0),
                        
                        Forms\Components\Select::make('metodo')
                            ->label('Método de Pago')
                            ->options([
                                'efectivo' => 'Efectivo',
                                'transferencia' => 'Transferencia',
                                'tarjeta' => 'Tarjeta',
                                'cheque' => 'Cheque',
                                'otro' => 'Otro',
                            ])
                            ->required()
                            ->default('efectivo'),
                        
                        Forms\Components\TextInput::make('referencia')
                            ->label('Referencia/Número')
                            ->maxLength(100)
                            ->visible(fn ($get) => in_array($get('metodo'), ['transferencia', 'tarjeta', 'cheque'])),
                        
                        Forms\Components\DatePicker::make('fecha_pago')
                            ->label('Fecha del Pago')
                            ->required()
                            ->default(now())
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        
                        Forms\Components\Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('proceso.descripcion_servicio')
                    ->label('Proceso')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn ($state) => $state),
                
                Tables\Columns\TextColumn::make('proceso.cliente.nombre')
                    ->label('Cliente')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('proceso.tramitador.nombre')
                    ->label('Tramitador')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('valor')
                    ->label('Valor')
                    ->money('COP')
                    ->sortable()
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('metodo')
                    ->label('Método')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'efectivo' => 'Efectivo',
                        'transferencia' => 'Transferencia',
                        'tarjeta' => 'Tarjeta',
                        'cheque' => 'Cheque',
                        'otro' => 'Otro',
                        default => ucfirst($state),
                    })
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'efectivo' => 'gray',
                        'transferencia' => 'info',
                        'tarjeta' => 'warning',
                        'cheque' => 'primary',
                        'otro' => 'secondary',
                        default => 'success',
                    }),
                
                Tables\Columns\TextColumn::make('referencia')
                    ->label('Referencia')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('fecha_pago')
                    ->label('Fecha Pago')
                    ->date('d/m/Y')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('metodo')
                    ->label('Método de Pago')
                    ->options([
                        'efectivo' => 'Efectivo',
                        'transferencia' => 'Transferencia',
                        'tarjeta' => 'Tarjeta',
                        'cheque' => 'Cheque',
                        'otro' => 'Otro',
                    ]),
                
                Tables\Filters\Filter::make('fecha_pago')
                    ->label('Fecha del Pago')
                    ->form([
                        Forms\Components\DatePicker::make('desde')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('hasta')
                            ->label('Hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['desde'], fn ($q, $date) => $q->whereDate('fecha_pago', '>=', $date))
                            ->when($data['hasta'], fn ($q, $date) => $q->whereDate('fecha_pago', '<=', $date));
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListPagos::route('/'),
            'create' => Pages\CreatePago::route('/create'),
            'edit' => Pages\EditPago::route('/{record}/edit'),
        ];
    }
}