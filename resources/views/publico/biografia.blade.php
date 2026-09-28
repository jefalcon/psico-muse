@extends('publico.base')

@section('titulo', 'Biografía · Elena Márquez, psicóloga sanitaria')
@section('descripcion', 'Formación, experiencia y enfoque terapéutico de Elena Márquez, psicóloga sanitaria colegiada M-28417 con más de 12 años de experiencia.')

@section('contenido')
<article class="max-w-5xl mx-auto px-4 py-10">
    <h1 class="font-serif text-3xl md:text-4xl text-teal-950">Elena Márquez</h1>
    <p class="mt-2 text-stone-600">Psicóloga sanitaria · Colegiada M-28417 por el Colegio Oficial de la Psicología de Madrid</p>

    <div class="mt-8 grid gap-8 md:grid-cols-3">
        <div>
            <img src="{{ asset('images/retrato.svg') }}" alt="Retrato ilustrado de Elena Márquez" width="600" height="700" class="w-full max-w-xs rounded-2xl border border-stone-200">
        </div>
        <div class="md:col-span-2 space-y-8 text-[15px] leading-relaxed text-stone-700">
            <section>
                <h2 class="font-serif text-2xl text-teal-950">Formación</h2>
                <ul class="mt-3 space-y-2 list-disc pl-5">
                    <li>Licenciatura en Psicología por la Universidad Complutense de Madrid (2009).</li>
                    <li>Máster en Psicología General Sanitaria por la Universidad Autónoma de Madrid (2011).</li>
                    <li>Máster en Terapia Cognitivo-Conductual por el Centro de Psicología Bertrand Russell (2013).</li>
                    <li>Acreditación como terapeuta EMDR por la Asociación EMDR España (2017, niveles I y II).</li>
                    <li>Formación continuada en duelo (IPIR Barcelona, 2019) y trauma complejo (2021).</li>
                </ul>
            </section>
            <section>
                <h2 class="font-serif text-2xl text-teal-950">Experiencia</h2>
                <p class="mt-3">Llevo más de 12 años ejerciendo como psicóloga. Durante seis años trabajé en un centro de salud mental del Servicio Madrileño de Salud, donde atendí a personas con trastornos de ansiedad, depresión y trastornos adaptativos. Desde 2018 me dedico en exclusiva a mi consulta privada, presencial en el barrio de Chamberí y online para toda España.</p>
                <p class="mt-3">Además de la clínica, colaboro como docente en cursos de posgrado sobre intervención en duelo y superviso a psicólogas en formación.</p>
            </section>
            <section>
                <h2 class="font-serif text-2xl text-teal-950">Enfoque terapéutico</h2>
                <p class="mt-3">Mi base es la terapia cognitivo-conductual, el enfoque con más evidencia científica para la ansiedad y la depresión. La integro con EMDR cuando hay recuerdos difíciles que siguen doliendo y con herramientas humanistas —escucha, validación, trabajo con valores— porque ninguna persona cabe entera en un protocolo.</p>
                <p class="mt-3">En la primera entrevista te explicaré con claridad cómo entiendo lo que te pasa y qué camino te propongo. Si creo que otro profesional puede ayudarte mejor, te lo diré y te orientaré.</p>
            </section>
            <p><a href="{{ route('contacto.formulario') }}" class="inline-block bg-teal-800 text-white px-6 py-3 rounded-lg hover:bg-teal-900">Solicitar primera entrevista gratuita</a></p>
        </div>
    </div>
</article>
@endsection
