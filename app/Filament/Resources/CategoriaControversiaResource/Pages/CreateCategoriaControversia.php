<?php

namespace App\Filament\Resources\CategoriaControversiaResource\Pages;

use App\Filament\Resources\CategoriaControversiaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCategoriaControversia extends CreateRecord
{
    protected static string $resource = CategoriaControversiaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
