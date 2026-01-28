<?php

namespace App\Filament\Resources\AbogadoResource\Pages;

use App\Filament\Resources\AbogadoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAbogados extends ListRecords
{
    protected static string $resource = AbogadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
