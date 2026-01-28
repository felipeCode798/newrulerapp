<?php

namespace App\Filament\Resources\CuadreResource\Pages;

use App\Filament\Resources\CuadreResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCuadre extends EditRecord
{
    protected static string $resource = CuadreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
