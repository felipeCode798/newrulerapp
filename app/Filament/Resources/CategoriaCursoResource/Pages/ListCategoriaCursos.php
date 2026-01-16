<?php

namespace App\Filament\Resources\CategoriaCursoResource\Pages;

use App\Filament\Resources\CategoriaCursoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategoriaCursos extends ListRecords
{
    protected static string $resource = CategoriaCursoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
