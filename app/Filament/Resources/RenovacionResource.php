<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RenovacionResource\Pages;
use App\Models\Renovacion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RenovacionResource extends Resource
{
    protected static ?string $model = Renovacion::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationLabel = 'Renovaciones';

    protected static ?string $navigationGroup = 'Configuración';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Renovación')
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

                        Forms\Components\Select::make('tipo')
                            ->options([
                                'solo_examen' => 'Solo Examen',
                                'examen_lamina' => 'Examen y Lámina',
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('precio_cliente')
                            ->label('Precio para Cliente')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->required(),

                        Forms\Components\Toggle::make('activo')
                            ->label('Activo')
                            ->default(true),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'solo_examen' => 'Solo Examen',
                        'examen_lamina' => 'Examen y Lámina',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'solo_examen' => 'info',
                        'examen_lamina' => 'success',
                    }),

                Tables\Columns\TextColumn::make('precio_cliente')
                    ->label('Precio Cliente')
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
                Tables\Filters\SelectFilter::make('tipo')
                    ->options([
                        'solo_examen' => 'Solo Examen',
                        'examen_lamina' => 'Examen y Lámina',
                    ]),

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
            'index' => Pages\ListRenovacions::route('/'),
            'create' => Pages\CreateRenovacion::route('/create'),
            'edit' => Pages\EditRenovacion::route('/{record}/edit'),
        ];
    }
}
