<?php

namespace App\Filament\Admin\Resources\Clientes;

use App\Filament\Admin\Resources\Clientes\Pages\CreateCliente;
use App\Filament\Admin\Resources\Clientes\Pages\EditCliente;
use App\Filament\Admin\Resources\Clientes\Pages\ListClientes;
use App\Filament\Admin\Resources\Clientes\Pages\ViewCliente;
use App\Models\Cliente;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClienteResource extends Resource
{
    protected static ?string $model = Cliente::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $modelLabel = 'Cliente';

    protected static ?string $pluralModelLabel = 'Clientes';

    protected static ?string $navigationLabel = 'Clientes';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nombre')
                ->label('Nombre')
                ->required()
                ->maxLength(255),
            TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->maxLength(255),
            TextInput::make('telefono')
                ->label('Teléfono')
                ->tel()
                ->maxLength(50),
            DatePicker::make('fecha_nacimiento')
                ->label('Fecha de nacimiento')
                ->maxDate(today()),
            Textarea::make('notas_internas')
                ->label('Notas internas')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->searchable(),
                TextColumn::make('fecha_nacimiento')
                    ->label('Nacimiento')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make()->label('Ver'),
                EditAction::make()->label('Editar'),
                DeleteAction::make()
                    ->label('Eliminar')
                    ->using(function (Cliente $record) {
                        // Borrar al usuario arrastra la ficha (onDelete cascade).
                        $record->user?->delete();
                        $record->delete();

                        return true;
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->using(function ($records) {
                            foreach ($records as $record) {
                                $record->user?->delete();
                                $record->delete();
                            }

                            return true;
                        }),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('user.name')->label('Nombre'),
            TextEntry::make('user.email')->label('Email'),
            TextEntry::make('telefono')->label('Teléfono')->placeholder('—'),
            TextEntry::make('fecha_nacimiento')->label('Fecha de nacimiento')->date('d/m/Y')->placeholder('—'),
            TextEntry::make('notas_internas')->label('Notas internas')->placeholder('—')->columnSpanFull(),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\HistorialRelationManager::class,
            RelationManagers\LeadsOrigenRelationManager::class,
            RelationManagers\CitasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClientes::route('/'),
            'create' => CreateCliente::route('/create'),
            'view' => ViewCliente::route('/{record}'),
            'edit' => EditCliente::route('/{record}/edit'),
        ];
    }
}
