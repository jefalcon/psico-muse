<?php

namespace App\Filament\Portal\Pages;

use App\Models\Cita;
use App\Models\Servicio;
use App\Services\GestorCitas;
use App\Services\Horario;
use BackedEnum;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;

class MisCitas extends Page
{
    protected string $view = 'filament.portal.pages.mis-citas';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $navigationLabel = 'Mis citas';

    protected static ?string $title = 'Mis citas';

    protected static ?int $navigationSort = 2;

    public ?int $servicio_id = null;

    public string $fecha = '';

    public string $hora = '';

    public string $comentario = '';

    /** @var array<int, string> */
    public array $huecos = [];

    public function mount(): void
    {
        $this->fecha = today()->addDays(2)->toDateString();
    }

    /** @return Collection<int, Cita> */
    public function citas(): Collection
    {
        $cliente = auth()->user()->cliente;

        if (! $cliente) {
            return collect();
        }

        return $cliente->citas()->with('servicio')->orderBy('inicio')->get();
    }

    /** @return Collection<int, Servicio> */
    public function servicios(): Collection
    {
        return Servicio::activos()->get();
    }

    public function updatedServicioId(): void
    {
        $this->recalcularHuecos();
    }

    public function updatedFecha(): void
    {
        $this->recalcularHuecos();
    }

    public function recalcularHuecos(): void
    {
        $this->huecos = [];
        $this->hora = '';

        if (! $this->servicio_id || $this->fecha === '') {
            return;
        }

        try {
            $dia = Carbon::parse($this->fecha);
        } catch (\Throwable) {
            return;
        }

        $servicio = Servicio::find($this->servicio_id);

        if (! $servicio) {
            return;
        }

        $this->huecos = Horario::huecosLibres($dia, $servicio->duracion_minutos);
    }

    public function solicitar(): void
    {
        $this->validate([
            'servicio_id' => ['required', 'exists:servicios,id'],
            'fecha' => ['required', 'date'],
            'hora' => ['required'],
            'comentario' => ['nullable', 'string', 'max:2000'],
        ], [], [
            'servicio_id' => 'servicio',
            'fecha' => 'fecha',
            'hora' => 'hora',
        ]);

        try {
            $inicio = Carbon::parse($this->fecha.' '.$this->hora);
        } catch (\Throwable) {
            Notification::make()->title('Fecha u hora no válida')->danger()->send();

            return;
        }

        try {
            GestorCitas::solicitar(
                auth()->user()->cliente->id,
                (int) $this->servicio_id,
                $inicio,
                $this->comentario ?: null
            );
        } catch (\RuntimeException $e) {
            Notification::make()->title('No se ha podido solicitar la cita')->body($e->getMessage())->danger()->send();

            return;
        }

        $this->comentario = '';
        $this->recalcularHuecos();

        Notification::make()->title('Solicitud enviada')->body('Te avisaremos cuando se confirme.')->success()->send();
    }

    public function cancelar(int $citaId): void
    {
        $cita = Cita::findOrFail($citaId);
        abort_unless($cita->cliente_id === auth()->user()->cliente?->id, 403);

        try {
            GestorCitas::cancelarPorCliente($cita);
        } catch (\RuntimeException $e) {
            Notification::make()->title('No se ha podido cancelar')->body($e->getMessage())->danger()->send();

            return;
        }

        Notification::make()->title('Cita cancelada')->success()->send();
    }
}
