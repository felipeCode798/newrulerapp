<?php

namespace App\Filament\Resources\CiaResource\Pages;

use App\Filament\Resources\CiaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCias extends ListRecords
{
    protected static string $resource = CiaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
