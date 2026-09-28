<?php

namespace App\Filament\Admin\Resources\Citas\Pages;

use App\Filament\Admin\Resources\Citas\CitaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCitas extends ListRecords
{
    protected static string $resource = CitaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Nueva cita'),
        ];
    }
}
