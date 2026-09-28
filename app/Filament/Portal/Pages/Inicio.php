<?php

namespace App\Filament\Portal\Pages;

use App\Models\Cita;
use App\Models\Hilo;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;

class Inicio extends Page
{
    protected string $view = 'filament.portal.pages.inicio';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?string $navigationLabel = 'Inicio';

    protected static ?string $title = 'Mi espacio';

    protected static ?int $navigationSort = 1;

    public static function getSlug(?\Filament\Panel $panel = null): string
    {
        return 'inicio';
    }

    /** @return Collection<int, Cita> */
    public function proximasCitas(): Collection
    {
        $cliente = auth()->user()->cliente;

        if (! $cliente) {
            return collect();
        }

        return $cliente->citas()
            ->with('servicio')
            ->whereIn('estado', [Cita::ESTADO_SOLICITADA, Cita::ESTADO_CONFIRMADA])
            ->where('inicio', '>=', now())
            ->orderBy('inicio')
            ->take(3)
            ->get();
    }

    /** @return Collection<int, Hilo> */
    public function hilosSinLeer(): Collection
    {
        $usuario = auth()->user();
        $cliente = $usuario->cliente;

        if (! $cliente) {
            return collect();
        }

        return $cliente->hilos()->with('mensajes')->get()
            ->filter(fn (Hilo $h) => $h->sinLeerPara($usuario) > 0)
            ->values();
    }
}
