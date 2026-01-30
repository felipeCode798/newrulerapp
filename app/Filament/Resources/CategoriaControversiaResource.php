<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoriaControversiaResource\Pages;
use App\Models\CategoriaControversia;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CategoriaControversiaResource extends Resource
{
    protected static ?string $model = CategoriaControversia::class;

    protected static ?string $navigationIcon = 'heroicon-o-scale';

    protected static ?string $navigationLabel = 'Categorías Controversia';

    protected static ?string $navigationGroup = 'Configuración';

    //protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Categoría')
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(3),

                        Forms\Components\TextInput::make('codigo')
                            ->label('Código')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('precio_cliente')
                            ->label('Precio para Cliente')
                            ->numeric()
                            ->columnSpan(2)
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

                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),

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
            'index' => Pages\ListCategoriaControversias::route('/'),
            'create' => Pages\CreateCategoriaControversia::route('/create'),
            'edit' => Pages\EditCategoriaControversia::route('/{record}/edit'),
        ];
    }
}
