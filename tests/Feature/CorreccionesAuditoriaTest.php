<?php

namespace Tests\Feature;

use App\Filament\Admin\Resources\Citas\Pages\CreateCita;
use App\Filament\Admin\Resources\Citas\Pages\EditCita;
use App\Filament\Admin\Resources\Hilos\Pages\CreateHilo;
use App\Filament\Admin\Resources\Hilos\Pages\ListHilos;
use App\Filament\Admin\Resources\Historial\Pages\CreateEntradaHistorial;
use App\Filament\Admin\Resources\Historial\Pages\ListEntradasHistorial;
use App\Models\Cita;
use App\Models\Cliente;
use App\Models\EntradaHistorial;
use App\Models\Hilo;
use App\Models\Servicio;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\DatabaseSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Regresión de la auditoría externa F2756F: cada test falla antes de su
 * arreglo y pasa después.
 */
class CorreccionesAuditoriaTest extends TestCase
{
    use RefreshDatabase;

    protected function comoAdmin(): User
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::where('email', 'admin@demo.test')->firstOrFail();
        $this->actingAs($admin);
        Filament::setCurrentPanel('admin');

        return $admin;
    }

    /** Opciones [valor => etiqueta] del selector de cliente de una página de creación. */
    private function opcionesSelectorCliente(string $pagina): array
    {
        $select = Livewire::test($pagina)->instance()->getSchema('form')->getComponent('cliente_id');
        $this->assertNotNull($select, "La página {$pagina} no tiene selector cliente_id.");

        return $select->getOptions();
    }

    private function valorPara(array $opciones, string $nombre): string
    {
        foreach ($opciones as $valor => $etiqueta) {
            if (str_contains((string) $etiqueta, $nombre)) {
                return (string) $valor;
            }
        }

        $this->fail("Ninguna opción contiene «{$nombre}». Opciones: ".json_encode($opciones));
    }

    private function diaLaborable(Carbon $dia, string $hora): Carbon
    {
        $d = $dia->copy();
        while ($d->isWeekend()) {
            $d->addDay();
        }
        [$h, $m] = explode(':', $hora);

        return $d->setTime((int) $h, (int) $m);
    }

    private function clientesDemo(): array
    {
        $lucia = Cliente::whereHas('user', fn ($q) => $q->where('email', 'cliente1@demo.test'))->firstOrFail();
        $marcos = Cliente::whereHas('user', fn ($q) => $q->where('email', 'cliente2@demo.test'))->firstOrFail();

        // Con los datos de demo los ids de usuario y de cliente no coinciden:
        // si el formulario guarda el id de usuario, el registro cae en otro cliente.
        $this->assertNotSame($marcos->id, $marcos->user->id);

        return [$lucia, $marcos];
    }

    // Defecto 1 (CRÍTICO): el selector de cliente ofrecía usuarios y guardaba
    // el id de usuario como cliente_id (paciente equivocado).
    public function test_selectores_de_cliente_ofrecen_solo_clientes(): void
    {
        $this->comoAdmin();
        [$lucia, $marcos] = $this->clientesDemo();
        $esperados = [(string) $lucia->id, (string) $marcos->id];
        sort($esperados);

        foreach ([CreateEntradaHistorial::class, CreateHilo::class, CreateCita::class] as $pagina) {
            $opciones = $this->opcionesSelectorCliente($pagina);

            $valores = array_map('strval', array_keys($opciones));
            sort($valores);
            $this->assertSame($esperados, $valores, "Valores del selector en {$pagina}.");

            $etiquetas = implode("\n", array_map('strval', array_values($opciones)));
            $this->assertStringNotContainsString('Elena Márquez', $etiquetas, "La admin no debe aparecer en {$pagina}.");
            $this->assertStringContainsString('Lucía Fernández', $etiquetas);
            $this->assertStringContainsString('Marcos Ruiz', $etiquetas);
        }
    }

    // Defecto 1: crear historial, hilo y cita desde el admin para un cliente
    // concreto debe asignarlos a ese cliente y no a otro.
    public function test_admin_crea_historial_hilo_y_cita_para_el_cliente_correcto(): void
    {
        $this->comoAdmin();
        [$lucia, $marcos] = $this->clientesDemo();

        // Historial clínico.
        $valorMarcos = $this->valorPara($this->opcionesSelectorCliente(CreateEntradaHistorial::class), 'Marcos Ruiz');

        Livewire::test(CreateEntradaHistorial::class)
            ->fillForm([
                'cliente_id' => $valorMarcos,
                'fecha' => today()->toDateString(),
                'motivo' => 'Motivo de prueba de auditoría',
                'notas_sesion' => 'Notas de prueba',
                'visible_cliente' => false,
                'adjuntos' => [],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $entrada = EntradaHistorial::where('motivo', 'Motivo de prueba de auditoría')->firstOrFail();
        $this->assertSame($marcos->id, $entrada->cliente_id);
        $this->assertTrue($marcos->fresh()->entradasHistorial()->whereKey($entrada->id)->exists());
        $this->assertFalse($lucia->fresh()->entradasHistorial()->whereKey($entrada->id)->exists());

        // Hilo desde "Abrir hilo" (acción de crear del listado de mensajes).
        $valorMarcosHilo = $this->valorPara($this->opcionesSelectorCliente(CreateHilo::class), 'Marcos Ruiz');

        Livewire::test(ListHilos::class)
            ->mountAction('create')
            ->setActionData([
                'cliente_id' => $valorMarcosHilo,
                'asunto' => 'Hilo de prueba de auditoría',
                'cuerpo' => 'Mensaje inicial de prueba',
            ])
            ->callMountedAction()
            ->assertHasNoActionErrors();

        $hilo = Hilo::where('asunto', 'Hilo de prueba de auditoría')->firstOrFail();
        $this->assertSame($marcos->id, $hilo->cliente_id);
        $this->assertTrue($marcos->fresh()->hilos()->whereKey($hilo->id)->exists());
        $this->assertFalse($lucia->fresh()->hilos()->whereKey($hilo->id)->exists());

        // Alta de cita.
        $servicio = Servicio::activos()->firstOrFail();
        $inicio = $this->diaLaborable(today()->addDays(20), '10:00');
        $valorMarcosCita = $this->valorPara($this->opcionesSelectorCliente(CreateCita::class), 'Marcos Ruiz');

        Livewire::test(CreateCita::class)
            ->fillForm([
                'cliente_id' => $valorMarcosCita,
                'servicio_id' => $servicio->id,
                'inicio' => $inicio->format('Y-m-d H:i'),
                'estado' => Cita::ESTADO_CONFIRMADA,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $cita = Cita::where('inicio', $inicio)->firstOrFail();
        $this->assertSame($marcos->id, $cita->cliente_id);
        $this->assertTrue($marcos->fresh()->citas()->whereKey($cita->id)->exists());
        $this->assertFalse($lucia->fresh()->citas()->whereKey($cita->id)->exists());

        // Edición de cita: reasignar al otro cliente también debe acertar.
        $valorLuciaCita = $this->valorPara($this->opcionesSelectorCliente(CreateCita::class), 'Lucía Fernández');

        Livewire::test(EditCita::class, ['record' => $cita->id])
            ->fillForm([
                'cliente_id' => $valorLuciaCita,
                'servicio_id' => $servicio->id,
                'inicio' => $inicio->format('Y-m-d H:i'),
                'estado' => Cita::ESTADO_CONFIRMADA,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame($lucia->id, $cita->fresh()->cliente_id);
    }

    // Defecto 1: el filtro de cliente del historial también confundía ids.
    public function test_filtro_de_historial_por_cliente_filtra_por_cliente(): void
    {
        $this->comoAdmin();
        [$lucia, $marcos] = $this->clientesDemo();

        Livewire::test(ListEntradasHistorial::class)
            ->filterTable('cliente_id', $marcos->id)
            ->assertCanSeeTableRecords($marcos->entradasHistorial)
            ->assertCanNotSeeTableRecords($lucia->entradasHistorial);
    }

    // Defecto 2: Filament cargaba el avatar desde ui-avatars.com en /admin y
    // /portal. El proveedor debe ser local, sin peticiones externas.
    public function test_paneles_usan_avatar_local_sin_peticiones_externas(): void
    {
        $this->comoAdmin();

        foreach (['admin', 'portal'] as $panel) {
            $proveedor = Filament::getPanel($panel)->getDefaultAvatarProvider();
            $this->assertNotSame(
                \Filament\AvatarProviders\UiAvatarsProvider::class,
                $proveedor,
                "El panel {$panel} sigue usando ui-avatars.com."
            );

            $url = app($proveedor)->get(User::firstOrFail());
            $this->assertStringNotContainsString('https://', $url, "El avatar del panel {$panel} apunta a una URL externa.");
            $this->assertStringNotContainsString('http://', $url, "El avatar del panel {$panel} apunta a una URL externa.");
            $this->assertStringStartsWith('data:image/svg+xml,', $url);
        }

        $this->get('/admin')->assertOk()->assertDontSee('ui-avatars.com', false);
    }

    // Defecto 2 (portal): la página del paciente tampoco debe pedir avatares fuera.
    public function test_portal_no_carga_avatar_externo(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->actingAs(User::where('email', 'cliente1@demo.test')->firstOrFail());

        $this->get('/portal/inicio')->assertOk()->assertDontSee('ui-avatars.com', false);
    }
}
