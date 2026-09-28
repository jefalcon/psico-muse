<?php

namespace App\Filament\Admin\Resources\Clientes\RelationManagers;

use App\Filament\Admin\Resources\Citas\CitaResource;
use App\Models\Cita;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CitasRelationManager extends RelationManager
{
    protected static string $relationship = 'citas';

    protected static ?string $title = 'Citas';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('inicio')->label('Fecha')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('servicio.nombre')->label('Servicio'),
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Cita::estados()[$state] ?? $state),
            ])
            ->defaultSort('inicio', 'desc')
            ->recordActions([
                ViewAction::make()
                    ->label('Ver')
                    ->url(fn ($record) => CitaResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
