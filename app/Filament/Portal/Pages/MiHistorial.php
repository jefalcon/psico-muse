<?php

namespace App\Filament\Portal\Pages;

use App\Models\EntradaHistorial;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;

class MiHistorial extends Page
{
    protected string $view = 'filament.portal.pages.mi-historial';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'Mi historial';

    protected static ?string $title = 'Mi historial';

    protected static ?int $navigationSort = 4;

    /** @return Collection<int, EntradaHistorial> */
    public function entradas(): Collection
    {
        $cliente = auth()->user()->cliente;

        if (! $cliente) {
            return collect();
        }

        return $cliente->entradasHistorial()
            ->with('adjuntos')
            ->where('visible_cliente', true)
            ->orderByDesc('fecha')
            ->get();
    }
}
