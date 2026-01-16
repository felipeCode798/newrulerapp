<?php

namespace App\Filament\Resources\CategoriaLicenciaResource\Pages;

use App\Filament\Resources\CategoriaLicenciaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategoriaLicencias extends ListRecords
{
    protected static string $resource = CategoriaLicenciaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
