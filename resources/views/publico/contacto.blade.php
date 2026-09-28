@extends('publico.base')

@section('titulo', 'Contacto · Pide tu primera entrevista gratuita')
@section('descripcion', 'Contacta con Consulta Demo: primera entrevista informativa gratuita, respuesta en menos de 24 horas laborables.')

@section('contenido')
<div class="max-w-3xl mx-auto px-4 py-10">
    <h1 class="font-serif text-3xl md:text-4xl text-teal-950">Contacto</h1>
    <p class="mt-3 text-stone-600">Cuéntame qué te preocupa y te respondo en menos de 24 horas laborables. La primera entrevista informativa (30 minutos) es gratuita.</p>

    @if (session('enviado'))
        <div class="mt-6 bg-teal-50 border border-teal-300 rounded-xl p-5" role="status">
            <p class="font-semibold text-teal-900">Mensaje enviado. ¡Gracias por escribir!</p>
            <p class="mt-1 text-sm text-stone-600">Te responderé lo antes posible, en menos de 24 horas laborables.</p>
        </div>
    @endif

    <form method="POST" action="{{ route('contacto.enviar') }}" class="mt-6 bg-white border border-stone-200 rounded-xl p-6 space-y-5">
        @csrf
        <div>
            <label for="nombre" class="block text-sm font-semibold">Nombre *</label>
            <input id="nombre" name="nombre" type="text" value="{{ old('nombre') }}" required class="mt-1 w-full border border-stone-300 rounded-lg px-3 py-2">
            @error('nombre') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
        </div>
        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label for="email" class="block text-sm font-semibold">Email *</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required class="mt-1 w-full border border-stone-300 rounded-lg px-3 py-2">
                @error('email') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="telefono" class="block text-sm font-semibold">Teléfono (opcional)</label>
                <input id="telefono" name="telefono" type="tel" value="{{ old('telefono') }}" class="mt-1 w-full border border-stone-300 rounded-lg px-3 py-2">
                @error('telefono') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>
        </div>
        <div>
            <label for="servicio_id" class="block text-sm font-semibold">Servicio de interés (opcional)</label>
            <select id="servicio_id" name="servicio_id" class="mt-1 w-full border border-stone-300 rounded-lg px-3 py-2">
                <option value="">— No lo sé todavía —</option>
                @foreach ($servicios as $servicio)
                    <option value="{{ $servicio->id }}" @selected(old('servicio_id') == $servicio->id)>{{ $servicio->nombre }}</option>
                @endforeach
            </select>
            @error('servicio_id') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="mensaje" class="block text-sm font-semibold">Mensaje *</label>
            <textarea id="mensaje" name="mensaje" rows="5" required class="mt-1 w-full border border-stone-300 rounded-lg px-3 py-2">{{ old('mensaje') }}</textarea>
            @error('mensaje') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="flex items-start gap-2 text-sm">
                <input type="checkbox" name="consentimiento" value="1" @checked(old('consentimiento')) class="mt-1">
                <span>He leído y acepto la <a href="{{ route('legal.privacidad') }}" class="underline text-teal-800">política de privacidad</a>. *</span>
            </label>
            @error('consentimiento') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
        </div>
        <button type="submit" class="bg-teal-800 text-white px-6 py-3 rounded-lg hover:bg-teal-900">Enviar mensaje</button>
    </form>
</div>
@endsection
