<?php

namespace App\Filament\Admin\Resources\Leads;

use App\Filament\Admin\Resources\Leads\Pages\CreateLead;
use App\Filament\Admin\Resources\Leads\Pages\EditLead;
use App\Filament\Admin\Resources\Leads\Pages\ListLeads;
use App\Filament\Admin\Resources\Leads\Pages\ViewLead;
use App\Filament\Admin\Resources\Leads\RelationManagers\InteraccionesRelationManager;
use App\Models\Lead;
use App\Services\GestorClientes;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserPlus;

    protected static ?string $modelLabel = 'Lead';

    protected static ?string $pluralModelLabel = 'Leads';

    protected static ?string $navigationLabel = 'Leads';

    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nombre')->label('Nombre')->required()->maxLength(255),
            TextInput::make('email')->label('Email')->email()->required()->maxLength(255),
            TextInput::make('telefono')->label('Teléfono')->tel()->maxLength(50),
            Select::make('servicio_id')
                ->label('Servicio de interés')
                ->relationship('servicio', 'nombre')
                ->searchable()
                ->preload(),
            Textarea::make('mensaje')->label('Mensaje')->required()->columnSpanFull(),
            Toggle::make('consentimiento')->label('Consentimiento de privacidad')->default(false),
            Select::make('estado')->label('Estado')->options(Lead::estados())->required()->default(Lead::ESTADO_NUEVO),
            Select::make('origen')->label('Origen')->options(Lead::origenes())->required()->default(Lead::ORIGEN_MANUAL),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('nombre')->label('Nombre'),
            TextEntry::make('email')->label('Email'),
            TextEntry::make('telefono')->label('Teléfono')->placeholder('—'),
            TextEntry::make('servicio.nombre')->label('Servicio de interés')->placeholder('—'),
            TextEntry::make('mensaje')->label('Mensaje')->columnSpanFull(),
            TextEntry::make('estado')
                ->label('Estado')
                ->badge()
                ->formatStateUsing(fn (string $state) => Lead::estados()[$state] ?? $state),
            TextEntry::make('origen')
                ->label('Origen')
                ->formatStateUsing(fn (string $state) => Lead::origenes()[$state] ?? $state),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')->label('Nombre')->searchable()->sortable(),
                TextColumn::make('email')->label('Email')->searchable(),
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Lead::estados()[$state] ?? $state)
                    ->sortable(),
                TextColumn::make('origen')
                    ->label('Origen')
                    ->formatStateUsing(fn (string $state) => Lead::origenes()[$state] ?? $state),
                TextColumn::make('created_at')->label('Recibido')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('estado')->label('Estado')->options(Lead::estados()),
            ])
            ->recordActions([
                ViewAction::make()->label('Ver'),
                EditAction::make()->label('Editar'),
                self::accionConvertir(),
                DeleteAction::make()->label('Eliminar'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function accionConvertir(): Action
    {
        return Action::make('convertir')
            ->label('Convertir en cliente')
            ->icon('heroicon-o-arrow-path')
            ->color('success')
            ->visible(fn (Lead $record) => $record->estado !== Lead::ESTADO_CONVERTIDO)
            ->requiresConfirmation()
            ->modalHeading('Convertir en cliente')
            ->modalDescription(fn (Lead $record) => 'Se creará la cuenta de cliente para '.$record->nombre.' ('.$record->email.') y se le enviará un email para establecer su contraseña.')
            ->action(function (Lead $record) {
                try {
                    GestorClientes::crearDesdeLead($record);
                } catch (\RuntimeException $e) {
                    Notification::make()
                        ->title('No se ha podido convertir')
                        ->body($e->getMessage())
                        ->danger()
                        ->send();

                    return;
                }

                Notification::make()
                    ->title('Lead convertido en cliente')
                    ->body('Se ha enviado el email para establecer la contraseña.')
                    ->success()
                    ->send();
            });
    }

    public static function getRelations(): array
    {
        return [
            InteraccionesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLeads::route('/'),
            'create' => CreateLead::route('/create'),
            'view' => ViewLead::route('/{record}'),
            'edit' => EditLead::route('/{record}/edit'),
        ];
    }
}
