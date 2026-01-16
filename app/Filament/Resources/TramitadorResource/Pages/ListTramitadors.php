<?php

namespace App\Filament\Resources\TramitadorResource\Pages;

use App\Filament\Resources\TramitadorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTramitadors extends ListRecords
{
    protected static string $resource = TramitadorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
