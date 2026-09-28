@extends('publico.base')

@section('titulo', 'Blog · Reflexiones sobre salud mental')
@section('descripcion', 'Artículos del blog de Consulta Demo: ideas prácticas sobre ansiedad, autoestima, hábitos y bienestar emocional.')

@section('contenido')
<div class="max-w-5xl mx-auto px-4 py-10">
    <h1 class="font-serif text-3xl md:text-4xl text-teal-950">Blog</h1>
    <p class="mt-3 text-stone-600">Reflexiones e ideas prácticas que también trabajo en consulta.</p>

    <div class="mt-8 space-y-6">
        @foreach ($posts as $post)
            <article class="flex gap-4 items-start bg-white border border-stone-200 rounded-xl p-4">
                @if ($post->imagen_url)
                    <img src="{{ $post->imagen_url }}" alt="" width="240" height="180" class="w-28 h-20 md:w-48 md:h-36 object-cover rounded-lg border border-stone-200 shrink-0">
                @endif
                <div>
                    <p class="text-xs uppercase tracking-wide text-stone-500">{{ $post->publicado_en?->format('d/m/Y') }}</p>
                    <h2 class="font-serif text-xl text-teal-950 mt-1"><a href="{{ route('blog.detalle', $post) }}" class="hover:underline">{{ $post->titulo }}</a></h2>
                    @if ($post->extracto)
                        <p class="text-sm text-stone-600 mt-1">{{ $post->extracto }}</p>
                    @endif
                </div>
            </article>
        @endforeach
    </div>

    <div class="mt-8">{{ $posts->links() }}</div>
</div>
@endsection
