<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'admin' => $this->role === 'admin',
            'portal' => $this->role === 'cliente',
            default => false,
        };
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCliente(): bool
    {
        return $this->role === 'cliente';
    }

    /** @return HasOne<Cliente, $this> */
    public function cliente(): HasOne
    {
        return $this->hasOne(Cliente::class);
    }

    /** @return HasMany<Mensaje, $this> */
    public function mensajesEnviados(): HasMany
    {
        return $this->hasMany(Mensaje::class, 'remitente_id');
    }

    public function mensajesSinLeer(): int
    {
        if ($this->isCliente() && $this->cliente) {
            $hiloIds = $this->cliente->hilos()->pluck('id');

            return Mensaje::whereIn('hilo_id', $hiloIds)
                ->where('remitente_id', '!=', $this->id)
                ->whereNull('leido_en')
                ->count();
        }

        if ($this->isAdmin()) {
            return Mensaje::whereHas('remitente', fn ($q) => $q->where('role', 'cliente'))
                ->whereNull('leido_en')
                ->count();
        }

        return 0;
    }
}
