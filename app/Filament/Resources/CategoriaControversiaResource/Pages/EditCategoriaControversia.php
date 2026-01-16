<?php

namespace App\Filament\Resources\CategoriaControversiaResource\Pages;

use App\Filament\Resources\CategoriaControversiaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategoriaControversia extends EditRecord
{
    protected static string $resource = CategoriaControversiaResource::class;

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
