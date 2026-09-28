<?php

namespace App\Filament\Admin\Resources\Historial\Pages;

use App\Filament\Admin\Resources\Historial\EntradaHistorialResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEntradaHistorial extends EditRecord
{
    protected static string $resource = EntradaHistorialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->label('Eliminar'),
        ];
    }

    protected function afterSave(): void
    {
        EntradaHistorialResource::completarMetadatosAdjuntos($this->record);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
