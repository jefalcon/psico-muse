<?php

namespace App\Filament\Admin\Resources\Hilos\Pages;

use App\Filament\Admin\Resources\Hilos\HiloResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHilos extends ListRecords
{
    protected static string $resource = HiloResource::class;

    protected function getHeaderActions(): array
    {
        return [
            HiloResource::accionEnvioMultiple(),
            CreateAction::make()->label('Abrir hilo'),
        ];
    }
}
