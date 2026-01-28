<?php

namespace App\Filament\Resources\CiaResource\Pages;

use App\Filament\Resources\CiaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCia extends EditRecord
{
    protected static string $resource = CiaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
