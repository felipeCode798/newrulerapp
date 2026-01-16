<?php

namespace App\Filament\Resources\CategoriaCursoResource\Pages;

use App\Filament\Resources\CategoriaCursoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategoriaCurso extends EditRecord
{
    protected static string $resource = CategoriaCursoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
