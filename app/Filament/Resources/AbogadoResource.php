<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbogadoResource\Pages;
use App\Models\Abogado;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AbogadoResource extends Resource
{
    protected static ?string $model = Abogado::class;
    protected static ?string $navigationIcon = 'heroicon-o-scale';
    protected static ?string $navigationGroup = 'Configuración';
    protected static ?string $modelLabel = 'Abogado';
    protected static ?string $pluralModelLabel = 'Abogados';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Personal')
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre Completo')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('documento')
                            ->label('Documento de Identidad')
                            ->required()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true),
                        
                        // Forms\Components\Select::make('especialidad')
                        //     ->label('Especialidad')
                        //     ->options([
                        //         'civil' => 'Civil',
                        //         'penal' => 'Penal',
                        //         'laboral' => 'Laboral',
                        //         'transito' => 'Tránsito',
                        //         'comercial' => 'Comercial',
                        //         'administrativo' => 'Administrativo',
                        //         'constitucional' => 'Constitucional',
                        //         'otro' => 'Otro',
                        //     ])
                        //     ->required()
                        //     ->default('transito'),
                        
                        // Forms\Components\TextInput::make('tarjeta_profesional')
                        //     ->label('Tarjeta Profesional')
                        //     ->maxLength(50),
                        
                        Forms\Components\TextInput::make('telefono')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(20),
                        
                        Forms\Components\TextInput::make('celular')
                            ->label('Celular')
                            ->tel()
                            ->maxLength(20),
                        
                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('direccion')
                            ->label('Dirección')
                            ->maxLength(255),
                        
                        // Forms\Components\TextInput::make('ciudad')
                        //     ->label('Ciudad')
                        //     ->maxLength(100)
                        //     ->columns(2),

                    ])
                    ->columns(2),
                
                // Forms\Components\Section::make('Información Profesional')
                //     ->schema([
                //         Forms\Components\TextInput::make('honorarios_hora')
                //             ->label('Honorarios por Hora')
                //             ->numeric()
                //             ->prefix('$')
                //             ->default(0),
                        
                //         Forms\Components\TextInput::make('porcentaje_comision')
                //             ->label('Porcentaje de Comisión')
                //             ->numeric()
                //             ->suffix('%')
                //             ->default(0),
                        
                //         Forms\Components\Textarea::make('areas_practica')
                //             ->label('Áreas de Práctica')
                //             ->rows(3),
                        
                //         Forms\Components\Textarea::make('formacion_academica')
                //             ->label('Formación Académica')
                //             ->rows(3),
                        
                //         Forms\Components\Textarea::make('experiencia')
                //             ->label('Experiencia')
                //             ->rows(3),
                        
                //         Forms\Components\Toggle::make('disponible')
                //             ->label('Disponible para nuevos casos')
                //             ->default(true),
                //     ])
                //     ->columns(2),
                
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
                
                Tables\Columns\TextColumn::make('documento')
                    ->label('Documento')
                    ->searchable()
                    ->sortable(),
                
                // Tables\Columns\TextColumn::make('especialidad')
                //     ->label('Especialidad')
                //     ->searchable()
                //     ->sortable()
                //     ->badge()
                //     ->color(fn ($state) => match($state) {
                //         'transito' => 'info',
                //         'civil' => 'primary',
                //         'penal' => 'danger',
                //         'laboral' => 'warning',
                //         default => 'gray',
                //     }),
                
                Tables\Columns\TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                
                // Tables\Columns\TextColumn::make('honorarios_hora')
                //     ->label('Honorarios/Hora')
                //     ->money('COP')
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                
                // Tables\Columns\IconColumn::make('disponible')
                //     ->label('Disponible')
                //     ->boolean()
                //     ->sortable(),
                
                Tables\Columns\IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('gastos_count')
                    ->label('Gastos Asignados')
                    ->counts('gastos')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Tables\Filters\SelectFilter::make('especialidad')
                //     ->label('Especialidad')
                //     ->options([
                //         'civil' => 'Civil',
                //         'penal' => 'Penal',
                //         'laboral' => 'Laboral',
                //         'transito' => 'Tránsito',
                //         'comercial' => 'Comercial',
                //         'administrativo' => 'Administrativo',
                //         'constitucional' => 'Constitucional',
                //         'otro' => 'Otro',
                //     ]),
                
                // Tables\Filters\Filter::make('disponible')
                //     ->label('Solo disponibles')
                //     ->query(fn ($query) => $query->where('disponible', true)),
                
                Tables\Filters\Filter::make('activo')
                    ->label('Solo activos')
                    ->query(fn ($query) => $query->where('activo', true)),
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
            'index' => Pages\ListAbogados::route('/'),
            'create' => Pages\CreateAbogado::route('/create'),
            'edit' => Pages\EditAbogado::route('/{record}/edit'),
        ];
    }
}