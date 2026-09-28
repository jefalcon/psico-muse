<?php

namespace App\Filament\Admin\Resources\Hilos\Pages;

use App\Filament\Admin\Resources\Hilos\HiloResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateHilo extends CreateRecord
{
    protected static string $resource = HiloResource::class;

    /** @param array<string, mixed> $data */
    protected function handleRecordCreation(array $data): Model
    {
        $hilo = \App\Services\GestorMensajes::abrirHilo(
            (int) $data['cliente_id'],
            $data['asunto'],
            auth()->user(),
            $data['cuerpo']
        );

        $this->record = $hilo;

        return $hilo;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
