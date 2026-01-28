<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CiaResource\Pages;
use App\Models\Cia;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CiaResource extends Resource
{
    protected static ?string $model = Cia::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'Configuración';
    protected static ?string $modelLabel = 'CIA';
    protected static ?string $pluralModelLabel = 'CIAs';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la CIA')
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre de la CIA')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        
                        Forms\Components\TextInput::make('codigo')
                            ->label('Código')
                            ->maxLength(50)
                            ->unique(ignoreRecord: true),
                        
                        Forms\Components\TextInput::make('direccion')
                            ->label('Dirección')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('telefono')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(20),
                        
                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('contacto')
                            ->label('Persona de Contacto')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('celular_contacto')
                            ->label('Celular Contacto')
                            ->tel()
                            ->maxLength(20),
                        
                        Forms\Components\Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Precios por Categoría de Controversia')
                    ->schema([
                        Forms\Components\Repeater::make('precios')
                            ->relationship('precios')
                            ->schema([
                                Forms\Components\Select::make('categoria_controversia_id')
                                    ->label('Categoría de Controversia')
                                    ->relationship('categoriaControversia', 'nombre')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                
                                Forms\Components\TextInput::make('precio')
                                    ->label('Precio')
                                    ->numeric()
                                    ->required()
                                    ->prefix('$')
                                    ->default(0),
                                
                                Forms\Components\Textarea::make('observaciones')
                                    ->label('Observaciones')
                                    ->rows(2),
                            ])
                            ->columns(2)
                            ->collapsible(),
                    ])
                    ->collapsible(),
                
                Forms\Components\Toggle::make('activo')
                    ->label('Activo')
                    ->default(true)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('contacto')
                    ->label('Contacto')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('precios_count')
                    ->label('Precios Configurados')
                    ->counts('precios')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('activo')
                    ->label('Solo activos')
                    ->query(fn ($query) => $query->where('activo', true)),
                
                Tables\Filters\Filter::make('inactivo')
                    ->label('Solo inactivos')
                    ->query(fn ($query) => $query->where('activo', false)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activar')
                        ->label('Activar')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['activo' => true])),
                    Tables\Actions\BulkAction::make('desactivar')
                        ->label('Desactivar')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['activo' => false])),
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
            'index' => Pages\ListCias::route('/'),
            'create' => Pages\CreateCia::route('/create'),
            'edit' => Pages\EditCia::route('/{record}/edit'),
        ];
    }
}