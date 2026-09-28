<?php

namespace App\Filament\Admin\Resources\Historial\Pages;

use App\Filament\Admin\Resources\Historial\EntradaHistorialResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEntradaHistorial extends ViewRecord
{
    protected static string $resource = EntradaHistorialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->label('Editar'),
        ];
    }
}
