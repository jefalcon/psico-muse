<?php

namespace App\Filament\Admin\Resources\Citas\Pages;

use App\Filament\Admin\Resources\Citas\CitaResource;
use App\Filament\Concerns\LanzaErroresDeFormulario;
use App\Models\Cita;
use App\Models\Servicio;
use App\Services\Horario;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateCita extends CreateRecord
{
    use LanzaErroresDeFormulario;

    protected static string $resource = CitaResource::class;

    /** @param array<string, mixed> $data */
    protected function handleRecordCreation(array $data): Model
    {
        $servicio = Servicio::findOrFail($data['servicio_id']);
        $inicio = Carbon::parse($data['inicio']);
        $fin = $inicio->copy()->addMinutes($servicio->duracion_minutos);

        if (! Horario::enHorario($inicio, $fin)) {
            self::errorDeFormulario('inicio', 'La cita queda fuera del horario laboral.');
        }

        if (($data['estado'] ?? Cita::ESTADO_CONFIRMADA) !== Cita::ESTADO_SOLICITADA
            && Cita::solapaConConfirmada($inicio, $fin)) {
            self::errorDeFormulario('inicio', 'Ese horario se solapa con otra cita confirmada.');
        }

        return Cita::create([
            'cliente_id' => $data['cliente_id'],
            'servicio_id' => $data['servicio_id'],
            'inicio' => $inicio,
            'fin' => $fin,
            'estado' => $data['estado'] ?? Cita::ESTADO_CONFIRMADA,
            'comentario_cliente' => $data['comentario_cliente'] ?? null,
            'motivo_rechazo' => $data['motivo_rechazo'] ?? null,
        ]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
