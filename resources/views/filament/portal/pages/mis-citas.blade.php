<x-filament-panels::page>
    <style>
        .mc-tabla { width: 100%; border-collapse: collapse; font-size: .9rem; margin-bottom: 2rem; background: #fff; }
        .mc-tabla th, .mc-tabla td { border: 1px solid #e5e7eb; padding: .5rem .6rem; text-align: left; vertical-align: top; }
        .mc-tabla th { background: #f3f4f6; }
        .mc-form { background: #fff; border: 1px solid #e5e7eb; border-radius: .75rem; padding: 1.25rem; max-width: 640px; }
        .mc-campo { margin-bottom: .9rem; }
        .mc-campo label { display: block; font-size: .85rem; font-weight: 600; margin-bottom: .25rem; }
        .mc-campo select, .mc-campo input, .mc-campo textarea { width: 100%; border: 1px solid #d1d5db; border-radius: .5rem; padding: .5rem .6rem; font-size: .9rem; }
        .mc-error { color: #b91c1c; font-size: .8rem; margin-top: .2rem; }
        .mc-btn { display: inline-block; padding: .5rem 1rem; border-radius: .5rem; background: #059669; color: #fff; border: none; cursor: pointer; font-size: .9rem; }
        .mc-btn-sec { background: #fff; color: #111827; border: 1px solid #d1d5db; }
        .mc-btn-danger { background: #dc2626; }
        .mc-estado { display: inline-block; padding: .1rem .5rem; border-radius: 999px; font-size: .75rem; background: #e5e7eb; }
        .mc-estado.solicitada { background: #fef3c7; }
        .mc-estado.confirmada { background: #d1fae5; }
        .mc-estado.rechazada, .mc-estado.cancelada { background: #fee2e2; }
        .mc-estado.completada { background: #e0e7ff; }
        .mc-motivo { font-size: .8rem; color: #b91c1c; }
        .mc-grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: .9rem; }
        @media (max-width: 640px) { .mc-grid2 { grid-template-columns: 1fr; } .mc-wrap { overflow-x: auto; } }
    </style>

    <h2 style="font-size:1rem;margin:0 0 .5rem">Tus citas</h2>
    @if ($this->citas()->isEmpty())
        <p style="font-size:.9rem;color:#4b5563">Aún no tienes citas. Solicita la primera con el formulario de abajo.</p>
    @else
        <div class="mc-wrap">
        <table class="mc-tabla">
            <thead>
                <tr><th>Fecha</th><th>Servicio</th><th>Estado</th><th>Acciones</th></tr>
            </thead>
            <tbody>
                @foreach ($this->citas() as $cita)
                    <tr>
                        <td>{{ $cita->inicio->format('d/m/Y H:i') }} – {{ $cita->fin->format('H:i') }}</td>
                        <td>{{ $cita->servicio->nombre }}</td>
                        <td>
                            <span class="mc-estado {{ $cita->estado }}">{{ $cita->estadoEtiqueta() }}</span>
                            @if ($cita->estado === 'rechazada' && $cita->motivo_rechazo)
                                <div class="mc-motivo">Motivo: {{ $cita->motivo_rechazo }}</div>
                            @endif
                        </td>
                        <td>
                            @if ($cita->puedeCancelarCliente())
                                <button type="button" class="mc-btn mc-btn-danger" wire:click="cancelar({{ $cita->id }})" wire:confirm="¿Seguro que quieres cancelar esta cita?">Cancelar</button>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    @endif

    <h2 style="font-size:1rem;margin:0 0 .5rem">Solicitar una cita</h2>
    <form wire:submit="solicitar" class="mc-form">
        <div class="mc-campo">
            <label for="mc-servicio">Servicio</label>
            <select id="mc-servicio" wire:model.live="servicio_id">
                <option value="">— Elige un servicio —</option>
                @foreach ($this->servicios() as $servicio)
                    <option value="{{ $servicio->id }}">{{ $servicio->nombre }} ({{ $servicio->duracion_minutos }} min{{ $servicio->precioFormateado() ? ' · '.$servicio->precioFormateado() : '' }})</option>
                @endforeach
            </select>
            @error('servicio_id') <div class="mc-error">{{ $message }}</div> @enderror
        </div>
        <div class="mc-grid2">
            <div class="mc-campo">
                <label for="mc-fecha">Fecha (mínimo 24 h de antelación)</label>
                <input id="mc-fecha" type="date" wire:model.live="fecha" min="{{ today()->addDay()->toDateString() }}">
                @error('fecha') <div class="mc-error">{{ $message }}</div> @enderror
            </div>
            <div class="mc-campo">
                <label for="mc-hora">Hora libre</label>
                <select id="mc-hora" wire:model="hora">
                    <option value="">— Elige hora —</option>
                    @foreach ($this->huecos as $hueco)
                        <option value="{{ $hueco }}">{{ $hueco }}</option>
                    @endforeach
                </select>
                @if ($this->servicio_id && $this->fecha !== '' && empty($this->huecos))
                    <div class="mc-error">No hay huecos libres ese día para este servicio.</div>
                @endif
                @error('hora') <div class="mc-error">{{ $message }}</div> @enderror
            </div>
        </div>
        <div class="mc-campo">
            <label for="mc-comentario">Comentario (opcional)</label>
            <textarea id="mc-comentario" wire:model="comentario" rows="3"></textarea>
            @error('comentario') <div class="mc-error">{{ $message }}</div> @enderror
        </div>
        <button type="submit" class="mc-btn">Solicitar cita</button>
    </form>
</x-filament-panels::page>
