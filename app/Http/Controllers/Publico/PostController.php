<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Models\Post;

class PostController extends Controller
{
    public function indice()
    {
        $posts = Post::publicados()->latest('publicado_en')->paginate(10);

        return view('publico.blog-indice', compact('posts'));
    }

    public function detalle(string $post)
    {
        $post = Post::publicados()->where('slug', $post)->firstOrFail();

        return view('publico.blog-detalle', compact('post'));
    }
}
