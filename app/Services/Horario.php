<?php

namespace App\Services;

use App\Models\Ajuste;
use App\Models\Cita;
use Carbon\Carbon;

/**
 * Horario laboral de la consulta y cálculo de huecos libres.
 *
 * Formato en ajustes (clave «horario», JSON):
 * {"1":[["09:00","14:00"],["16:00","20:00"]],"2":[...],...,"7":[]}
 * con días ISO (1 = lunes … 7 = domingo).
 */
class Horario
{
    public const CLAVE = 'horario';

    /** @return array<int, array<int, array{0: string, 1: string}>> */
    public static function porDefecto(): array
    {
        $laborable = [['09:00', '14:00'], ['16:00', '20:00']];

        return [
            1 => $laborable,
            2 => $laborable,
            3 => $laborable,
            4 => $laborable,
            5 => $laborable,
            6 => [],
            7 => [],
        ];
    }

    /** @return array<int, array<int, array{0: string, 1: string}>> */
    public static function segmentos(): array
    {
        $json = Ajuste::obtener(self::CLAVE);

        if ($json === null) {
            return self::porDefecto();
        }

        $datos = json_decode($json, true);

        if (! is_array($datos)) {
            return self::porDefecto();
        }

        $resultado = [];
        for ($dia = 1; $dia <= 7; $dia++) {
            $tramos = $datos[(string) $dia] ?? $datos[$dia] ?? [];
            $resultado[$dia] = is_array($tramos) ? array_values($tramos) : [];
        }

        return $resultado;
    }

    /** @param array<int, array<int, array{0: string, 1: string}>> $segmentos */
    public static function guardar(array $segmentos): void
    {
        Ajuste::guardar(self::CLAVE, json_encode($segmentos));
    }

    public static function enHorario(Carbon $inicio, Carbon $fin): bool
    {
        if ($fin->lessThanOrEqualTo($inicio)) {
            return false;
        }

        // La cita debe caber entera dentro de un único tramo del día de inicio.
        $dia = (int) $inicio->isoWeekday();
        $fecha = $inicio->toDateString();

        foreach (self::segmentos()[$dia] ?? [] as $tramo) {
            [$desde, $hasta] = $tramo;
            $tramoInicio = Carbon::parse("{$fecha} {$desde}");
            $tramoFin = Carbon::parse("{$fecha} {$hasta}");

            if ($inicio->greaterThanOrEqualTo($tramoInicio) && $fin->lessThanOrEqualTo($tramoFin)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Huecos libres de un día para una duración dada.
     *
     * @return array<string> Horas «H:i» ordenadas.
     */
    public static function huecosLibres(Carbon $dia, int $duracionMinutos, ?int $ignorarCitaId = null): array
    {
        $fecha = $dia->toDateString();
        $minimo = now()->addHours(24);
        $huecos = [];

        $bloqueantes = Cita::bloqueantes()
            ->when($ignorarCitaId, fn ($q) => $q->where('id', '!=', $ignorarCitaId))
            ->whereDate('inicio', $fecha)
            ->orderBy('inicio')
            ->get(['inicio', 'fin']);

        foreach (self::segmentos()[(int) $dia->copy()->isoWeekday()] ?? [] as $tramo) {
            [$desde, $hasta] = $tramo;
            $cursor = Carbon::parse("{$fecha} {$desde}");
            $limite = Carbon::parse("{$fecha} {$hasta}");

            while ($cursor->copy()->addMinutes($duracionMinutos)->lessThanOrEqualTo($limite)) {
                $fin = $cursor->copy()->addMinutes($duracionMinutos);

                $solapa = $bloqueantes->contains(
                    fn (Cita $c) => $c->inicio->lessThan($fin) && $c->fin->greaterThan($cursor)
                );

                if (! $solapa && $cursor->greaterThanOrEqualTo($minimo)) {
                    $huecos[] = $cursor->format('H:i');
                }

                $cursor->addMinutes(15);
            }
        }

        sort($huecos);

        return array_values(array_unique($huecos));
    }

    /** @return array<int, string> */
    public static function nombresDias(): array
    {
        return [
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            7 => 'Domingo',
        ];
    }
}
