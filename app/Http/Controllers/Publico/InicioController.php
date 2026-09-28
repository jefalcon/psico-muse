<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Models\Articulo;
use App\Models\Post;

class InicioController extends Controller
{
    public function __invoke()
    {
        $posts = Post::publicados()->latest('publicado_en')->take(3)->get();
        $articulos = Articulo::publicados()->latest('publicado_en')->take(3)->get();

        $publicaciones = $posts->map(fn (Post $p) => [
            'tipo' => 'blog',
            'titulo' => $p->titulo,
            'extracto' => $p->extracto,
            'imagen' => $p->imagen_url,
            'url' => route('blog.detalle', $p),
            'fecha' => $p->publicado_en,
        ])->concat($articulos->map(fn (Articulo $a) => [
            'tipo' => 'articulo',
            'titulo' => $a->titulo,
            'extracto' => $a->extracto,
            'imagen' => $a->imagen_url,
            'url' => route('articulos.detalle', $a),
            'fecha' => $a->publicado_en,
        ]))->sortByDesc('fecha')->take(3)->values();

        return view('publico.inicio', compact('publicaciones'));
    }

    public function biografia()
    {
        return view('publico.biografia');
    }
}
