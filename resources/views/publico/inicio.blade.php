@extends('publico.base')

@section('titulo', 'Consulta Demo · Psicóloga sanitaria en Madrid y online')
@section('descripcion', 'Terapia psicológica para ansiedad, depresión, duelo, autoestima y estrés. Primera entrevista informativa gratuita. Presencial en Madrid y online.')

@section('contenido')
<section class="max-w-5xl mx-auto px-4 pt-10 pb-6 grid gap-8 md:grid-cols-2 md:items-center">
    <div>
        <p class="text-teal-800 font-medium">Elena Márquez · Psicóloga sanitaria · Colegiada M-28417</p>
        <h1 class="font-serif text-4xl md:text-5xl text-teal-950 mt-3 leading-tight">Un espacio seguro para entenderte y sentirte mejor</h1>
        <p class="mt-4 text-lg text-stone-600">Acompaño a personas adultas que atraviesan ansiedad, depresión, duelo o momentos de cambio. Terapia basada en la evidencia, a tu ritmo y sin juicios.</p>
        <p class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('contacto.formulario') }}" class="inline-block bg-teal-800 text-white px-6 py-3 rounded-lg hover:bg-teal-900">Pedir primera entrevista</a>
            <a href="{{ route('servicios') }}" class="inline-block border border-teal-800 text-teal-900 px-6 py-3 rounded-lg hover:bg-teal-50">Ver servicios</a>
        </p>
    </div>
    <div>
        <img src="{{ asset('images/retrato.svg') }}" alt="Retrato ilustrado de Elena Márquez, psicóloga sanitaria" width="600" height="700" class="w-full max-w-sm mx-auto rounded-2xl border border-stone-200">
    </div>
</section>

<section class="max-w-5xl mx-auto px-4 py-8">
    <h2 class="font-serif text-2xl text-teal-950">Cómo trabajo</h2>
    <div class="mt-4 grid gap-6 md:grid-cols-3 text-[15px] leading-relaxed">
        <div>
            <h3 class="font-semibold text-teal-900">Primera entrevista gratuita</h3>
            <p class="mt-1 text-stone-600">30 minutos para conocernos, entender tu motivo de consulta y valorar si puedo ayudarte. Sin compromiso.</p>
        </div>
        <div>
            <h3 class="font-semibold text-teal-900">Plan a tu medida</h3>
            <p class="mt-1 text-stone-600">Definimos juntos objetivos claros y revisamos el progreso cada pocas sesiones.</p>
        </div>
        <div>
            <h3 class="font-semibold text-teal-900">Terapia basada en la evidencia</h3>
            <p class="mt-1 text-stone-600">Integro terapia cognitivo-conductual, EMDR y enfoques humanistas según lo que necesites.</p>
        </div>
    </div>
</section>

<section class="bg-white border-y border-stone-200">
    <div class="max-w-5xl mx-auto px-4 py-10">
        <h2 class="font-serif text-2xl text-teal-950">Últimas publicaciones</h2>
        <div class="mt-5 space-y-5">
            @foreach ($publicaciones as $pub)
                <article class="flex gap-4 items-start">
                    @if ($pub['imagen'])
                        <img src="{{ $pub['imagen'] }}" alt="" width="160" height="120" class="w-28 h-20 md:w-40 md:h-28 object-cover rounded-lg border border-stone-200 shrink-0">
                    @endif
                    <div>
                        <p class="text-xs uppercase tracking-wide text-stone-500">{{ $pub['tipo'] === 'blog' ? 'Blog' : 'Artículo de salud' }} · {{ $pub['fecha']?->format('d/m/Y') }}</p>
                        <h3 class="font-serif text-lg text-teal-950 mt-1"><a href="{{ $pub['url'] }}" class="hover:underline">{{ $pub['titulo'] }}</a></h3>
                        @if ($pub['extracto'])
                            <p class="text-sm text-stone-600 mt-1">{{ $pub['extracto'] }}</p>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
        <p class="mt-6 flex flex-wrap gap-4 text-[15px]">
            <a href="{{ route('blog.indice') }}" class="text-teal-800 underline">Ver todo el blog</a>
            <a href="{{ route('articulos.indice') }}" class="text-teal-800 underline">Ver artículos de salud</a>
        </p>
    </div>
</section>

<section class="max-w-5xl mx-auto px-4 py-10 grid gap-8 md:grid-cols-2">
    <div>
        <h2 class="font-serif text-2xl text-teal-950">Sobre mí</h2>
        <p class="mt-3 text-stone-600 leading-relaxed">Soy Elena Márquez, psicóloga sanitaria con más de 12 años de experiencia en consulta privada y en recursos públicos de salud mental. Creo en una terapia honesta: te explicaré siempre qué estamos haciendo y por qué.</p>
        <p class="mt-4"><a href="{{ route('biografia') }}" class="text-teal-800 underline">Conoce mi formación y enfoque</a></p>
    </div>
    <div>
        <h2 class="font-serif text-2xl text-teal-950">Contacto</h2>
        <p class="mt-3 text-stone-600 leading-relaxed">Escríbeme y te respondo en menos de 24 horas laborables. También puedes usar el formulario para solicitar tu primera entrevista gratuita.</p>
        <p class="mt-4"><a href="{{ route('contacto.formulario') }}" class="text-teal-800 underline">Ir al formulario de contacto</a></p>
    </div>
</section>
@endsection
