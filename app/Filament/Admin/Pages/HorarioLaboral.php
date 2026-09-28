<?php

namespace App\Filament\Admin\Pages;

use App\Services\Horario;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class HorarioLaboral extends Page
{
    protected string $view = 'filament.admin.pages.horario-laboral';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static ?string $navigationLabel = 'Horario laboral';

    protected static ?string $title = 'Horario laboral';

    protected static ?int $navigationSort = 90;

    /** @var array<string, mixed> | null */
    public ?array $data = [];

    public function mount(): void
    {
        $relleno = [];
        foreach (Horario::segmentos() as $dia => $tramos) {
            $relleno['dia_'.$dia] = collect($tramos)
                ->map(fn ($t) => $t[0].'-'.$t[1])
                ->implode(', ');
        }

        $this->form->fill($relleno);
    }

    public function form(Schema $schema): Schema
    {
        $campos = [];
        foreach (Horario::nombresDias() as $dia => $nombre) {
            $campos[] = \Filament\Forms\Components\TextInput::make('dia_'.$dia)
                ->label($nombre)
                ->placeholder('Ej.: 09:00-14:00, 16:00-20:00 (vacío = cerrado)')
                ->helperText('Tramos separados por comas, formato HH:MM-HH:MM.')
                ->rules([
                    fn () => function (string $attribute, $value, \Closure $fail) {
                        foreach (self::trocear((string) $value) as $trozo) {
                            if (! preg_match('/^([01]\d|2[0-3]):[0-5]\d-([01]\d|2[0-3]):[0-5]\d$/', $trozo)) {
                                $fail('Formato no válido. Usa HH:MM-HH:MM separados por comas.');

                                return;
                            }
                            [$a, $b] = explode('-', $trozo);
                            if ($b <= $a) {
                                $fail('En cada tramo la hora de fin debe ser posterior a la de inicio.');

                                return;
                            }
                        }
                    },
                ]);
        }

        return $schema->components($campos)->statePath('data');
    }

    public function guardar(): void
    {
        $datos = $this->form->getState();
        $segmentos = [];
        for ($dia = 1; $dia <= 7; $dia++) {
            $segmentos[$dia] = array_map(
                fn ($t) => explode('-', $t),
                self::trocear((string) ($datos['dia_'.$dia] ?? ''))
            );
        }

        Horario::guardar($segmentos);

        Notification::make()->title('Horario guardado')->success()->send();
    }

    /** @return array<int, string> */
    public static function trocear(string $valor): array
    {
        return array_values(array_filter(array_map(
            fn ($t) => preg_replace('/\s+/', '', $t),
            explode(',', $valor)
        )));
    }
}
