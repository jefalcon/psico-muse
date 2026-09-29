<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    protected $fillable = [
        'user_id',
        'telefono',
        'fecha_nacimiento',
        'notas_internas',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function nombre(): string
    {
        return $this->user->name ?? '—';
    }

    public function email(): string
    {
        return $this->user->email ?? '—';
    }

    /** Etiqueta "Nombre (email)" para los selectores de cliente. */
    public function etiquetaSelector(): string
    {
        $usuario = $this->user;

        if (! $usuario) {
            return "Cliente #{$this->id}";
        }

        return "{$usuario->name} ({$usuario->email})";
    }

    /**
     * Opciones id de cliente => etiqueta, solo clientes (nunca usuarios
     * sueltos como la admin) y con el id de cliente como valor.
     *
     * @return array<int, string>
     */
    public static function opcionesParaSelector(): array
    {
        return static::query()
            ->with('user')
            ->whereHas('user')
            ->get()
            ->sortBy(fn (Cliente $cliente) => (string) $cliente->user?->name)
            ->mapWithKeys(fn (Cliente $cliente) => [$cliente->id => $cliente->etiquetaSelector()])
            ->all();
    }

    /** @return HasMany<Lead, $this> */
    public function leadsConvertidos(): HasMany
    {
        return $this->hasMany(Lead::class, 'cliente_id');
    }

    /** @return HasMany<EntradaHistorial, $this> */
    public function entradasHistorial(): HasMany
    {
        return $this->hasMany(EntradaHistorial::class);
    }

    /** @return HasMany<Hilo, $this> */
    public function hilos(): HasMany
    {
        return $this->hasMany(Hilo::class);
    }

    /** @return HasMany<Cita, $this> */
    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class);
    }
}
