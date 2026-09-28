<?php

namespace App\Filament\Admin\Resources\Clientes\RelationManagers;

use App\Filament\Admin\Resources\Historial\EntradaHistorialResource;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HistorialRelationManager extends RelationManager
{
    protected static string $relationship = 'entradasHistorial';

    protected static ?string $title = 'Historial clínico';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fecha')->label('Fecha')->date('d/m/Y')->sortable(),
                TextColumn::make('motivo')->label('Motivo')->limit(60),
                IconColumn::make('visible_cliente')->label('Visible')->boolean(),
            ])
            ->defaultSort('fecha', 'desc')
            ->recordActions([
                ViewAction::make()
                    ->label('Ver')
                    ->url(fn ($record) => EntradaHistorialResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
