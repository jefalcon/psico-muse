<x-filament-panels::page>
    <style>
        .mh-entrada { background: #fff; border: 1px solid #e5e7eb; border-radius: .75rem; padding: 1rem 1.25rem; margin-bottom: 1rem; }
        .mh-entrada h3 { margin: 0 0 .5rem; font-size: 1rem; }
        .mh-bloque { margin-bottom: .6rem; font-size: .9rem; }
        .mh-bloque strong { display: block; font-size: .8rem; color: #4b5563; margin-bottom: .15rem; }
        .mh-adj a { display: inline-block; margin-right: .75rem; font-size: .85rem; }
    </style>

    @forelse ($this->entradas() as $entrada)
        <div class="mh-entrada">
            <h3>Sesión del {{ $entrada->fecha->format('d/m/Y') }}</h3>
            <div class="mh-bloque"><strong>Motivo de consulta</strong>{{ $entrada->motivo }}</div>
            @if ($entrada->notas_sesion)
                <div class="mh-bloque"><strong>Notas compartidas</strong>{{ $entrada->notas_sesion }}</div>
            @endif
            @if ($entrada->plan_terapeutico)
                <div class="mh-bloque"><strong>Plan terapéutico</strong>{{ $entrada->plan_terapeutico }}</div>
            @endif
            @if ($entrada->adjuntos->isNotEmpty())
                <div class="mh-bloque mh-adj">
                    <strong>Documentos</strong>
                    @foreach ($entrada->adjuntos as $adjunto)
                        <a href="{{ route('adjuntos.descargar', $adjunto) }}">{{ $adjunto->nombre_original ?? 'Descargar documento' }}</a>
                    @endforeach
                </div>
            @endif
        </div>
    @empty
        <p style="font-size:.9rem;color:#4b5563">Todavía no hay entradas compartidas en tu historial.</p>
    @endforelse
</x-filament-panels::page>
