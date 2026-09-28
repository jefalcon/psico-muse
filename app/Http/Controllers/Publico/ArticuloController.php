<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Models\Articulo;
use Illuminate\Http\Request;

class ArticuloController extends Controller
{
    public function indice(Request $request)
    {
        $categoria = $request->query('categoria');
        $query = Articulo::publicados()->latest('publicado_en');

        if (in_array($categoria, [Articulo::CATEGORIA_TRASTORNO, Articulo::CATEGORIA_TRATAMIENTO], true)) {
            $query->where('categoria', $categoria);
        } else {
            $categoria = null;
        }

        $articulos = $query->paginate(10)->withQueryString();

        return view('publico.articulos-indice', compact('articulos', 'categoria'));
    }

    public function detalle(string $articulo)
    {
        $articulo = Articulo::publicados()->where('slug', $articulo)->firstOrFail();

        return view('publico.articulos-detalle', compact('articulo'));
    }
}
