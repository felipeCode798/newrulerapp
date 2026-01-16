<?php

namespace App\Filament\Resources\EscuelaResource\Pages;

use App\Filament\Resources\EscuelaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEscuela extends CreateRecord
{
    protected static string $resource = EscuelaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
