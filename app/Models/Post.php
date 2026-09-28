<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public const ESTADO_BORRADOR = 'borrador';

    public const ESTADO_PUBLICADO = 'publicado';

    protected $fillable = [
        'titulo',
        'slug',
        'extracto',
        'cuerpo',
        'imagen',
        'estado',
        'publicado_en',
    ];

    protected function casts(): array
    {
        return [
            'publicado_en' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** @param Builder<$this> $query */
    public function scopePublicados(Builder $query): Builder
    {
        return $query->where('estado', self::ESTADO_PUBLICADO)
            ->whereNotNull('publicado_en')
            ->where('publicado_en', '<=', now());
    }

    public static function estados(): array
    {
        return [
            self::ESTADO_BORRADOR => 'Borrador',
            self::ESTADO_PUBLICADO => 'Publicado',
        ];
    }

    public function getImagenUrlAttribute(): ?string
    {
        if (! $this->imagen) {
            return null;
        }

        if (str_starts_with($this->imagen, 'images/')) {
            return asset($this->imagen);
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->url($this->imagen);
    }
}
