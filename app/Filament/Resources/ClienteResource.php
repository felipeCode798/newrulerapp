<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClienteResource\Pages;
use App\Models\Cliente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ClienteResource extends Resource
{
    protected static ?string $model = Cliente::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    
    protected static ?string $navigationLabel = 'Clientes';
    
    protected static ?string $navigationGroup = 'Gestión';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Personal')
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre Completo')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('cedula')
                            ->label('Cédula')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20),

                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('telefono')
                            ->label('Teléfono')
                            ->tel()
                            ->required()
                            ->maxLength(20),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Configuración')
                    ->schema([
                        Forms\Components\Toggle::make('activo')
                            ->label('Activo')
                            ->default(true)
                            ->inline(false),
                    ])
                    ->collapsible(),
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

                Tables\Columns\TextColumn::make('cedula')
                    ->label('Cédula')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->searchable(),

                Tables\Columns\IconColumn::make('activo')
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha Creación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Última Actualización')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('activo')
                    ->label('Estado')
                    ->options([
                        '1' => 'Activo',
                        '0' => 'Inactivo',
                    ]),

                Tables\Filters\Filter::make('created_at')
                    ->label('Fecha de Creación')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['created_from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->color('info'),
                    Tables\Actions\EditAction::make()
                        ->color('warning'),
                    Tables\Actions\DeleteAction::make()
                        ->color('danger'),
                ])
                ->label('Acciones')
                ->icon('heroicon-m-ellipsis-vertical')
                ->button()
                ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\BulkAction::make('activar')
                    ->label('Activar Seleccionados')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function ($records) {
                        foreach ($records as $record) {
                            $record->update(['activo' => true]);
                        }
                    })
                    ->requiresConfirmation(),
                Tables\Actions\BulkAction::make('desactivar')
                    ->label('Desactivar Seleccionados')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->action(function ($records) {
                        foreach ($records as $record) {
                            $record->update(['activo' => false]);
                        }
                    })
                    ->requiresConfirmation(),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }

    public static function getRelations(): array
    {
        return [
            // Puedes agregar relaciones aquí si necesitas
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClientes::route('/'),
            'create' => Pages\CreateCliente::route('/create'),
            'edit' => Pages\EditCliente::route('/{record}/edit'),
            'view' => Pages\ViewCliente::route('/{record}'),
        ];
    }
}