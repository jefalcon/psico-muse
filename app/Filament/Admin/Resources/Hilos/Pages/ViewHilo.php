<?php

namespace App\Filament\Admin\Resources\Hilos\Pages;

use App\Filament\Admin\Resources\Hilos\HiloResource;
use Filament\Resources\Pages\ViewRecord;

class ViewHilo extends ViewRecord
{
    protected static string $resource = HiloResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);
        $this->record->marcarLeidoPara(auth()->user());
    }

    protected function getHeaderActions(): array
    {
        return [
            HiloResource::accionResponder(),
        ];
    }
}
