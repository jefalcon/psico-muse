<?php

namespace App\Filament\Portal\Pages;

use App\Models\Hilo;
use App\Services\GestorMensajes;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;

class Mensajes extends Page
{
    protected string $view = 'filament.portal.pages.mensajes';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?string $navigationLabel = 'Mensajes';

    protected static ?string $title = 'Mensajes';

    protected static ?int $navigationSort = 3;

    public ?int $hilo_id = null;

    public string $cuerpo = '';

    public bool $mostrar_nuevo = false;

    public string $nuevo_asunto = '';

    public string $nuevo_cuerpo = '';

    public function mount(): void
    {
        $pedido = request()->query('hilo');

        if ($pedido) {
            $this->seleccionar((int) $pedido);
        }
    }

    /** @return Collection<int, Hilo> */
    public function hilos(): Collection
    {
        $cliente = auth()->user()->cliente;

        if (! $cliente) {
            return collect();
        }

        return $cliente->hilos()->withCount('mensajes')->orderByDesc('updated_at')->get();
    }

    public function hiloActual(): ?Hilo
    {
        if (! $this->hilo_id) {
            return null;
        }

        $hilo = Hilo::with(['mensajes.remitente'])->find($this->hilo_id);

        if (! $hilo || $hilo->cliente_id !== auth()->user()->cliente?->id) {
            return null;
        }

        return $hilo;
    }

    public function seleccionar(int $id): void
    {
        $hilo = Hilo::find($id);

        if (! $hilo || $hilo->cliente_id !== auth()->user()->cliente?->id) {
            abort(404);
        }

        $this->hilo_id = $id;
        $this->mostrar_nuevo = false;
        $hilo->marcarLeidoPara(auth()->user());
    }

    public function responder(): void
    {
        $this->validate(['cuerpo' => ['required', 'string', 'max:10000']], [], ['cuerpo' => 'mensaje']);

        $hilo = $this->hiloActual();
        abort_unless($hilo, 404);

        GestorMensajes::responder($hilo, auth()->user(), $this->cuerpo);
        $hilo->touch();
        $this->cuerpo = '';

        Notification::make()->title('Mensaje enviado')->success()->send();
    }

    public function abrirNuevo(): void
    {
        $this->validate([
            'nuevo_asunto' => ['required', 'string', 'max:255'],
            'nuevo_cuerpo' => ['required', 'string', 'max:10000'],
        ], [], ['nuevo_asunto' => 'asunto', 'nuevo_cuerpo' => 'mensaje']);

        $hilo = GestorMensajes::abrirHilo(
            auth()->user()->cliente->id,
            $this->nuevo_asunto,
            auth()->user(),
            $this->nuevo_cuerpo
        );

        $this->nuevo_asunto = '';
        $this->nuevo_cuerpo = '';
        $this->mostrar_nuevo = false;
        $this->hilo_id = $hilo->id;

        Notification::make()->title('Hilo creado')->success()->send();
    }

    public static function getNavigationBadge(): ?string
    {
        $n = auth()->user()?->mensajesSinLeer() ?? 0;

        return $n > 0 ? (string) $n : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
