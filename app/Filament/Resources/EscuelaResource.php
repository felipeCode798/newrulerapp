<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EscuelaResource\Pages;
use App\Models\Escuela;
use App\Models\PinEscuela;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EscuelaResource extends Resource
{
    protected static ?string $model = Escuela::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Escuelas';

    protected static ?string $navigationGroup = 'Configuración';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Escuela')
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('numero_pines')
                            ->label('Número de Pines')
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->minValue(0)
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                // Esta función se ejecutará al crear pines
                            }),

                        Forms\Components\TextInput::make('direccion')
                            ->label('Dirección')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('telefono')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('valor_carta_escuela')
                            ->label('Valor Carta Escuela')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->required(),

                        Forms\Components\Toggle::make('activo')
                            ->label('Activo')
                            ->default(true),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Asignación de Pines')
                    ->schema([
                        Forms\Components\Repeater::make('pines')
                            ->relationship('pines')
                            ->schema([
                                Forms\Components\TextInput::make('pin')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),

                                Forms\Components\Select::make('user_id')
                                    ->label('Usuario Asignado')
                                    ->options(User::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->nullable(),

                                Forms\Components\Select::make('estado')
                                    ->options([
                                        'disponible' => 'Disponible',
                                        'asignado' => 'Asignado',
                                        'usado' => 'Usado',
                                    ])
                                    ->default('disponible')
                                    ->required(),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->addActionLabel('Agregar PIN')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['pin'] ?? null),
                    ])
                    ->hidden(fn ($record) => $record === null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('numero_pines')
                    ->label('# Pines')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('pines_count')
                    ->label('Pines Creados')
                    ->counts('pines')
                    ->sortable(),

                Tables\Columns\TextColumn::make('direccion')
                    ->label('Dirección')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('telefono')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('valor_carta_escuela')
                    ->label('Valor Carta')
                    ->money('COP')
                    ->sortable(),

                Tables\Columns\IconColumn::make('activo')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('activo')
                    ->label('Estado')
                    ->placeholder('Todos')
                    ->trueLabel('Activos')
                    ->falseLabel('Inactivos'),
            ])
            ->actions([
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
            'index' => Pages\ListEscuelas::route('/'),
            'create' => Pages\CreateEscuela::route('/create'),
            'edit' => Pages\EditEscuela::route('/{record}/edit'),
        ];
    }
}
