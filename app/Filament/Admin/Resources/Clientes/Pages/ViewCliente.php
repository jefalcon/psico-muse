<?php

namespace App\Filament\Admin\Resources\Clientes\Pages;

use App\Filament\Admin\Resources\Clientes\ClienteResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCliente extends ViewRecord
{
    protected static string $resource = ClienteResource::class;

    /** @return array<int, \Filament\Actions\Action> */
    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->label('Editar'),
        ];
    }
}
