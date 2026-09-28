<?php

namespace App\Filament\AvatarProviders;

use Filament\AvatarProviders\Contracts\AvatarProvider;
use Illuminate\Database\Eloquent\Model;

/**
 * Avatar local con las iniciales del usuario en un SVG embebido (data URI).
 * Sustituye al proveedor por defecto de Filament (ui-avatars.com) para que
 * ninguna página haga peticiones a dominios externos.
 */
class InicialesAvatarProvider implements AvatarProvider
{
    public function get(Model $record): string
    {
        $nombre = trim((string) ($record->getAttribute('name') ?? ''));

        $iniciales = collect(preg_split('/\s+/u', $nombre) ?: [])
            ->filter()
            ->map(fn (string $palabra): string => mb_strtoupper(mb_substr($palabra, 0, 1)))
            ->take(2)
            ->implode('');

        if ($iniciales === '') {
            $iniciales = '?';
        }

        $colores = ['#0f766e', '#1d4ed8', '#6d28d9', '#be123c', '#b45309', '#047857'];
        $fondo = $colores[abs(crc32($nombre)) % count($colores)];

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64">'
            .'<rect width="64" height="64" rx="32" fill="'.$fondo.'"/>'
            .'<text x="32" y="41" font-family="system-ui, sans-serif" font-size="24" fill="#ffffff" text-anchor="middle">'
            .e($iniciales).'</text></svg>';

        return 'data:image/svg+xml,'.rawurlencode($svg);
    }
}
