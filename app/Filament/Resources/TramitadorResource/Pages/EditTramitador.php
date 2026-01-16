<?php

namespace App\Filament\Resources\TramitadorResource\Pages;

use App\Filament\Resources\TramitadorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTramitador extends EditRecord
{
    protected static string $resource = TramitadorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Actualizar usuario asociado
        $this->record->user->update([
            'name' => $data['nombre'],
            'email' => $data['email'],
        ]);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
