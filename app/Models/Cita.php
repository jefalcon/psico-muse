<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $cliente_id
 * @property int $servicio_id
 * @property Carbon $inicio
 * @property Carbon $fin
 * @property string $estado
 * @property string|null $comentario_cliente
 * @property string|null $motivo_rechazo
 */
class Cita extends Model
{
    public const ESTADO_SOLICITADA = 'solicitada';

    public const ESTADO_CONFIRMADA = 'confirmada';

    public const ESTADO_RECHAZADA = 'rechazada';

    public const ESTADO_CANCELADA = 'cancelada';

    public const ESTADO_COMPLETADA = 'completada';

    protected $fillable = [
        'cliente_id',
        'servicio_id',
        'inicio',
        'fin',
        'estado',
        'comentario_cliente',
        'motivo_rechazo',
    ];

    protected function casts(): array
    {
        return [
            'inicio' => 'datetime',
            'fin' => 'datetime',
        ];
    }

    /** @return BelongsTo<Cliente, $this> */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /** @return BelongsTo<Servicio, $this> */
    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class);
    }

    public static function estados(): array
    {
        return [
            self::ESTADO_SOLICITADA => 'Solicitada',
            self::ESTADO_CONFIRMADA => 'Confirmada',
            self::ESTADO_RECHAZADA => 'Rechazada',
            self::ESTADO_CANCELADA => 'Cancelada',
            self::ESTADO_COMPLETADA => 'Completada',
        ];
    }

    public function estadoEtiqueta(): string
    {
        return self::estados()[$this->estado] ?? $this->estado;
    }

    /** @param Builder<$this> $query */
    public function scopeBloqueantes(Builder $query): Builder
    {
        return $query->whereIn('estado', [self::ESTADO_CONFIRMADA, self::ESTADO_COMPLETADA]);
    }

    public static function solapaConConfirmada(Carbon $inicio, Carbon $fin, ?int $ignorarId = null): bool
    {
        return self::bloqueantes()
            ->when($ignorarId, fn (Builder $q) => $q->where('id', '!=', $ignorarId))
            ->where('inicio', '<', $fin)
            ->where('fin', '>', $inicio)
            ->exists();
    }

    public function puedeCancelarCliente(): bool
    {
        return in_array($this->estado, [self::ESTADO_SOLICITADA, self::ESTADO_CONFIRMADA], true)
            && $this->inicio->greaterThan(now()->addHours(24));
    }
}
