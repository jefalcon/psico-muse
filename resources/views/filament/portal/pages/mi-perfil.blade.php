<x-filament-panels::page>
    <style>
        .pf-form { background: #fff; border: 1px solid #e5e7eb; border-radius: .75rem; padding: 1.25rem; max-width: 560px; margin-bottom: 1.5rem; }
        .pf-form h2 { font-size: 1rem; margin: 0 0 .75rem; }
        .pf-campo { margin-bottom: .9rem; }
        .pf-campo label { display: block; font-size: .85rem; font-weight: 600; margin-bottom: .25rem; }
        .pf-campo input { width: 100%; border: 1px solid #d1d5db; border-radius: .5rem; padding: .5rem .6rem; font-size: .9rem; }
        .pf-error { color: #b91c1c; font-size: .8rem; margin-top: .2rem; }
        .pf-btn { display: inline-block; padding: .5rem 1rem; border-radius: .5rem; background: #059669; color: #fff; border: none; cursor: pointer; font-size: .9rem; }
        .pf-dato { font-size: .9rem; margin-bottom: .4rem; }
    </style>

    <div class="pf-form">
        <h2>Tus datos</h2>
        <p class="pf-dato"><strong>Nombre:</strong> {{ auth()->user()->name }}</p>
        <p class="pf-dato"><strong>Email:</strong> {{ auth()->user()->email }}</p>
    </div>

    <form wire:submit="guardarTelefono" class="pf-form">
        <h2>Teléfono</h2>
        <div class="pf-campo">
            <label for="pf-tel">Teléfono</label>
            <input id="pf-tel" type="tel" wire:model="telefono">
            @error('telefono') <div class="pf-error">{{ $message }}</div> @enderror
        </div>
        <button type="submit" class="pf-btn">Guardar teléfono</button>
    </form>

    <form wire:submit="cambiarContrasena" class="pf-form">
        <h2>Cambiar contraseña</h2>
        <div class="pf-campo">
            <label for="pf-actual">Contraseña actual</label>
            <input id="pf-actual" type="password" wire:model="actual" autocomplete="current-password">
            @error('actual') <div class="pf-error">{{ $message }}</div> @enderror
        </div>
        <div class="pf-campo">
            <label for="pf-nueva">Nueva contraseña (mínimo 8 caracteres)</label>
            <input id="pf-nueva" type="password" wire:model="nueva" autocomplete="new-password">
            @error('nueva') <div class="pf-error">{{ $message }}</div> @enderror
        </div>
        <div class="pf-campo">
            <label for="pf-conf">Repite la nueva contraseña</label>
            <input id="pf-conf" type="password" wire:model="nueva_confirmation" autocomplete="new-password">
        </div>
        <button type="submit" class="pf-btn">Cambiar contraseña</button>
    </form>
</x-filament-panels::page>
