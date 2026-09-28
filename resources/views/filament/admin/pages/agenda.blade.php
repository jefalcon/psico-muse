<x-filament-panels::page>
    <style>
        .agenda-bar { display: flex; flex-wrap: wrap; gap: .5rem; align-items: center; justify-content: space-between; margin-bottom: 1rem; }
        .agenda-btns { display: flex; flex-wrap: wrap; gap: .5rem; align-items: center; }
        .agenda-btn { display: inline-block; padding: .45rem .9rem; border-radius: .5rem; border: 1px solid #d1d5db; background: #fff; font-size: .85rem; cursor: pointer; }
        .agenda-btn.activo { background: #0f766e; border-color: #0f766e; color: #fff; }
        .agenda-titulo { font-size: 1.05rem; font-weight: 600; }
        .agenda-aviso { margin-bottom: 1rem; padding: .7rem 1rem; border-radius: .5rem; background: #fef3c7; border: 1px solid #f59e0b; font-size: .9rem; }
        .agenda-grid7 { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); gap: .5rem; }
        .agenda-dia { border: 1px solid #e5e7eb; border-radius: .5rem; padding: .4rem; min-height: 5.5rem; background: #fff; }
        .agenda-dia.hoy { border-color: #0f766e; box-shadow: 0 0 0 1px #0f766e; }
        .agenda-dia.otro-mes { background: #f9fafb; color: #9ca3af; }
        .agenda-dia-num { font-size: .75rem; font-weight: 700; margin-bottom: .25rem; }
        .agenda-cita { display: block; font-size: .72rem; line-height: 1.25; padding: .25rem .35rem; border-radius: .35rem; margin-bottom: .25rem; border-left: 4px solid #9ca3af; background: #f3f4f6; color: #111827; text-decoration: none; }
        .agenda-cita.solicitada { background: #fef3c7; border-color: #f59e0b; font-weight: 600; }
        .agenda-cita.confirmada { background: #d1fae5; border-color: #10b981; }
        .agenda-cita.completada { background: #e0e7ff; border-color: #6366f1; }
        .agenda-cita.rechazada, .agenda-cita.cancelada { background: #fee2e2; border-color: #ef4444; text-decoration: line-through; }
        .agenda-leyenda { display: flex; flex-wrap: wrap; gap: .75rem; margin-top: 1rem; font-size: .8rem; }
        .agenda-leyenda span { display: inline-flex; align-items: center; gap: .3rem; }
        .agenda-punto { width: .8rem; height: .8rem; border-radius: .2rem; display: inline-block; }
        @media (max-width: 900px) { .agenda-grid7 { grid-template-columns: 1fr; } .agenda-dia { min-height: auto; } }
    </style>

    <div class="agenda-bar">
        <div class="agenda-btns">
            <button type="button" wire:click="anterior" class="agenda-btn">← Anterior</button>
            <button type="button" wire:click="hoy" class="agenda-btn">Hoy</button>
            <button type="button" wire:click="siguiente" class="agenda-btn">Siguiente →</button>
        </div>
        <div class="agenda-btns">
            <button type="button" wire:click="cambiarModo('semana')" class="agenda-btn {{ $this->modo === 'semana' ? 'activo' : '' }}">Semana</button>
            <button type="button" wire:click="cambiarModo('mes')" class="agenda-btn {{ $this->modo === 'mes' ? 'activo' : '' }}">Mes</button>
        </div>
    </div>

    @php
        $rango = $this->rango();
        $citas = $this->citas();
        $porDia = $citas->groupBy(fn ($c) => $c->inicio->toDateString());
        $pendientes = $citas->where('estado', 'solicitada')->count();
    @endphp

    <p class="agenda-titulo">
        {{ $rango['inicio']->format('d/m/Y') }} — {{ $rango['fin']->format('d/m/Y') }}
    </p>

    @if ($pendientes > 0)
        <div class="agenda-aviso">
            Hay {{ $pendientes }} {{ Str::plural('solicitud pendiente', $pendientes) }} en este periodo
            ({{ $this->pendientes() }} en total).
        </div>
    @endif

    <div class="agenda-grid7">
        @foreach (['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'] as $nombre)
            <div class="agenda-dia-num" style="text-align:center">{{ $nombre }}</div>
        @endforeach
        @for ($dia = $rango['inicio']->copy()->startOfDay(); $dia->lte($rango['fin']); $dia->addDay())
            @php
                $clave = $dia->toDateString();
                $esHoy = $clave === today()->toDateString();
                $otroMes = $this->modo === 'mes' && $dia->month !== \Carbon\Carbon::parse($this->referencia)->month;
            @endphp
            <div class="agenda-dia {{ $esHoy ? 'hoy' : '' }} {{ $otroMes ? 'otro-mes' : '' }}">
                <div class="agenda-dia-num">{{ $dia->format('j') }}</div>
                @foreach ($porDia->get($clave, collect()) as $cita)
                    <a class="agenda-cita {{ $cita->estado }}" href="{{ \App\Filament\Admin\Pages\Agenda::urlCita($cita) }}">
                        {{ $cita->inicio->format('H:i') }} · {{ $cita->cliente->user?->name }}
                        <br>{{ $cita->servicio->nombre }} ({{ $cita->estadoEtiqueta() }})
                    </a>
                @endforeach
            </div>
        @endfor
    </div>

    <div class="agenda-leyenda">
        <span><span class="agenda-punto" style="background:#f59e0b"></span> Solicitada</span>
        <span><span class="agenda-punto" style="background:#10b981"></span> Confirmada</span>
        <span><span class="agenda-punto" style="background:#6366f1"></span> Completada</span>
        <span><span class="agenda-punto" style="background:#ef4444"></span> Rechazada / cancelada</span>
    </div>
</x-filament-panels::page>
