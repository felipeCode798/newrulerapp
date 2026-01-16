<?php

namespace App\Filament\Resources\EscuelaResource\Pages;

use App\Filament\Resources\EscuelaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEscuelas extends ListRecords
{
    protected static string $resource = EscuelaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
