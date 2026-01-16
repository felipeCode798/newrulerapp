<?php

namespace App\Filament\Resources\EscuelaResource\Pages;

use App\Filament\Resources\EscuelaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEscuela extends EditRecord
{
    protected static string $resource = EscuelaResource::class;

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
