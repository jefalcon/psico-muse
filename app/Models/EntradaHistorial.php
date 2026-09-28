<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EntradaHistorial extends Model
{
    protected $table = 'entradas_historial';

    protected $fillable = [
        'cliente_id',
        'fecha',
        'motivo',
        'notas_sesion',
        'plan_terapeutico',
        'visible_cliente',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'visible_cliente' => 'boolean',
        ];
    }

    /** @return BelongsTo<Cliente, $this> */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /** @return HasMany<Adjunto, $this> */
    public function adjuntos(): HasMany
    {
        return $this->hasMany(Adjunto::class, 'entrada_historial_id');
    }
}
