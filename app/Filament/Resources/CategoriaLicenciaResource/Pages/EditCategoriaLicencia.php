<?php

namespace App\Filament\Resources\CategoriaLicenciaResource\Pages;

use App\Filament\Resources\CategoriaLicenciaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategoriaLicencia extends EditRecord
{
    protected static string $resource = CategoriaLicenciaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
