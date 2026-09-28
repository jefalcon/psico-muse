<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\Lead;
use App\Models\User;
use Filament\Auth\Notifications\ResetPassword as NotificacionContrasena;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use RuntimeException;

class GestorClientes
{
    /**
     * Crea el usuario + ficha de cliente y envía el email para
     * establecer la contraseña. Devuelve [user, cliente].
     *
     * @return array{0: User, 1: Cliente}
     */
    public static function crearDesdeLead(Lead $lead): array
    {
        if ($lead->estado === Lead::ESTADO_CONVERTIDO && $lead->cliente_id) {
            throw new RuntimeException('Este lead ya fue convertido en cliente.');
        }

        if (User::where('email', $lead->email)->exists()) {
            throw new RuntimeException('Ya existe un usuario con el email '.$lead->email.'.');
        }

        return DB::transaction(function () use ($lead) {
            $user = User::create([
                'name' => $lead->nombre,
                'email' => $lead->email,
                'password' => Hash::make(Str::random(32)),
                'role' => 'cliente',
            ]);

            $cliente = Cliente::create([
                'user_id' => $user->id,
                'telefono' => $lead->telefono,
            ]);

            $lead->update([
                'estado' => Lead::ESTADO_CONVERTIDO,
                'cliente_id' => $cliente->id,
            ]);

            self::enviarEmailContrasena($user);

            return [$user, $cliente];
        });
    }

    /**
     * @return array{0: User, 1: Cliente}
     */
    public static function crearManual(string $nombre, string $email, ?string $telefono = null, ?string $fechaNacimiento = null, ?string $notas = null): array
    {
        if (User::where('email', $email)->exists()) {
            throw new RuntimeException('Ya existe un usuario con el email '.$email.'.');
        }

        return DB::transaction(function () use ($nombre, $email, $telefono, $fechaNacimiento, $notas) {
            $user = User::create([
                'name' => $nombre,
                'email' => $email,
                'password' => Hash::make(Str::random(32)),
                'role' => 'cliente',
            ]);

            $cliente = Cliente::create([
                'user_id' => $user->id,
                'telefono' => $telefono,
                'fecha_nacimiento' => $fechaNacimiento,
                'notas_internas' => $notas,
            ]);

            self::enviarEmailContrasena($user);

            return [$user, $cliente];
        });
    }

    public static function enviarEmailContrasena(User $user): void
    {
        $token = Password::createToken($user);
        $notificacion = app(NotificacionContrasena::class, ['token' => $token]);
        $notificacion->url = Filament::getPanel('portal')->getResetPasswordUrl($token, $user);
        $user->notify($notificacion);
    }
}
