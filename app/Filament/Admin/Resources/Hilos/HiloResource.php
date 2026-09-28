<?php

namespace App\Filament\Admin\Resources\Hilos;

use App\Filament\Admin\Resources\Hilos\Pages\CreateHilo;
use App\Filament\Admin\Resources\Hilos\Pages\ListHilos;
use App\Filament\Admin\Resources\Hilos\Pages\ViewHilo;
use App\Models\Hilo;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HiloResource extends Resource
{
    protected static ?string $model = Hilo::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?string $modelLabel = 'Hilo de mensajes';

    protected static ?string $pluralModelLabel = 'Mensajes';

    protected static ?string $navigationLabel = 'Mensajes';

    protected static ?int $navigationSort = 60;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('cliente_id')
                ->label('Cliente')
                ->relationship('cliente.user', 'name')
                ->searchable()
                ->preload()
                ->required(),
            TextInput::make('asunto')
                ->label('Asunto')
                ->required()
                ->maxLength(255),
            Textarea::make('cuerpo')
                ->label('Mensaje')
                ->required()
                ->columnSpanFull(),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('cliente.user.name')->label('Cliente'),
            TextEntry::make('asunto')->label('Asunto'),
            RepeatableEntry::make('mensajes')
                ->label('Conversación')
                ->columnSpanFull()
                ->schema([
                    TextEntry::make('remitente.name')->label('De'),
                    TextEntry::make('created_at')->label('Fecha')->dateTime('d/m/Y H:i'),
                    TextEntry::make('cuerpo')->label('Mensaje')->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('cliente.user.name')->label('Cliente')->searchable()->sortable(),
                TextColumn::make('asunto')->label('Asunto')->searchable()->limit(50),
                TextColumn::make('no_leidos')
                    ->label('Sin leer')
                    ->getStateUsing(fn (Hilo $record) => $record->sinLeerPara(auth()->user()) ?: null)
                    ->badge()
                    ->color('warning')
                    ->placeholder('—'),
                TextColumn::make('updated_at')->label('Actividad')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->recordActions([
                ViewAction::make()->label('Abrir'),
                DeleteAction::make()->label('Eliminar'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function accionResponder(): Action
    {
        return Action::make('responder')
            ->label('Responder')
            ->icon('heroicon-o-paper-airplane')
            ->color('primary')
            ->schema([
                Textarea::make('cuerpo')->label('Mensaje')->required()->rows(4),
            ])
            ->action(function (Hilo $record, array $data) {
                \App\Services\GestorMensajes::responder($record, auth()->user(), $data['cuerpo']);
                $record->marcarLeidoPara(auth()->user());
                $record->touch();

                Notification::make()->title('Mensaje enviado')->success()->send();
            });
    }

    public static function accionEnvioMultiple(): Action
    {
        return Action::make('envio_multiple')
            ->label('Envío a varios')
            ->icon('heroicon-o-megaphone')
            ->color('gray')
            ->schema([
                Select::make('clientes')
                    ->label('Clientes')
                    ->multiple()
                    ->required()
                    ->searchable()
                    ->preload()
                    ->options(fn () => \App\Models\Cliente::with('user')->get()->pluck('user.name', 'id')),
                TextInput::make('asunto')->label('Asunto')->required()->maxLength(255),
                Textarea::make('cuerpo')->label('Mensaje')->required()->rows(4),
            ])
            ->modalDescription('Cada cliente recibirá el mensaje en su propio hilo individual. Ningún cliente verá a los demás destinatarios.')
            ->action(function (array $data) {
                $ids = \App\Services\GestorMensajes::envioMultiple(
                    array_map('intval', $data['clientes']),
                    $data['asunto'],
                    auth()->user(),
                    $data['cuerpo']
                );

                Notification::make()
                    ->title('Mensaje enviado a '.count($ids).' clientes')
                    ->success()
                    ->send();
            });
    }

    public static function getNavigationBadge(): ?string
    {
        $n = auth()->user()?->mensajesSinLeer() ?? 0;

        return $n > 0 ? (string) $n : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHilos::route('/'),
            'create' => CreateHilo::route('/create'),
            'view' => ViewHilo::route('/{record}'),
        ];
    }
}
