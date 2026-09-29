<?php

namespace App\Filament\Admin\Resources\Clientes\Pages;

use App\Filament\Admin\Resources\Clientes\ClienteResource;
use App\Filament\Concerns\LanzaErroresDeFormulario;
use App\Models\Cliente;
use App\Models\User;
use App\Services\GestorClientes;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateCliente extends CreateRecord
{
    use LanzaErroresDeFormulario;

    protected static string $resource = ClienteResource::class;

    /** @param array<string, mixed> $data */
    protected function handleRecordCreation(array $data): Model
    {
        if (User::where('email', $data['email'])->exists()) {
            self::errorDeFormulario('email', 'Ya existe un usuario con ese email.');
        }

        [, $cliente] = GestorClientes::crearManual(
            $data['nombre'],
            $data['email'],
            $data['telefono'] ?? null,
            $data['fecha_nacimiento'] ?? null,
            $data['notas_internas'] ?? null,
        );

        $this->record = $cliente;

        return $cliente;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Cliente creado. Se le ha enviado un email para establecer su contraseña.';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
