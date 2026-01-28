<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Información de Acceso')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Nombre Completo'),
                        
                        Infolists\Components\TextEntry::make('email')
                            ->label('Correo Electrónico'),
                        
                        Infolists\Components\IconEntry::make('email_verified_at')
                            ->label('Email Verificado')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Roles y Permisos')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('roles')
                            ->label('Roles Asignados')
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->label('Nombre del Rol')
                                    ->badge()
                                    ->color('primary'),
                            ])
                            ->columns(1),
                    ])
                    ->collapsible(),

                Infolists\Components\Section::make('Información del Sistema')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Fecha de Creación')
                            ->dateTime('d/m/Y H:i'),
                        
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Última Actualización')
                            ->dateTime('d/m/Y H:i'),
                        
                        Infolists\Components\TextEntry::make('cliente.nombre')
                            ->label('Cliente Asociado')
                            ->placeholder('No asociado')
                            ->visible(fn ($record) => $record->cliente !== null),
                        
                        Infolists\Components\TextEntry::make('tramitador.nombre')
                            ->label('Tramitador Asociado')
                            ->placeholder('No asociado')
                            ->visible(fn ($record) => $record->tramitador !== null),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}