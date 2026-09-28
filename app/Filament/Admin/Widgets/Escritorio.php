<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\Citas\CitaResource;
use App\Filament\Admin\Resources\Hilos\HiloResource;
use App\Filament\Admin\Resources\Leads\LeadResource;
use App\Models\Cita;
use App\Models\Lead;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class Escritorio extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $solicitudes = Cita::where('estado', Cita::ESTADO_SOLICITADA)->count();
        $leads = Lead::where('estado', Lead::ESTADO_NUEVO)->count();
        $sinLeer = auth()->user()?->mensajesSinLeer() ?? 0;
        $hoy = Cita::whereDate('inicio', today())->whereIn('estado', [Cita::ESTADO_CONFIRMADA, Cita::ESTADO_SOLICITADA])->count();

        return [
            Stat::make('Solicitudes pendientes', $solicitudes)
                ->description('Citas por confirmar')
                ->url(CitaResource::getUrl('index', ['tableFilters[estado][value]' => Cita::ESTADO_SOLICITADA]))
                ->color($solicitudes > 0 ? 'warning' : 'success'),
            Stat::make('Leads nuevos', $leads)
                ->description('Sin contactar')
                ->url(LeadResource::getUrl('index', ['tableFilters[estado][value]' => Lead::ESTADO_NUEVO]))
                ->color($leads > 0 ? 'warning' : 'success'),
            Stat::make('Mensajes sin leer', $sinLeer)
                ->description('Hilos con actividad')
                ->url(HiloResource::getUrl('index'))
                ->color($sinLeer > 0 ? 'warning' : 'success'),
            Stat::make('Citas de hoy', $hoy)
                ->description(today()->format('d/m/Y'))
                ->url(CitaResource::getUrl('index'))
                ->color('primary'),
        ];
    }
}
