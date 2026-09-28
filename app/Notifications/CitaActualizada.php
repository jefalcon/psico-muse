<?php

namespace App\Notifications;

use App\Models\Cita;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CitaActualizada extends Notification
{
    use Queueable;

    public function __construct(
        public Cita $cita,
        public string $accion,
        public string $url,
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $cuando = $this->cita->inicio->format('d/m/Y H:i');
        $mensaje = (new MailMessage)
            ->subject('Tu cita del '.$cuando.': '.$this->accion)
            ->greeting('Hola '.$notifiable->name.',');

        match ($this->cita->estado) {
            Cita::ESTADO_CONFIRMADA => $mensaje->line('Tu cita de '.$this->cita->servicio->nombre.' del '.$cuando.' ha sido confirmada.'),
            Cita::ESTADO_RECHAZADA => $mensaje->line('Lamentablemente no podemos atender tu solicitud de cita del '.$cuando.'. Motivo: '.($this->cita->motivo_rechazo ?? '—')),
            default => $mensaje->line('Tu cita ha sido reprogramada al '.$cuando.'.'),
        };

        return $mensaje
            ->action('Ver mis citas', $this->url)
            ->line('Gracias por confiar en Consulta Demo.');
    }
}
