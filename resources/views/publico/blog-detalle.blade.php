@extends('publico.base')

@section('titulo', $post->titulo.' · Blog')
@section('descripcion', $post->extracto ?: 'Entrada del blog de Consulta Demo.')

@section('contenido')
<article class="max-w-3xl mx-auto px-4 py-10">
    <p class="text-sm text-stone-500"><a href="{{ route('blog.indice') }}" class="underline">Blog</a> · {{ $post->publicado_en?->format('d/m/Y') }}</p>
    <h1 class="font-serif text-3xl md:text-4xl text-teal-950 mt-2">{{ $post->titulo }}</h1>
    @if ($post->extracto)
        <p class="mt-3 text-lg text-stone-600">{{ $post->extracto }}</p>
    @endif
    @if ($post->imagen_url)
        <img src="{{ $post->imagen_url }}" alt="" width="800" height="600" class="mt-6 w-full rounded-xl border border-stone-200">
    @endif
    <div class="mt-6 text-[16px] leading-relaxed text-stone-700 space-y-4 contenido-enriquecido">{!! $post->cuerpo !!}</div>
    <p class="mt-8"><a href="{{ route('blog.indice') }}" class="text-teal-800 underline">← Volver al blog</a></p>
</article>
@endsection
