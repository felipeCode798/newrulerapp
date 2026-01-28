<?php

namespace App\Filament\Resources\AbogadoResource\Pages;

use App\Filament\Resources\AbogadoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAbogado extends EditRecord
{
    protected static string $resource = AbogadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
