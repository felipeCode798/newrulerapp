<?php

namespace App\Filament\Resources\ClienteResource\Pages;

use App\Filament\Resources\ClienteResource;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewCliente extends ViewRecord
{
    protected static string $resource = ClienteResource::class;

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
                Infolists\Components\Section::make('Información Personal')
                    ->schema([
                        Infolists\Components\TextEntry::make('nombre')
                            ->label('Nombre Completo'),
                        
                        Infolists\Components\TextEntry::make('cedula')
                            ->label('Cédula'),
                        
                        Infolists\Components\TextEntry::make('email')
                            ->label('Correo Electrónico'),
                        
                        Infolists\Components\TextEntry::make('telefono')
                            ->label('Teléfono'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Estado')
                    ->schema([
                        Infolists\Components\IconEntry::make('activo')
                            ->label('Activo')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),
                    ]),

                Infolists\Components\Section::make('Información del Sistema')
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Usuario Asociado'),
                        
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Fecha de Creación')
                            ->dateTime('d/m/Y H:i'),
                        
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Última Actualización')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}