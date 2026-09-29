<?php

namespace App\Filament\Admin\Resources\Historial\Pages;

use App\Filament\Admin\Resources\Historial\EntradaHistorialResource;
use App\Models\EntradaHistorial;
use Filament\Resources\Pages\CreateRecord;

class CreateEntradaHistorial extends CreateRecord
{
    protected static string $resource = EntradaHistorialResource::class;

    protected function afterCreate(): void
    {
        $entrada = $this->record;
        assert($entrada instanceof EntradaHistorial);
        EntradaHistorialResource::completarMetadatosAdjuntos($entrada);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
