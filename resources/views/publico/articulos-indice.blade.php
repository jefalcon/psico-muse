@extends('publico.base')

@section('titulo', 'Artículos de salud · Trastornos y tratamientos')
@section('descripcion', 'Artículos divulgativos sobre trastornos (ansiedad, depresión, duelo) y tratamientos (terapia cognitivo-conductual, EMDR).')

@section('contenido')
<div class="max-w-5xl mx-auto px-4 py-10">
    <h1 class="font-serif text-3xl md:text-4xl text-teal-950">Artículos de salud</h1>
    <p class="mt-3 text-stone-600">Información rigurosa y divulgativa sobre trastornos frecuentes y tratamientos psicológicos.</p>

    <nav class="mt-6" aria-label="Filtrar por categoría">
        <ul class="flex flex-wrap gap-2 text-sm">
            <li><a href="{{ route('articulos.indice') }}" class="inline-block px-4 py-2 rounded-full border {{ $categoria ? 'border-stone-300 hover:border-teal-700' : 'bg-teal-800 text-white border-teal-800' }}">Todos</a></li>
            <li><a href="{{ route('articulos.indice', ['categoria' => 'trastorno']) }}" class="inline-block px-4 py-2 rounded-full border {{ $categoria === 'trastorno' ? 'bg-teal-800 text-white border-teal-800' : 'border-stone-300 hover:border-teal-700' }}">Trastornos</a></li>
            <li><a href="{{ route('articulos.indice', ['categoria' => 'tratamiento']) }}" class="inline-block px-4 py-2 rounded-full border {{ $categoria === 'tratamiento' ? 'bg-teal-800 text-white border-teal-800' : 'border-stone-300 hover:border-teal-700' }}">Tratamientos</a></li>
        </ul>
    </nav>

    <div class="mt-8 space-y-6">
        @forelse ($articulos as $articulo)
            <article class="flex gap-4 items-start bg-white border border-stone-200 rounded-xl p-4">
                @if ($articulo->imagen_url)
                    <img src="{{ $articulo->imagen_url }}" alt="" width="240" height="180" class="w-28 h-20 md:w-48 md:h-36 object-cover rounded-lg border border-stone-200 shrink-0">
                @endif
                <div>
                    <p class="text-xs uppercase tracking-wide text-stone-500">{{ $articulo->categoriaEtiqueta() }} · {{ $articulo->publicado_en?->format('d/m/Y') }}</p>
                    <h2 class="font-serif text-xl text-teal-950 mt-1"><a href="{{ route('articulos.detalle', $articulo) }}" class="hover:underline">{{ $articulo->titulo }}</a></h2>
                    @if ($articulo->extracto)
                        <p class="text-sm text-stone-600 mt-1">{{ $articulo->extracto }}</p>
                    @endif
                </div>
            </article>
        @empty
            <p class="text-stone-600">No hay artículos en esta categoría todavía.</p>
        @endforelse
    </div>

    <div class="mt-8">{{ $articulos->links() }}</div>
</div>
@endsection
