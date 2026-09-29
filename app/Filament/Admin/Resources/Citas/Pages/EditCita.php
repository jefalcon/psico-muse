<?php

namespace App\Filament\Admin\Resources\Citas\Pages;

use App\Filament\Admin\Resources\Citas\CitaResource;
use App\Filament\Concerns\LanzaErroresDeFormulario;
use App\Models\Cita;
use App\Models\Servicio;
use App\Services\Horario;
use Carbon\Carbon;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditCita extends EditRecord
{
    use LanzaErroresDeFormulario;

    protected static string $resource = CitaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CitaResource::accionConfirmar(),
            CitaResource::accionRechazar(),
            CitaResource::accionReprogramar(),
            DeleteAction::make()->label('Eliminar'),
        ];
    }

    /** @param array<string, mixed> $data */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        assert($record instanceof Cita);

        $servicio = Servicio::findOrFail($data['servicio_id']);
        $inicio = Carbon::parse($data['inicio']);
        $fin = $inicio->copy()->addMinutes($servicio->duracion_minutos);

        if (! Horario::enHorario($inicio, $fin)) {
            self::errorDeFormulario('inicio', 'La cita queda fuera del horario laboral.');
        }

        if (($data['estado'] ?? $record->estado) !== Cita::ESTADO_SOLICITADA
            && Cita::solapaConConfirmada($inicio, $fin, $record->id)) {
            self::errorDeFormulario('inicio', 'Ese horario se solapa con otra cita confirmada.');
        }

        if (($data['estado'] ?? '') === Cita::ESTADO_RECHAZADA && trim((string) ($data['motivo_rechazo'] ?? '')) === '') {
            self::errorDeFormulario('motivo_rechazo', 'El rechazo exige indicar un motivo.');
        }

        $record->update([
            'cliente_id' => $data['cliente_id'],
            'servicio_id' => $data['servicio_id'],
            'inicio' => $inicio,
            'fin' => $fin,
            'estado' => $data['estado'] ?? $record->estado,
            'comentario_cliente' => $data['comentario_cliente'] ?? null,
            'motivo_rechazo' => $data['motivo_rechazo'] ?? null,
        ]);

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
