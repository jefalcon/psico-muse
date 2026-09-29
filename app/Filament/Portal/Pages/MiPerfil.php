<?php

namespace App\Filament\Portal\Pages;

use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class MiPerfil extends Page
{
    protected string $view = 'filament.portal.pages.mi-perfil';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected static ?string $navigationLabel = 'Mi perfil';

    protected static ?string $title = 'Mi perfil';

    protected static ?int $navigationSort = 5;

    public string $telefono = '';

    public string $actual = '';

    public string $nueva = '';

    public string $nueva_confirmation = '';

    public function mount(): void
    {
        $this->telefono = (string) (auth()->user()->cliente->telefono ?? '');
    }

    public function guardarTelefono(): void
    {
        $this->validate(['telefono' => ['nullable', 'string', 'max:50']], [], ['telefono' => 'teléfono']);

        auth()->user()->cliente?->update(['telefono' => $this->telefono ?: null]);

        Notification::make()->title('Teléfono actualizado')->success()->send();
    }

    public function cambiarContrasena(): void
    {
        $this->validate([
            'actual' => ['required', 'string'],
            'nueva' => ['required', 'string', 'confirmed', Password::min(8)],
        ], [], ['actual' => 'contraseña actual', 'nueva' => 'nueva contraseña']);

        $usuario = auth()->user();

        if (! Hash::check($this->actual, $usuario->password)) {
            $this->addError('actual', 'La contraseña actual no es correcta.');

            return;
        }

        $usuario->update(['password' => $this->nueva]);
        $this->reset(['actual', 'nueva', 'nueva_confirmation']);

        Notification::make()->title('Contraseña actualizada')->success()->send();
    }
}
