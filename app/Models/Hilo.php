<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hilo extends Model
{
    protected $fillable = [
        'cliente_id',
        'asunto',
        'creado_por',
    ];

    /** @return BelongsTo<Cliente, $this> */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /** @return BelongsTo<User, $this> */
    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    /** @return HasMany<Mensaje, $this> */
    public function mensajes(): HasMany
    {
        return $this->hasMany(Mensaje::class);
    }

    public function ultimoMensaje(): ?Mensaje
    {
        return $this->mensajes()->latest('id')->first();
    }

    public function sinLeerPara(User $usuario): int
    {
        return $this->mensajes()
            ->where('remitente_id', '!=', $usuario->id)
            ->whereNull('leido_en')
            ->count();
    }

    public function marcarLeidoPara(User $usuario): void
    {
        $this->mensajes()
            ->where('remitente_id', '!=', $usuario->id)
            ->whereNull('leido_en')
            ->update(['leido_en' => now()]);
    }
}
