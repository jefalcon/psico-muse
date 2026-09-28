<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mensaje extends Model
{
    protected $fillable = [
        'hilo_id',
        'remitente_id',
        'cuerpo',
        'leido_en',
    ];

    protected function casts(): array
    {
        return [
            'leido_en' => 'datetime',
        ];
    }

    /** @return BelongsTo<Hilo, $this> */
    public function hilo(): BelongsTo
    {
        return $this->belongsTo(Hilo::class);
    }

    /** @return BelongsTo<User, $this> */
    public function remitente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'remitente_id');
    }
}
