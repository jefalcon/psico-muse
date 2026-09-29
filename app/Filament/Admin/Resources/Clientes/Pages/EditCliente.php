<?php

namespace App\Filament\Admin\Resources\Clientes\Pages;

use App\Filament\Admin\Resources\Clientes\ClienteResource;
use App\Models\Cliente;
use App\Models\User;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class EditCliente extends EditRecord
{
    protected static string $resource = ClienteResource::class;

    /** @param array<string, mixed> $data */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $cliente = $this->record;
        assert($cliente instanceof Cliente);

        $data['nombre'] = $cliente->user?->name;
        $data['email'] = $cliente->user?->email;

        return $data;
    }

    /** @param array<string, mixed> $data */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        assert($record instanceof Cliente);

        $user = $record->user;

        if ($user && User::where('email', $data['email'])->where('id', '!=', $user->id)->exists()) {
            throw ValidationException::withMessages([
                'data.email' => 'Ya existe otro usuario con ese email.',
            ]);
        }

        $user?->update([
            'name' => $data['nombre'],
            'email' => $data['email'],
        ]);

        $record->update([
            'telefono' => $data['telefono'] ?? null,
            'fecha_nacimiento' => $data['fecha_nacimiento'] ?? null,
            'notas_internas' => $data['notas_internas'] ?? null,
        ]);

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
