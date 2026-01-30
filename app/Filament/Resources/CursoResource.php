<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CursoResource\Pages;
use App\Models\Curso;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CursoResource extends Resource
{
    protected static ?string $model = Curso::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Cursos';

    protected static ?string $navigationGroup = 'Configuración';

    //protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Curso')
                    ->schema([
                        Forms\Components\TextInput::make('codigo')
                            ->label('Código')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('categoria')
                            ->label('Categoría')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Toggle::make('activo')
                            ->label('Activo')
                            ->default(true),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Precios para Clientes - 50%')
                    ->schema([
                        Forms\Components\TextInput::make('precio_cliente_50_transito')
                            ->label('Valor Tránsito')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->required(),

                        Forms\Components\TextInput::make('precio_cliente_50_recibir')
                            ->label('Valor a Recibir')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Precios para Clientes - 25%')
                    ->schema([
                        Forms\Components\TextInput::make('precio_cliente_20_transito')
                            ->label('Valor Tránsito')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->required(),

                        Forms\Components\TextInput::make('precio_cliente_20_recibir')
                            ->label('Valor a Recibir')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('categoria')
                    ->label('Categoría')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('precio_cliente_50_recibir')
                    ->label('Cliente 50%')
                    ->money('COP')
                    ->sortable(),

                Tables\Columns\TextColumn::make('precio_cliente_20_recibir')
                    ->label('Cliente 20%')
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
            'index' => Pages\ListCursos::route('/'),
            'create' => Pages\CreateCurso::route('/create'),
            'edit' => Pages\EditCurso::route('/{record}/edit'),
        ];
    }
}
