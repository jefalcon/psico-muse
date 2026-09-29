<?php

namespace App\Filament\Concerns;

use Illuminate\Validation\ValidationException;

/**
 * Lanza errores de validación visibles en los campos del formulario.
 *
 * Los formularios de Filament guardan su estado bajo la clave «data», así
 * que los mensajes deben usar claves «data.campo»; con la clave sin
 * prefijo el error no se muestra en la interfaz.
 */
trait LanzaErroresDeFormulario
{
    protected static function errorDeFormulario(string $campo, string $mensaje): never
    {
        throw ValidationException::withMessages(['data.'.$campo => $mensaje]);
    }
}
