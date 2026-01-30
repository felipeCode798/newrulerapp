<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoriaLicenciaResource\Pages;
use App\Models\CategoriaLicencia;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CategoriaLicenciaResource extends Resource
{
    protected static ?string $model = CategoriaLicencia::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationLabel = 'Categorías Licencia';

    protected static ?string $navigationGroup = 'Configuración';

    //protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Categoría')
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('codigo')
                            ->label('Código')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\Toggle::make('activo')
                            ->label('Activo')
                            ->default(true),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Valores de la Licencia')
                    ->schema([
                        Forms\Components\TextInput::make('examen_medico')
                            ->label('Examen Médico')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->required()
                            ->helperText('Valor del examen médico'),

                        Forms\Components\TextInput::make('lamina')
                            ->label('Lámina')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->required()
                            ->helperText('Valor de la lámina/impresión'),

                        Forms\Components\TextInput::make('honorarios')
                            ->label('Honorarios')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->required()
                            ->helperText('Honorarios del trámite'),

                        Forms\Components\TextInput::make('sin_curso')
                            ->label('Sin Curso')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->required()
                            ->helperText('Valor adicional sin curso'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('examen_medico')
                    ->label('Examen Médico')
                    ->money('COP')
                    ->sortable(),

                Tables\Columns\TextColumn::make('lamina')
                    ->label('Lámina')
                    ->money('COP')
                    ->sortable(),

                Tables\Columns\TextColumn::make('honorarios')
                    ->money('COP')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sin_curso')
                    ->label('Sin Curso')
                    ->money('COP')
                    ->sortable()
                    ->toggleable(),

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
            'index' => Pages\ListCategoriaLicencias::route('/'),
            'create' => Pages\CreateCategoriaLicencia::route('/create'),
            'edit' => Pages\EditCategoriaLicencia::route('/{record}/edit'),
        ];
    }
}
