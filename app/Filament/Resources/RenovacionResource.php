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
    protected static ?string $modelLabel = 'Renovacion';
    protected static ?string $pluralModelLabel = 'Renovaciones';
    protected static ?string $navigationGroup = 'Configuración';
    //protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Renovación')
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(3),

                        Forms\Components\TextInput::make('precio_renovacion')
                            ->label('Precio Renovación')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->required(),

                        Forms\Components\TextInput::make('precio_examen')
                            ->label('Precio Examen')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->required(),

                        Forms\Components\TextInput::make('precio_lamina')
                            ->label('Precio Lámina')
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

                Tables\Columns\TextColumn::make('precio_renovacion')
                    ->label('Renovación')
                    ->money('COP')
                    ->sortable(),

                Tables\Columns\TextColumn::make('precio_examen')
                    ->label('Examen')
                    ->money('COP')
                    ->sortable(),

                Tables\Columns\TextColumn::make('precio_lamina')
                    ->label('Lámina')
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRenovacions::route('/'),
            'create' => Pages\CreateRenovacion::route('/create'),
            'edit' => Pages\EditRenovacion::route('/{record}/edit'),
        ];
    }
}