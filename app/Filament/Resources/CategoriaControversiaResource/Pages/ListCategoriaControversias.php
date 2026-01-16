<?php

namespace App\Filament\Resources\CategoriaControversiaResource\Pages;

use App\Filament\Resources\CategoriaControversiaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategoriaControversias extends ListRecords
{
    protected static string $resource = CategoriaControversiaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
