<?php

namespace App\Filament\Admin\Resources\Citas;

use App\Filament\Admin\Resources\Citas\Pages\CreateCita;
use App\Filament\Admin\Resources\Citas\Pages\EditCita;
use App\Filament\Admin\Resources\Citas\Pages\ListCitas;
use App\Filament\Admin\Resources\Citas\Pages\ViewCita;
use App\Models\Cita;
use App\Models\Cliente;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CitaResource extends Resource
{
    protected static ?string $model = Cita::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $modelLabel = 'Cita';

    protected static ?string $pluralModelLabel = 'Citas';

    protected static ?string $navigationLabel = 'Citas';

    protected static ?int $navigationSort = 70;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('cliente_id')
                ->label('Cliente')
                ->options(fn (): array => Cliente::opcionesParaSelector())
                ->searchable()
                ->preload()
                ->required(),
            Select::make('servicio_id')
                ->label('Servicio')
                ->relationship('servicio', 'nombre')
                ->searchable()
                ->preload()
                ->required()
                ->live(),
            DateTimePicker::make('inicio')
                ->label('Inicio')
                ->required()
                ->seconds(false)
                ->minutesStep(15),
            Select::make('estado')
                ->label('Estado')
                ->options(Cita::estados())
                ->required()
                ->default(Cita::ESTADO_CONFIRMADA),
            Textarea::make('comentario_cliente')
                ->label('Comentario del cliente')
                ->columnSpanFull(),
            Textarea::make('motivo_rechazo')
                ->label('Motivo del rechazo')
                ->columnSpanFull(),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('cliente.user.name')->label('Cliente'),
            TextEntry::make('servicio.nombre')->label('Servicio'),
            TextEntry::make('inicio')->label('Inicio')->dateTime('d/m/Y H:i'),
            TextEntry::make('fin')->label('Fin')->dateTime('d/m/Y H:i'),
            TextEntry::make('estado')
                ->label('Estado')
                ->badge()
                ->formatStateUsing(fn (string $state) => Cita::estados()[$state] ?? $state),
            TextEntry::make('comentario_cliente')->label('Comentario del cliente')->placeholder('—'),
            TextEntry::make('motivo_rechazo')->label('Motivo del rechazo')->placeholder('—'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('inicio')->label('Fecha')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('cliente.user.name')->label('Cliente')->searchable(),
                TextColumn::make('servicio.nombre')->label('Servicio'),
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Cita::estados()[$state] ?? $state)
                    ->color(fn (string $state) => match ($state) {
                        Cita::ESTADO_SOLICITADA => 'warning',
                        Cita::ESTADO_CONFIRMADA => 'success',
                        Cita::ESTADO_RECHAZADA, Cita::ESTADO_CANCELADA => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
            ])
            ->defaultSort('inicio')
            ->filters([
                SelectFilter::make('estado')->label('Estado')->options(Cita::estados()),
            ])
            ->recordActions([
                ViewAction::make()->label('Ver'),
                self::accionConfirmar(),
                self::accionRechazar(),
                self::accionReprogramar(),
                EditAction::make()->label('Editar'),
                DeleteAction::make()->label('Eliminar'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function accionConfirmar(): Action
    {
        return Action::make('confirmar')
            ->label('Confirmar')
            ->icon('heroicon-o-check')
            ->color('success')
            ->visible(fn (Cita $record) => $record->estado === Cita::ESTADO_SOLICITADA)
            ->requiresConfirmation()
            ->action(function (Cita $record) {
                try {
                    \App\Services\GestorCitas::confirmar($record);
                } catch (\RuntimeException $e) {
                    Notification::make()->title('No se ha podido confirmar')->body($e->getMessage())->danger()->send();

                    return;
                }

                Notification::make()->title('Cita confirmada')->success()->send();
            });
    }

    public static function accionRechazar(): Action
    {
        return Action::make('rechazar')
            ->label('Rechazar')
            ->icon('heroicon-o-x-mark')
            ->color('danger')
            ->visible(fn (Cita $record) => $record->estado === Cita::ESTADO_SOLICITADA)
            ->schema([
                Textarea::make('motivo')->label('Motivo del rechazo')->required(),
            ])
            ->action(function (Cita $record, array $data) {
                try {
                    \App\Services\GestorCitas::rechazar($record, $data['motivo']);
                } catch (\RuntimeException $e) {
                    Notification::make()->title('No se ha podido rechazar')->body($e->getMessage())->danger()->send();

                    return;
                }

                Notification::make()->title('Solicitud rechazada')->success()->send();
            });
    }

    public static function accionReprogramar(): Action
    {
        return Action::make('reprogramar')
            ->label('Reprogramar')
            ->icon('heroicon-o-clock')
            ->color('gray')
            ->visible(fn (Cita $record) => in_array($record->estado, [Cita::ESTADO_SOLICITADA, Cita::ESTADO_CONFIRMADA], true))
            ->schema([
                DateTimePicker::make('nuevo_inicio')->label('Nuevo inicio')->required()->seconds(false)->minutesStep(15),
            ])
            ->action(function (Cita $record, array $data) {
                try {
                    \App\Services\GestorCitas::reprogramar($record, \Carbon\Carbon::parse($data['nuevo_inicio']));
                } catch (\RuntimeException $e) {
                    Notification::make()->title('No se ha podido reprogramar')->body($e->getMessage())->danger()->send();

                    return;
                }

                Notification::make()->title('Cita reprogramada')->success()->send();
            });
    }

    public static function getNavigationBadge(): ?string
    {
        $n = Cita::where('estado', Cita::ESTADO_SOLICITADA)->count();

        return $n > 0 ? (string) $n : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCitas::route('/'),
            'create' => CreateCita::route('/create'),
            'view' => ViewCita::route('/{record}'),
            'edit' => EditCita::route('/{record}/edit'),
        ];
    }
}
