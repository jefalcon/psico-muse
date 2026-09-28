<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Resources\Citas\CitaResource;
use App\Models\Cita;
use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;

class Agenda extends Page
{
    protected string $view = 'filament.admin.pages.agenda';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;

    protected static ?string $navigationLabel = 'Agenda';

    protected static ?string $title = 'Agenda';

    protected static ?int $navigationSort = 69;

    public string $modo = 'semana';

    public string $referencia = '';

    public function mount(): void
    {
        $this->referencia = today()->toDateString();
    }

    public function cambiarModo(string $modo): void
    {
        $this->modo = in_array($modo, ['semana', 'mes'], true) ? $modo : 'semana';
    }

    public function anterior(): void
    {
        $ref = Carbon::parse($this->referencia);
        $this->referencia = ($this->modo === 'mes' ? $ref->subMonthNoOverflow() : $ref->subWeek())->toDateString();
    }

    public function siguiente(): void
    {
        $ref = Carbon::parse($this->referencia);
        $this->referencia = ($this->modo === 'mes' ? $ref->addMonthNoOverflow() : $ref->addWeek())->toDateString();
    }

    public function hoy(): void
    {
        $this->referencia = today()->toDateString();
    }

    /** @return array{inicio: Carbon, fin: Carbon} */
    public function rango(): array
    {
        $ref = Carbon::parse($this->referencia);

        if ($this->modo === 'mes') {
            $inicioMes = $ref->copy()->startOfMonth();

            return [
                'inicio' => $inicioMes->copy()->startOfWeek(Carbon::MONDAY),
                'fin' => $ref->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY),
            ];
        }

        return [
            'inicio' => $ref->copy()->startOfWeek(Carbon::MONDAY)->startOfDay(),
            'fin' => $ref->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay(),
        ];
    }

    /** @return Collection<int, Cita> */
    public function citas(): Collection
    {
        $rango = $this->rango();

        return Cita::with(['cliente.user', 'servicio'])
            ->whereBetween('inicio', [$rango['inicio'], $rango['fin']])
            ->orderBy('inicio')
            ->get();
    }

    public function pendientes(): int
    {
        return Cita::where('estado', Cita::ESTADO_SOLICITADA)->count();
    }

    public static function urlCita(Cita $cita): string
    {
        return CitaResource::getUrl('view', ['record' => $cita]);
    }
}
