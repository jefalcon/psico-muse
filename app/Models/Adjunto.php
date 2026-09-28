<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Adjunto extends Model
{
    protected $fillable = [
        'entrada_historial_id',
        'ruta',
        'nombre_original',
        'mime',
        'tamano',
    ];

    /** @return BelongsTo<EntradaHistorial, $this> */
    public function entrada(): BelongsTo
    {
        return $this->belongsTo(EntradaHistorial::class, 'entrada_historial_id');
    }
}
