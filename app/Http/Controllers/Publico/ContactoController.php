<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Models\Interaccion;
use App\Models\Lead;
use App\Models\Servicio;
use Illuminate\Http\Request;

class ContactoController extends Controller
{
    public function formulario()
    {
        $servicios = Servicio::activos()->get();

        return view('publico.contacto', compact('servicios'));
    }

    public function enviar(Request $request)
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'servicio_id' => ['nullable', 'exists:servicios,id'],
            'mensaje' => ['required', 'string', 'max:5000'],
            'consentimiento' => ['accepted'],
        ], [
            'consentimiento.accepted' => 'Debes aceptar la política de privacidad.',
        ]);

        $lead = Lead::where('email', $datos['email'])->first();

        if ($lead) {
            Interaccion::create([
                'lead_id' => $lead->id,
                'fecha' => now(),
                'canal' => Interaccion::CANAL_WEB,
                'nota' => 'Nuevo mensaje desde el formulario web: '.mb_substr($datos['mensaje'], 0, 1000),
            ]);
        } else {
            Lead::create([
                'nombre' => $datos['nombre'],
                'email' => $datos['email'],
                'telefono' => $datos['telefono'] ?? null,
                'servicio_id' => $datos['servicio_id'] ?? null,
                'mensaje' => $datos['mensaje'],
                'consentimiento' => true,
                'estado' => Lead::ESTADO_NUEVO,
                'origen' => Lead::ORIGEN_WEB,
            ]);
        }

        return redirect()->route('contacto.formulario')->with('enviado', true);
    }
}
