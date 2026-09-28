<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;

class LegalController extends Controller
{
    public function aviso()
    {
        return view('publico.aviso-legal');
    }

    public function privacidad()
    {
        return view('publico.privacidad');
    }
}
