@extends('publico.base')

@section('titulo', 'Servicios · Terapia psicológica presencial y online')
@section('descripcion', 'Servicios de psicología sanitaria: terapia individual, ansiedad y estrés, depresión, duelo, terapia online y primera entrevista gratuita.')

@section('contenido')
<div class="max-w-5xl mx-auto px-4 py-10">
    <h1 class="font-serif text-3xl md:text-4xl text-teal-950">Servicios</h1>
    <p class="mt-3 text-stone-600 max-w-2xl">Todas las modalidades incluyen seguimiento entre sesiones por mensaje cuando lo necesitas y revisión de objetivos cada seis sesiones.</p>

    <div class="mt-8 space-y-6">
        @foreach ($servicios as $servicio)
            <article class="bg-white border border-stone-200 rounded-xl p-6">
                <div class="flex flex-wrap items-baseline gap-x-4 gap-y-1">
                    <h2 class="font-serif text-xl text-teal-950">{{ $servicio->nombre }}</h2>
                    <p class="text-sm text-stone-500">{{ $servicio->duracion_minutos }} minutos
                        @if ($servicio->precioFormateado()) · {{ $servicio->precioFormateado() }}@endif
                    </p>
                </div>
                <p class="mt-2 text-[15px] text-stone-600 leading-relaxed">{{ $servicio->descripcion }}</p>
            </article>
        @endforeach
    </div>

    <div class="mt-10 bg-teal-50 border border-teal-200 rounded-xl p-6">
        <h2 class="font-serif text-xl text-teal-950">¿No sabes qué servicio necesitas?</h2>
        <p class="mt-2 text-stone-600">La primera entrevista informativa (30 minutos) es gratuita y sin compromiso. Cuéntame qué te preocupa y te orientaré.</p>
        <p class="mt-4"><a href="{{ route('contacto.formulario') }}" class="inline-block bg-teal-800 text-white px-6 py-3 rounded-lg hover:bg-teal-900">Contactar</a></p>
    </div>
</div>
@endsection
