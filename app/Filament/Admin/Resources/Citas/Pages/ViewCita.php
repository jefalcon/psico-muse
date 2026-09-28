<?php

namespace App\Filament\Admin\Resources\Citas\Pages;

use App\Filament\Admin\Resources\Citas\CitaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCita extends ViewRecord
{
    protected static string $resource = CitaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CitaResource::accionConfirmar(),
            CitaResource::accionRechazar(),
            CitaResource::accionReprogramar(),
            EditAction::make()->label('Editar'),
        ];
    }
}
