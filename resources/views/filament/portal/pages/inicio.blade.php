<x-filament-panels::page>
    <style>
        .p-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
        .p-card { border: 1px solid #e5e7eb; border-radius: .75rem; padding: 1rem 1.25rem; background: #fff; text-decoration: none; color: inherit; display: block; }
        a.p-card:hover { border-color: #059669; }
        .p-card h3 { margin: 0 0 .25rem; font-size: 1rem; }
        .p-card p { margin: 0; font-size: .85rem; color: #4b5563; }
        .p-sec { margin-bottom: 1.5rem; }
        .p-sec h2 { font-size: 1rem; margin: 0 0 .5rem; }
        .p-item { border: 1px solid #e5e7eb; border-radius: .5rem; padding: .6rem .8rem; margin-bottom: .5rem; background: #fff; font-size: .9rem; }
        .p-vacio { font-size: .9rem; color: #4b5563; }
    </style>

    <div class="p-grid">
        <a class="p-card" href="{{ \App\Filament\Portal\Pages\MisCitas::getUrl() }}">
            <h3>Mis citas</h3>
            <p>Consulta tus citas y solicita una nueva.</p>
        </a>
        <a class="p-card" href="{{ \App\Filament\Portal\Pages\Mensajes::getUrl() }}">
            <h3>Mensajes</h3>
            <p>Habla con tu psicóloga.</p>
        </a>
        <a class="p-card" href="{{ \App\Filament\Portal\Pages\MiHistorial::getUrl() }}">
            <h3>Mi historial</h3>
            <p>Documentos y resúmenes compartidos.</p>
        </a>
        <a class="p-card" href="{{ \App\Filament\Portal\Pages\MiPerfil::getUrl() }}">
            <h3>Mi perfil</h3>
            <p>Actualiza tu teléfono y contraseña.</p>
        </a>
    </div>

    <div class="p-sec">
        <h2>Próximas citas</h2>
        @forelse ($this->proximasCitas() as $cita)
            <div class="p-item">{{ $cita->inicio->format('d/m/Y H:i') }} · {{ $cita->servicio->nombre }} · {{ $cita->estadoEtiqueta() }}</div>
        @empty
            <p class="p-vacio">No tienes próximas citas. Puedes solicitar una desde «Mis citas».</p>
        @endforelse
    </div>

    <div class="p-sec">
        <h2>Mensajes sin leer</h2>
        @forelse ($this->hilosSinLeer() as $hilo)
            <div class="p-item">
                <a href="{{ \App\Filament\Portal\Pages\Mensajes::getUrl(['hilo' => $hilo->id]) }}">{{ $hilo->asunto }}</a>
                ({{ $hilo->sinLeerPara(auth()->user()) }} sin leer)
            </div>
        @empty
            <p class="p-vacio">Estás al día: no hay mensajes sin leer.</p>
        @endforelse
    </div>
</x-filament-panels::page>
