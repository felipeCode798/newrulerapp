<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TramitadorResource\Pages;
use App\Models\Tramitador;
use App\Models\Categoria;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TramitadorResource extends Resource
{
    protected static ?string $model = Tramitador::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationLabel = 'Tramitadores';

    protected static ?string $navigationGroup = 'Configuración';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Tramitador')
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('cedula')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('telefono')
                            ->tel()
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Toggle::make('activo')
                            ->label('Activo')
                            ->default(true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Precios de Cursos')
                    ->schema([
                        Forms\Components\TextInput::make('curso_50_transito')
                            ->label('Curso 50% - Valor Tránsito')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->required(),

                        Forms\Components\TextInput::make('curso_50_recibir')
                            ->label('Curso 50% - Valor a Recibir')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->required(),

                        Forms\Components\TextInput::make('curso_20_transito')
                            ->label('Curso 20% - Valor Tránsito')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->required(),

                        Forms\Components\TextInput::make('curso_20_recibir')
                            ->label('Curso 20% - Valor a Recibir')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->required(),
                    ])
                    ->columns(2),

                // Nueva sección para categorías con precios
                Forms\Components\Section::make('Categorías y Precios')
                    ->schema([
                        Forms\Components\Repeater::make('categorias')
                            ->relationship('categorias')
                            ->schema([
                                Forms\Components\Select::make('categoria_id')
                                    ->label('Categoría')
                                    ->options(Categoria::where('activa', true)->pluck('nombre', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                                Forms\Components\TextInput::make('precio')
                                    ->label('Precio')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0)
                                    ->required(),
                            ])
                            ->columns(2)
                            ->itemLabel(fn (array $state): ?string =>
                                $state['categoria_id'] ?? null ?
                                Categoria::find($state['categoria_id'])?->nombre : 'Nueva categoría'
                            )
                            ->reorderable()
                            ->defaultItems(0)
                            ->addActionLabel('Agregar Categoría'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('cedula')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('telefono')
                    ->searchable(),

                Tables\Columns\TextColumn::make('categorias.nombre')
                    ->label('Categorías')
                    ->badge()
                    ->separator(', ')
                    ->searchable(),

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

                Tables\Filters\SelectFilter::make('categorias')
                    ->relationship('categorias', 'nombre')
                    ->multiple()
                    ->searchable()
                    ->preload(),
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
            'index' => Pages\ListTramitadors::route('/'),
            'create' => Pages\CreateTramitador::route('/create'),
            'edit' => Pages\EditTramitador::route('/{record}/edit'),
        ];
    }
}
