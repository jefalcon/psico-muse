<?php

namespace Tests\Feature;

use App\Models\Adjunto;
use App\Models\Cita;
use App\Models\Cliente;
use App\Models\EntradaHistorial;
use App\Models\Hilo;
use App\Models\Lead;
use App\Models\Servicio;
use App\Models\User;
use App\Services\GestorCitas;
use App\Services\GestorClientes;
use App\Services\GestorMensajes;
use Carbon\Carbon;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FlujoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
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

    public function test_convertir_lead_crea_cliente_y_notifica(): void
    {
        Notification::fake();

        $lead = Lead::where('estado', Lead::ESTADO_NUEVO)->firstOrFail();
        [$user, $cliente] = GestorClientes::crearDesdeLead($lead);

        $this->assertSame('cliente', $user->role);
        $this->assertSame($lead->email, $user->email);
        $this->assertSame(Lead::ESTADO_CONVERTIDO, $lead->fresh()->estado);
        $this->assertSame($cliente->id, $lead->fresh()->cliente_id);
        Notification::assertSentTo($user, \Filament\Auth\Notifications\ResetPassword::class);
    }

    public function test_convertir_dos_veces_falla(): void
    {
        $lead = Lead::where('estado', Lead::ESTADO_NUEVO)->firstOrFail();
        GestorClientes::crearDesdeLead($lead);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/ya fue convertido/');
        GestorClientes::crearDesdeLead($lead->fresh());
    }

    public function test_convertir_con_email_existente_muestra_error_sin_crear(): void
    {
        $lead = Lead::where('estado', Lead::ESTADO_NUEVO)->firstOrFail();
        $lead->update(['email' => 'cliente1@demo.test']);

        try {
            GestorClientes::crearDesdeLead($lead);
            $this->fail('Debería lanzar excepción.');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('Ya existe un usuario', $e->getMessage());
        }

        $this->assertSame(Lead::ESTADO_NUEVO, $lead->fresh()->estado);
        $this->assertSame(2, Cliente::count());
    }

    public function test_dos_confirmadas_no_se_solapan(): void
    {
        $cliente = Cliente::firstOrFail();
        $servicio = Servicio::activos()->firstOrFail();
        $inicio = $this->diaLaborable(today()->addDays(10), '10:00');

        GestorCitas::solicitar($cliente->id, $servicio->id, $inicio);
        $pdo = Cita::latest('id')->firstOrFail();
        GestorCitas::confirmar($pdo);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/ocupado/');
        GestorCitas::solicitar($cliente->id, $servicio->id, $inicio->copy()->addMinutes(15));
    }

    public function test_confirmar_solapada_falla_y_rechazar_exige_motivo(): void
    {
        $cliente = Cliente::firstOrFail();
        $servicio = Servicio::activos()->firstOrFail();

        $a = Cita::create([
            'cliente_id' => $cliente->id, 'servicio_id' => $servicio->id,
            'inicio' => $this->diaLaborable(today()->addDays(11), '10:00'),
            'fin' => $this->diaLaborable(today()->addDays(11), '10:00')->addMinutes(50),
            'estado' => Cita::ESTADO_CONFIRMADA,
        ]);
        $b = Cita::create([
            'cliente_id' => $cliente->id, 'servicio_id' => $servicio->id,
            'inicio' => $this->diaLaborable(today()->addDays(11), '10:15'),
            'fin' => $this->diaLaborable(today()->addDays(11), '10:15')->addMinutes(50),
            'estado' => Cita::ESTADO_SOLICITADA,
        ]);

        try {
            GestorCitas::confirmar($b);
            $this->fail('Debería fallar por solape.');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('solapa', $e->getMessage());
        }

        try {
            GestorCitas::rechazar($b, '  ');
            $this->fail('Debería exigir motivo.');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('motivo', $e->getMessage());
        }

        Notification::fake();
        GestorCitas::rechazar($b, 'Hueco no disponible');
        $this->assertSame(Cita::ESTADO_RECHAZADA, $b->fresh()->estado);
        Notification::assertSentTo($cliente->user, \App\Notifications\CitaActualizada::class);
    }

    public function test_solicitar_exige_24h_y_horario(): void
    {
        $cliente = Cliente::firstOrFail();
        $servicio = Servicio::activos()->firstOrFail();

        try {
            GestorCitas::solicitar($cliente->id, $servicio->id, now()->addHours(2));
            $this->fail('Debería exigir 24 h.');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('24 horas', $e->getMessage());
        }

        $domingo = today()->next(Carbon::SUNDAY)->setTime(10, 0);
        if ($domingo->lessThan(now()->addHours(24))) {
            $domingo->addWeek();
        }

        try {
            GestorCitas::solicitar($cliente->id, $servicio->id, $domingo);
            $this->fail('Debería exigir horario laboral.');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('horario laboral', $e->getMessage());
        }
    }

    public function test_envio_multiple_crea_hilos_individuales(): void
    {
        Notification::fake();
        $admin = User::where('role', 'admin')->firstOrFail();
        $ids = Cliente::pluck('id')->all();

        $hilos = GestorMensajes::envioMultiple($ids, 'Aviso', $admin, 'Mensaje común');

        $this->assertCount(2, $hilos);
        $clientes = Hilo::whereIn('id', $hilos)->pluck('cliente_id')->all();
        sort($clientes);
        $esperados = $ids;
        sort($esperados);
        $this->assertSame($esperados, $clientes);

        foreach (Cliente::with('user')->get() as $cliente) {
            Notification::assertSentTo($cliente->user, \App\Notifications\MensajeRecibido::class);
        }
    }

    public function test_adjuntos_solo_para_su_dueno_y_admin(): void
    {
        Storage::disk('privado')->put('historial/t.pdf', 'contenido');
        $c1 = Cliente::whereHas('user', fn ($q) => $q->where('email', 'cliente1@demo.test'))->firstOrFail();
        $c2 = Cliente::whereHas('user', fn ($q) => $q->where('email', 'cliente2@demo.test'))->firstOrFail();

        $visible = EntradaHistorial::create([
            'cliente_id' => $c1->id, 'fecha' => today(), 'motivo' => 'm', 'visible_cliente' => true,
        ]);
        $adjunto = Adjunto::create([
            'entrada_historial_id' => $visible->id, 'ruta' => 'historial/t.pdf', 'nombre_original' => 't.pdf',
        ]);

        $oculta = EntradaHistorial::create([
            'cliente_id' => $c1->id, 'fecha' => today(), 'motivo' => 'm', 'visible_cliente' => false,
        ]);
        $adjuntoOculto = Adjunto::create([
            'entrada_historial_id' => $oculta->id, 'ruta' => 'historial/t.pdf', 'nombre_original' => 't.pdf',
        ]);

        $this->get('/adjuntos/'.$adjunto->id)->assertRedirect('/entrar');

        $this->actingAs($c1->user);
        $this->get('/adjuntos/'.$adjunto->id)->assertOk();
        $this->get('/adjuntos/'.$adjuntoOculto->id)->assertForbidden();

        $this->actingAs($c2->user);
        $this->get('/adjuntos/'.$adjunto->id)->assertForbidden();

        $this->actingAs(User::where('role', 'admin')->firstOrFail());
        $this->get('/adjuntos/'.$adjuntoOculto->id)->assertOk();
    }
}
