<?php

namespace App\Services;

use App\Models\Cita;
use App\Notifications\CitaActualizada;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Notifications\Notification as AvisoFilament;
use RuntimeException;

class GestorCitas
{
    public static function confirmar(Cita $cita): void
    {
        if ($cita->estado === Cita::ESTADO_CONFIRMADA) {
            return;
        }

        if (Cita::solapaConConfirmada($cita->inicio, $cita->fin, $cita->id)) {
            throw new RuntimeException('No se puede confirmar: se solapa con otra cita confirmada.');
        }

        $cita->update(['estado' => Cita::ESTADO_CONFIRMADA, 'motivo_rechazo' => null]);
        self::avisar($cita, 'confirmada');
    }

    public static function rechazar(Cita $cita, string $motivo): void
    {
        $motivo = trim($motivo);

        if ($motivo === '') {
            throw new RuntimeException('El rechazo exige indicar un motivo.');
        }

        $cita->update(['estado' => Cita::ESTADO_RECHAZADA, 'motivo_rechazo' => $motivo]);
        self::avisar($cita, 'rechazada');
    }

    public static function reprogramar(Cita $cita, Carbon $nuevoInicio): void
    {
        $duracion = $cita->inicio->diffInMinutes($cita->fin);
        $nuevoFin = $nuevoInicio->copy()->addMinutes($duracion);

        if (! Horario::enHorario($nuevoInicio, $nuevoFin)) {
            throw new RuntimeException('El nuevo horario queda fuera del horario laboral.');
        }

        if (Cita::solapaConConfirmada($nuevoInicio, $nuevoFin, $cita->id)) {
            throw new RuntimeException('El nuevo horario se solapa con otra cita confirmada.');
        }

        $cita->update([
            'inicio' => $nuevoInicio,
            'fin' => $nuevoFin,
            'estado' => Cita::ESTADO_CONFIRMADA,
            'motivo_rechazo' => null,
        ]);

        self::avisar($cita, 'reprogramada');
    }

    public static function cancelarPorCliente(Cita $cita): void
    {
        if (! $cita->puedeCancelarCliente()) {
            throw new RuntimeException('Solo puedes cancelar citas solicitadas o confirmadas con más de 24 horas de antelación.');
        }

        $cita->update(['estado' => Cita::ESTADO_CANCELADA]);
    }

    public static function solicitar(int $clienteId, int $servicioId, Carbon $inicio, ?string $comentario = null): Cita
    {
        $servicio = \App\Models\Servicio::findOrFail($servicioId);
        $fin = $inicio->copy()->addMinutes($servicio->duracion_minutos);

        if ($inicio->lessThan(now()->addHours(24))) {
            throw new RuntimeException('Las citas deben solicitarse con al menos 24 horas de antelación.');
        }

        if (! Horario::enHorario($inicio, $fin)) {
            throw new RuntimeException('El hueco elegido queda fuera del horario laboral.');
        }

        if (Cita::solapaConConfirmada($inicio, $fin)) {
            throw new RuntimeException('Ese hueco ya está ocupado. Elige otro, por favor.');
        }

        return Cita::create([
            'cliente_id' => $clienteId,
            'servicio_id' => $servicioId,
            'inicio' => $inicio,
            'fin' => $fin,
            'estado' => Cita::ESTADO_SOLICITADA,
            'comentario_cliente' => $comentario ? trim($comentario) : null,
        ]);
    }

    public static function avisar(Cita $cita, string $accion): void
    {
        $usuario = $cita->cliente->user;

        if (! $usuario) {
            return;
        }

        $url = Filament::getPanel('portal')->getUrl().'/mis-citas';

        $usuario->notify(new CitaActualizada($cita->load('servicio'), $accion, $url));

        $cuerpo = $cita->servicio->nombre.' · '.$cita->inicio->format('d/m/Y H:i');

        if ($accion === 'rechazada' && filled($cita->motivo_rechazo)) {
            $cuerpo .= ' · Motivo: '.$cita->motivo_rechazo;
        }

        AvisoFilament::make()
            ->title('Tu cita ha sido '.$accion)
            ->body($cuerpo)
            ->sendToDatabase($usuario);
    }
}
