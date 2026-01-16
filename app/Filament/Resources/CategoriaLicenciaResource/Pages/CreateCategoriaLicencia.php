<?php

namespace App\Filament\Resources\CategoriaLicenciaResource\Pages;

use App\Filament\Resources\CategoriaLicenciaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCategoriaLicencia extends CreateRecord
{
    protected static string $resource = CategoriaLicenciaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
