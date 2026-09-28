<?php

namespace App\Filament\Admin\Resources\Historial\Pages;

use App\Filament\Admin\Resources\Historial\EntradaHistorialResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEntradasHistorial extends ListRecords
{
    protected static string $resource = EntradaHistorialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Nueva entrada'),
        ];
    }
}
