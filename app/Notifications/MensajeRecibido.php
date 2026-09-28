<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MensajeRecibido extends Notification
{
    use Queueable;

    public function __construct(
        public string $asuntoHilo,
        public string $nombreRemitente,
        public string $url,
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nuevo mensaje: '.$this->asuntoHilo)
            ->greeting('Hola '.$notifiable->name.',')
            ->line($this->nombreRemitente.' te ha escrito en el hilo «'.$this->asuntoHilo.'».')
            ->action('Leer mensaje', $this->url)
            ->line('Gracias por confiar en Consulta Demo.');
    }
}
