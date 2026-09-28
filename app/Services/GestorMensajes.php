<?php

namespace App\Services;

use App\Models\Hilo;
use App\Models\Mensaje;
use App\Models\User;
use App\Notifications\MensajeRecibido;
use Filament\Facades\Filament;
use Filament\Notifications\Notification as AvisoFilament;

class GestorMensajes
{
    public static function responder(Hilo $hilo, User $remitente, string $cuerpo): Mensaje
    {
        $mensaje = Mensaje::create([
            'hilo_id' => $hilo->id,
            'remitente_id' => $remitente->id,
            'cuerpo' => trim($cuerpo),
        ]);

        self::avisarDestinatarios($hilo, $remitente);

        return $mensaje;
    }

    public static function abrirHilo(int $clienteId, string $asunto, User $remitente, string $cuerpo): Hilo
    {
        $hilo = Hilo::create([
            'cliente_id' => $clienteId,
            'asunto' => trim($asunto),
            'creado_por' => $remitente->id,
        ]);

        Mensaje::create([
            'hilo_id' => $hilo->id,
            'remitente_id' => $remitente->id,
            'cuerpo' => trim($cuerpo),
        ]);

        self::avisarDestinatarios($hilo, $remitente);

        return $hilo;
    }

    /**
     * Envío a varios: cada cliente recibe el mensaje en su propio hilo individual.
     *
     * @param array<int> $clienteIds
     * @return array<int> IDs de los hilos creados.
     */
    public static function envioMultiple(array $clienteIds, string $asunto, User $remitente, string $cuerpo): array
    {
        $ids = [];
        foreach (array_unique($clienteIds) as $clienteId) {
            $ids[] = self::abrirHilo((int) $clienteId, $asunto, $remitente, $cuerpo)->id;
        }

        return $ids;
    }

    /** @return \Illuminate\Support\Collection<int, User> */
    public static function destinatarios(Hilo $hilo, User $remitente): \Illuminate\Support\Collection
    {
        if ($remitente->isAdmin()) {
            return collect([$hilo->cliente->user])->filter();
        }

        return User::where('role', 'admin')->get();
    }

    public static function avisarDestinatarios(Hilo $hilo, User $remitente): void
    {
        foreach (self::destinatarios($hilo, $remitente) as $destinatario) {
            $url = $destinatario->isAdmin()
                ? Filament::getPanel('admin')->getUrl().'/hilos/'.$hilo->id
                : Filament::getPanel('portal')->getUrl().'/mensajes?hilo='.$hilo->id;

            $destinatario->notify(new MensajeRecibido($hilo->asunto, $remitente->name, $url));

            AvisoFilament::make()
                ->title('Nuevo mensaje: '.$hilo->asunto)
                ->body('De '.$remitente->name)
                ->sendToDatabase($destinatario);
        }
    }
}
