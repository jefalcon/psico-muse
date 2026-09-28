<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Models\Articulo;
use App\Models\Post;

class SitemapController extends Controller
{
    public function __invoke()
    {
        $urls = [
            route('inicio'),
            route('biografia'),
            route('servicios'),
            route('blog.indice'),
            route('articulos.indice'),
            route('contacto.formulario'),
            route('legal.aviso'),
            route('legal.privacidad'),
        ];

        $posts = Post::publicados()->latest('publicado_en')->get();
        $articulos = Articulo::publicados()->latest('publicado_en')->get();

        return response()
            ->view('publico.sitemap', compact('urls', 'posts', 'articulos'))
            ->header('Content-Type', 'application/xml');
    }
}
