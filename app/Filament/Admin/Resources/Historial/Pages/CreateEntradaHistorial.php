<?php

namespace App\Filament\Admin\Resources\Historial\Pages;

use App\Filament\Admin\Resources\Historial\EntradaHistorialResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEntradaHistorial extends CreateRecord
{
    protected static string $resource = EntradaHistorialResource::class;

    protected function afterCreate(): void
    {
        EntradaHistorialResource::completarMetadatosAdjuntos($this->record);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
