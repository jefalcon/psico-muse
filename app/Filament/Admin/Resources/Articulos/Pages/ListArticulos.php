<?php

namespace App\Filament\Admin\Resources\Articulos\Pages;

use App\Filament\Admin\Resources\Articulos\ArticuloResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListArticulos extends ListRecords
{
    protected static string $resource = ArticuloResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Nuevo artículo'),
        ];
    }
}
