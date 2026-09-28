<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    public const ESTADO_NUEVO = 'nuevo';

    public const ESTADO_CONTACTADO = 'contactado';

    public const ESTADO_CUALIFICADO = 'cualificado';

    public const ESTADO_DESCARTADO = 'descartado';

    public const ESTADO_CONVERTIDO = 'convertido';

    public const ORIGEN_WEB = 'web';

    public const ORIGEN_MANUAL = 'manual';

    protected $fillable = [
        'nombre',
        'email',
        'telefono',
        'servicio_id',
        'mensaje',
        'consentimiento',
        'estado',
        'origen',
        'cliente_id',
    ];

    protected function casts(): array
    {
        return [
            'consentimiento' => 'boolean',
        ];
    }

    /** @return BelongsTo<Servicio, $this> */
    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class);
    }

    /** @return BelongsTo<Cliente, $this> */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /** @return HasMany<Interaccion, $this> */
    public function interacciones(): HasMany
    {
        return $this->hasMany(Interaccion::class)->orderByDesc('fecha');
    }

    public static function estados(): array
    {
        return [
            self::ESTADO_NUEVO => 'Nuevo',
            self::ESTADO_CONTACTADO => 'Contactado',
            self::ESTADO_CUALIFICADO => 'Cualificado',
            self::ESTADO_DESCARTADO => 'Descartado',
            self::ESTADO_CONVERTIDO => 'Convertido',
        ];
    }

    public static function origenes(): array
    {
        return [
            self::ORIGEN_WEB => 'Web',
            self::ORIGEN_MANUAL => 'Manual',
        ];
    }

    public function estadoEtiqueta(): string
    {
        return self::estados()[$this->estado] ?? $this->estado;
    }
}
