<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Models\Servicio;

class ServicioController extends Controller
{
    public function __invoke()
    {
        $servicios = Servicio::activos()->get();

        return view('publico.servicios', compact('servicios'));
    }
}
