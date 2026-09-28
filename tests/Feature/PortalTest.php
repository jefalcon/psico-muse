<?php

namespace Tests\Feature;

use App\Filament\Portal\Pages\Mensajes;
use App\Filament\Portal\Pages\MisCitas;
use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Hilo;
use App\Models\Mensaje;
use App\Models\Servicio;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PortalTest extends TestCase
{
    use RefreshDatabase;

    private function cliente(string $email = 'cliente@demo.test'): User
    {
        $user = User::factory()->create(['role' => 'cliente', 'email' => $email]);
        Cliente::create(['user_id' => $user->id, 'telefono' => '600111222']);

        return $user->fresh();
    }

    public function test_invitado_es_redirigido_al_login_del_portal(): void
    {
        $this->get('/portal')->assertRedirect('/portal/login');
    }

    public function test_admin_recibe_403_en_portal(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->get('/portal')->assertForbidden();
        $this->get('/portal/mis-citas')->assertForbidden();
    }

    public function test_cliente_ve_las_paginas_del_portal(): void
    {
        $this->actingAs($this->cliente());

        $this->get('/portal')->assertRedirect('/portal/inicio');

        foreach ([
            '/portal/inicio',
            '/portal/mis-citas',
            '/portal/mensajes',
            '/portal/mi-historial',
            '/portal/mi-perfil',
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_cliente_no_ve_hilo_de_otro(): void
    {
        $uno = $this->cliente('uno@demo.test');
        $dos = $this->cliente('dos@demo.test');

        $hilo = Hilo::create(['cliente_id' => $dos->cliente->id, 'asunto' => 'Privado', 'creado_por' => $dos->id]);

        $this->actingAs($uno);
        $this->get('/portal/mensajes?hilo='.$hilo->id)->assertNotFound();
    }

    public function test_cliente_solicita_cita_en_hueco_libre(): void
    {
        $user = $this->cliente();
        $this->actingAs($user);

        $servicio = Servicio::create([
            'nombre' => 'Terapia individual',
            'descripcion' => 'Sesión de 50 minutos.',
            'duracion_minutos' => 50,
            'precio' => 60,
            'activo' => true,
        ]);

        $dia = today()->addDays(3);
        while ($dia->isWeekend()) {
            $dia->addDay();
        }

        Livewire::test(MisCitas::class)
            ->set('servicio_id', $servicio->id)
            ->set('fecha', $dia->toDateString())
            ->assertSet('huecos', fn ($huecos) => count($huecos) > 0)
            ->set('hora', '10:00')
            ->set('comentario', 'Primera visita')
            ->call('solicitar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('citas', [
            'cliente_id' => $user->cliente->id,
            'servicio_id' => $servicio->id,
            'estado' => Cita::ESTADO_SOLICITADA,
        ]);
    }

    public function test_cliente_cancela_su_cita_con_antelacion(): void
    {
        $user = $this->cliente();
        $this->actingAs($user);

        $servicio = Servicio::create([
            'nombre' => 'Terapia individual',
            'descripcion' => 'Sesión.',
            'duracion_minutos' => 50,
            'activo' => true,
        ]);

        $inicio = Carbon::parse(today()->addDays(5)->toDateString().' 10:00');
        while ($inicio->isWeekend()) {
            $inicio->addDay();
        }

        $cita = Cita::create([
            'cliente_id' => $user->cliente->id,
            'servicio_id' => $servicio->id,
            'inicio' => $inicio,
            'fin' => $inicio->copy()->addMinutes(50),
            'estado' => Cita::ESTADO_CONFIRMADA,
        ]);

        Livewire::test(MisCitas::class)->call('cancelar', $cita->id);

        $this->assertSame(Cita::ESTADO_CANCELADA, $cita->fresh()->estado);
    }

    public function test_cliente_responde_en_su_hilo(): void
    {
        $user = $this->cliente();
        $this->actingAs($user);

        $hilo = Hilo::create(['cliente_id' => $user->cliente->id, 'asunto' => 'Duda', 'creado_por' => $user->id]);
        Mensaje::create(['hilo_id' => $hilo->id, 'remitente_id' => $user->id, 'cuerpo' => 'Hola']);

        Livewire::test(Mensajes::class)
            ->call('seleccionar', $hilo->id)
            ->set('cuerpo', 'Gracias por tu respuesta')
            ->call('responder')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('mensajes', [
            'hilo_id' => $hilo->id,
            'remitente_id' => $user->id,
            'cuerpo' => 'Gracias por tu respuesta',
        ]);
    }
}
