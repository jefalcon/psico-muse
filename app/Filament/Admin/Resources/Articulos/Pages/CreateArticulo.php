<?php

namespace App\Filament\Admin\Resources\Articulos\Pages;

use App\Filament\Admin\Resources\Articulos\ArticuloResource;
use Filament\Resources\Pages\CreateRecord;

class CreateArticulo extends CreateRecord
{
    protected static string $resource = ArticuloResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
