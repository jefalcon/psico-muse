<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Consulta Demo · Psicóloga sanitaria en Madrid')</title>
    <meta name="description" content="@yield('descripcion', 'Consulta de psicología sanitaria: terapia para ansiedad, depresión, duelo y crecimiento personal, presencial en Madrid y online.') ">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-stone-50 text-stone-800 font-sans antialiased">
    <a href="#contenido" class="sr-only">Saltar al contenido</a>
    <header class="bg-white border-b border-stone-200">
        <div class="max-w-5xl mx-auto px-4 py-4 flex flex-wrap items-center gap-x-8 gap-y-3">
            <a href="{{ route('inicio') }}" class="flex items-center gap-3 mr-auto">
                <img src="{{ asset('images/favicon.svg') }}" alt="" width="36" height="36">
                <span class="font-serif text-xl text-teal-900">Consulta Demo <span class="block text-sm text-stone-500 font-sans">Psicología sanitaria</span></span>
            </a>
            <nav aria-label="Principal">
                <ul class="flex flex-wrap gap-x-5 gap-y-2 text-[15px]">
                    <li><a class="hover:text-teal-800 hover:underline {{ request()->routeIs('inicio') ? 'text-teal-800 font-semibold' : '' }}" href="{{ route('inicio') }}">Inicio</a></li>
                    <li><a class="hover:text-teal-800 hover:underline {{ request()->routeIs('biografia') ? 'text-teal-800 font-semibold' : '' }}" href="{{ route('biografia') }}">Biografía</a></li>
                    <li><a class="hover:text-teal-800 hover:underline {{ request()->routeIs('servicios') ? 'text-teal-800 font-semibold' : '' }}" href="{{ route('servicios') }}">Servicios</a></li>
                    <li><a class="hover:text-teal-800 hover:underline {{ request()->routeIs('blog.*') ? 'text-teal-800 font-semibold' : '' }}" href="{{ route('blog.indice') }}">Blog</a></li>
                    <li><a class="hover:text-teal-800 hover:underline {{ request()->routeIs('articulos.*') ? 'text-teal-800 font-semibold' : '' }}" href="{{ route('articulos.indice') }}">Artículos de salud</a></li>
                    <li><a class="hover:text-teal-800 hover:underline {{ request()->routeIs('contacto.*') ? 'text-teal-800 font-semibold' : '' }}" href="{{ route('contacto.formulario') }}">Contacto</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main id="contenido">
        @yield('contenido')
    </main>

    <footer class="bg-teal-950 text-teal-50 mt-16">
        <div class="max-w-5xl mx-auto px-4 py-10 grid gap-8 md:grid-cols-3">
            <div>
                <p class="font-serif text-lg">Consulta Demo</p>
                <p class="text-sm mt-2 text-teal-100">Psicología sanitaria presencial en Madrid y online. Colegiada M-28417.</p>
            </div>
            <nav aria-label="Secundaria">
                <ul class="text-sm space-y-2">
                    <li><a class="hover:underline" href="{{ route('biografia') }}">Biografía</a></li>
                    <li><a class="hover:underline" href="{{ route('servicios') }}">Servicios</a></li>
                    <li><a class="hover:underline" href="{{ route('blog.indice') }}">Blog</a></li>
                    <li><a class="hover:underline" href="{{ route('articulos.indice') }}">Artículos de salud</a></li>
                    <li><a class="hover:underline" href="{{ route('contacto.formulario') }}">Contacto</a></li>
                </ul>
            </nav>
            <nav aria-label="Legal">
                <ul class="text-sm space-y-2">
                    <li><a class="hover:underline" href="{{ route('legal.aviso') }}">Aviso legal</a></li>
                    <li><a class="hover:underline" href="{{ route('legal.privacidad') }}">Política de privacidad</a></li>
                    <li><a class="hover:underline" href="{{ url('/portal') }}">Espacio del paciente</a></li>
                </ul>
            </nav>
        </div>
        <div class="border-t border-teal-900">
            <p class="max-w-5xl mx-auto px-4 py-4 text-xs text-teal-200">© {{ date('Y') }} Consulta Demo. Contenido de demostración con fines ilustrativos.</p>
        </div>
    </footer>
</body>
</html>
