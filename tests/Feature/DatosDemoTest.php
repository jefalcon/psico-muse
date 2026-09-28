<?php

namespace Tests\Feature;

use App\Models\Articulo;
use App\Models\Cita;
use App\Models\Cliente;
use App\Models\EntradaHistorial;
use App\Models\Hilo;
use App\Models\Lead;
use App\Models\Post;
use App\Models\Servicio;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class DatosDemoTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeders_crean_los_datos_de_demostracion(): void
    {
        $this->seed(DatabaseSeeder::class);

        // Usuarios y credenciales.
        $this->assertTrue(Auth::attempt(['email' => 'admin@demo.test', 'password' => 'password']));
        Auth::logout();
        $this->assertTrue(Auth::attempt(['email' => 'cliente1@demo.test', 'password' => 'password']));
        Auth::logout();
        $this->assertTrue(Auth::attempt(['email' => 'cliente2@demo.test', 'password' => 'password']));
        Auth::logout();
        $this->assertSame('admin', User::where('email', 'admin@demo.test')->firstOrFail()->role);

        // Servicios: 4 activos y 1 inactivo.
        $this->assertSame(4, Servicio::where('activo', true)->count());
        $this->assertSame(1, Servicio::where('activo', false)->count());

        // Contenidos.
        $this->assertSame(3, Post::where('estado', Post::ESTADO_PUBLICADO)->count());
        $this->assertSame(1, Post::where('estado', Post::ESTADO_BORRADOR)->count());
        $this->assertSame(2, Articulo::where('categoria', Articulo::CATEGORIA_TRASTORNO)->where('estado', Articulo::ESTADO_PUBLICADO)->count());
        $this->assertSame(2, Articulo::where('categoria', Articulo::CATEGORIA_TRATAMIENTO)->where('estado', Articulo::ESTADO_PUBLICADO)->count());

        // Leads en 3 estados distintos.
        $this->assertSame(3, Lead::count());
        $this->assertSame(3, Lead::distinct()->count('estado'));

        // Historial visible y no visible para ambos clientes.
        foreach (Cliente::all() as $cliente) {
            $this->assertGreaterThanOrEqual(1, EntradaHistorial::where('cliente_id', $cliente->id)->where('visible_cliente', true)->count());
            $this->assertGreaterThanOrEqual(1, EntradaHistorial::where('cliente_id', $cliente->id)->where('visible_cliente', false)->count());
            $this->assertGreaterThanOrEqual(1, Hilo::where('cliente_id', $cliente->id)->count());
        }

        // Citas en varios estados.
        $this->assertGreaterThanOrEqual(4, Cita::distinct()->count('estado'));

        // Toda publicación tiene imagen.
        $this->assertSame(0, Post::whereNull('imagen')->count());
        $this->assertSame(0, Articulo::whereNull('imagen')->count());
    }
}
