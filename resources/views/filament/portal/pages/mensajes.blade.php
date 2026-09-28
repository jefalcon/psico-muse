<x-filament-panels::page>
    <style>
        .mj-cols { display: grid; grid-template-columns: 280px 1fr; gap: 1rem; align-items: start; }
        .mj-lista { background: #fff; border: 1px solid #e5e7eb; border-radius: .75rem; padding: .75rem; }
        .mj-hilo { display: block; width: 100%; text-align: left; border: 1px solid #e5e7eb; border-radius: .5rem; padding: .5rem .6rem; margin-bottom: .5rem; background: #fff; cursor: pointer; font-size: .85rem; }
        .mj-hilo.activo { border-color: #059669; box-shadow: 0 0 0 1px #059669; }
        .mj-hilo .n { display: inline-block; min-width: 1.4rem; text-align: center; background: #f59e0b; color: #fff; border-radius: 999px; font-size: .7rem; padding: 0 .3rem; }
        .mj-panel { background: #fff; border: 1px solid #e5e7eb; border-radius: .75rem; padding: 1rem; }
        .mj-msg { border: 1px solid #e5e7eb; border-radius: .5rem; padding: .6rem .8rem; margin-bottom: .6rem; font-size: .9rem; }
        .mj-msg.propio { background: #ecfdf5; border-color: #a7f3d0; }
        .mj-meta { font-size: .75rem; color: #4b5563; margin-bottom: .25rem; }
        .mj-campo label { display: block; font-size: .85rem; font-weight: 600; margin-bottom: .25rem; }
        .mj-campo textarea, .mj-campo input { width: 100%; border: 1px solid #d1d5db; border-radius: .5rem; padding: .5rem .6rem; font-size: .9rem; }
        .mj-error { color: #b91c1c; font-size: .8rem; margin-top: .2rem; }
        .mj-btn { display: inline-block; padding: .5rem 1rem; border-radius: .5rem; background: #059669; color: #fff; border: none; cursor: pointer; font-size: .9rem; margin-top: .5rem; }
        .mj-btn-sec { background: #fff; color: #111827; border: 1px solid #d1d5db; }
        @media (max-width: 800px) { .mj-cols { grid-template-columns: 1fr; } }
    </style>

    <div style="margin-bottom:1rem">
        <button type="button" class="mj-btn mj-btn-sec" wire:click="$set('mostrar_nuevo', true)">Nuevo hilo</button>
    </div>

    @if ($this->mostrar_nuevo)
        <form wire:submit="abrirNuevo" class="mj-panel" style="margin-bottom:1rem">
            <div class="mj-campo" style="margin-bottom:.7rem">
                <label for="mj-asunto">Asunto</label>
                <input id="mj-asunto" type="text" wire:model="nuevo_asunto">
                @error('nuevo_asunto') <div class="mj-error">{{ $message }}</div> @enderror
            </div>
            <div class="mj-campo">
                <label for="mj-nuevo">Mensaje</label>
                <textarea id="mj-nuevo" wire:model="nuevo_cuerpo" rows="4"></textarea>
                @error('nuevo_cuerpo') <div class="mj-error">{{ $message }}</div> @enderror
            </div>
            <button type="submit" class="mj-btn">Enviar</button>
            <button type="button" class="mj-btn mj-btn-sec" wire:click="$set('mostrar_nuevo', false)">Cancelar</button>
        </form>
    @endif

    <div class="mj-cols">
        <div class="mj-lista">
            @forelse ($this->hilos() as $hilo)
                <button type="button" wire:click="seleccionar({{ $hilo->id }})" class="mj-hilo {{ $this->hilo_id === $hilo->id ? 'activo' : '' }}">
                    <strong>{{ $hilo->asunto }}</strong><br>
                    <span style="color:#4b5563">{{ $hilo->updated_at->format('d/m/Y H:i') }}</span>
                    @php $n = $hilo->sinLeerPara(auth()->user()); @endphp
                    @if ($n > 0) <span class="n">{{ $n }}</span> @endif
                </button>
            @empty
                <p style="font-size:.85rem;color:#4b5563">No tienes hilos todavía.</p>
            @endforelse
        </div>
        <div class="mj-panel">
            @php $actual = $this->hiloActual(); @endphp
            @if ($actual)
                <h2 style="font-size:1rem;margin:0 0 .75rem">{{ $actual->asunto }}</h2>
                @foreach ($actual->mensajes->sortBy('id') as $mensaje)
                    <div class="mj-msg {{ $mensaje->remitente_id === auth()->id() ? 'propio' : '' }}">
                        <div class="mj-meta">{{ $mensaje->remitente?->name }} · {{ $mensaje->created_at->format('d/m/Y H:i') }}</div>
                        <div style="white-space:pre-wrap">{{ $mensaje->cuerpo }}</div>
                    </div>
                @endforeach
                <form wire:submit="responder">
                    <div class="mj-campo">
                        <label for="mj-resp">Responder</label>
                        <textarea id="mj-resp" wire:model="cuerpo" rows="3"></textarea>
                        @error('cuerpo') <div class="mj-error">{{ $message }}</div> @enderror
                    </div>
                    <button type="submit" class="mj-btn">Enviar respuesta</button>
                </form>
            @else
                <p style="font-size:.9rem;color:#4b5563">Elige un hilo de la lista para leerlo y responder.</p>
            @endif
        </div>
    </div>
</x-filament-panels::page>
