<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Servicio extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'duracion_minutos',
        'precio',
        'activo',
        'orden',
    ];

    protected function casts(): array
    {
        return [
            'duracion_minutos' => 'integer',
            'precio' => 'decimal:2',
            'activo' => 'boolean',
            'orden' => 'integer',
        ];
    }

    /** @return HasMany<Cita, $this> */
    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class);
    }

    /** @return HasMany<Lead, $this> */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    /** @param Builder<$this> $query */
    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true)->orderBy('orden')->orderBy('id');
    }

    public function precioFormateado(): ?string
    {
        if ($this->precio === null) {
            return null;
        }

        return number_format((float) $this->precio, 2, ',', '.').' €';
    }
}
