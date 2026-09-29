<?php

namespace App\Filament\Admin\Resources\Hilos\Pages;

use App\Filament\Admin\Resources\Hilos\HiloResource;
use App\Models\Hilo;
use Filament\Resources\Pages\ViewRecord;

class ViewHilo extends ViewRecord
{
    protected static string $resource = HiloResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $hilo = $this->record;
        assert($hilo instanceof Hilo);
        $hilo->marcarLeidoPara(auth()->user());
    }

    protected function getHeaderActions(): array
    {
        return [
            HiloResource::accionResponder(),
        ];
    }
}
