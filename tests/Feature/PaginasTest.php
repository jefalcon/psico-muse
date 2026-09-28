<?php

namespace Tests\Feature;

use App\Filament\Admin\Pages\Agenda;
use App\Filament\Admin\Pages\HorarioLaboral;
use App\Filament\Portal\Pages\Mensajes;
use App\Filament\Portal\Pages\MiPerfil;
use App\Models\Cliente;
use App\Models\Hilo;
use App\Models\User;
use App\Services\Horario;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class PaginasTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_agenda_cambia_de_modo_y_periodo(): void
    {
        $this->actingAs(User::where('email', 'admin@demo.test')->firstOrFail());
        \Filament\Facades\Filament::setCurrentPanel('admin');

        Livewire::test(Agenda::class)
            ->assertSet('modo', 'semana')
            ->call('cambiarModo', 'mes')
            ->assertSet('modo', 'mes')
            ->call('siguiente')
            ->call('anterior')
            ->call('hoy')
            ->assertHasNoErrors();
    }

    public function test_horario_laboral_se_guarda(): void
    {
        $this->actingAs(User::where('email', 'admin@demo.test')->firstOrFail());
        \Filament\Facades\Filament::setCurrentPanel('admin');

        Livewire::test(HorarioLaboral::class)
            ->set('data.dia_6', '10:00-13:00')
            ->call('guardar')
            ->assertHasNoErrors();

        $this->assertSame([['10:00', '13:00']], Horario::segmentos()[6]);
    }

    public function test_cliente_abre_hilo_nuevo(): void
    {
        $user = User::where('email', 'cliente1@demo.test')->firstOrFail();
        $this->actingAs($user);
        \Filament\Facades\Filament::setCurrentPanel('portal');

        Livewire::test(Mensajes::class)
            ->set('mostrar_nuevo', true)
            ->set('nuevo_asunto', 'Pregunta sobre la próxima sesión')
            ->set('nuevo_cuerpo', '¿Debo llevar algo preparado?')
            ->call('abrirNuevo')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('hilos', [
            'cliente_id' => $user->cliente->id,
            'asunto' => 'Pregunta sobre la próxima sesión',
        ]);
    }

    public function test_cliente_actualiza_telefono_y_contrasena(): void
    {
        $user = User::where('email', 'cliente2@demo.test')->firstOrFail();
        $this->actingAs($user);
        \Filament\Facades\Filament::setCurrentPanel('portal');

        Livewire::test(MiPerfil::class)
            ->set('telefono', '699888777')
            ->call('guardarTelefono')
            ->assertHasNoErrors();

        $this->assertSame('699888777', $user->cliente->fresh()->telefono);

        Livewire::test(MiPerfil::class)
            ->set('actual', 'password')
            ->set('nueva', 'nueva-clave-123')
            ->set('nueva_confirmation', 'nueva-clave-123')
            ->call('cambiarContrasena')
            ->assertHasNoErrors();

        $this->assertTrue(Hash::check('nueva-clave-123', $user->fresh()->password));
    }
}
