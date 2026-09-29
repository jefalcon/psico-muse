<?php

namespace App\Http\Controllers;

use App\Models\Adjunto;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdjuntoController extends Controller
{
    public function descargar(Adjunto $adjunto): StreamedResponse
    {
        $usuario = auth()->user();
        $entrada = $adjunto->entrada;

        abort_if($usuario === null, 403);

        if ($usuario->isCliente()) {
            $esPropio = $usuario->cliente && $entrada->cliente_id === $usuario->cliente->id;
            abort_unless($esPropio && $entrada->visible_cliente, 403);
        } else {
            abort_unless($usuario->isAdmin(), 403);
        }

        abort_unless(Storage::disk('privado')->exists($adjunto->ruta), 404);

        return Storage::disk('privado')->download($adjunto->ruta, $adjunto->nombre_original);
    }
}
