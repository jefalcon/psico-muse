@extends('publico.base')

@section('titulo', $articulo->titulo.' · Artículos de salud')
@section('descripcion', $articulo->extracto ?: 'Artículo divulgativo de Consulta Demo.')

@section('contenido')
<article class="max-w-3xl mx-auto px-4 py-10">
    <p class="text-sm text-stone-500"><a href="{{ route('articulos.indice') }}" class="underline">Artículos de salud</a> · {{ $articulo->categoriaEtiqueta() }} · {{ $articulo->publicado_en?->format('d/m/Y') }}</p>
    <h1 class="font-serif text-3xl md:text-4xl text-teal-950 mt-2">{{ $articulo->titulo }}</h1>
    @if ($articulo->extracto)
        <p class="mt-3 text-lg text-stone-600">{{ $articulo->extracto }}</p>
    @endif
    @if ($articulo->imagen_url)
        <img src="{{ $articulo->imagen_url }}" alt="" width="800" height="600" class="mt-6 w-full rounded-xl border border-stone-200">
    @endif
    <div class="mt-6 text-[16px] leading-relaxed text-stone-700 space-y-4 contenido-enriquecido">{!! $articulo->cuerpo !!}</div>
    <aside class="mt-8 bg-amber-50 border border-amber-300 rounded-xl p-4 text-sm text-stone-700">
        <p><strong>Aviso:</strong> esta información es divulgativa y no sustituye una consulta profesional. Si te preocupa tu salud mental, pide cita con un profesional sanitario.</p>
    </aside>
    <p class="mt-8"><a href="{{ route('articulos.indice') }}" class="text-teal-800 underline">← Volver a artículos</a></p>
</article>
@endsection
