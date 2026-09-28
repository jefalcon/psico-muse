<?php

namespace App\Filament\Admin\Resources\Servicios\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ServiciosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('duracion_minutos')
                    ->label('Duración (min)')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('precio')
                    ->label('Precio (€)')
                    ->money('EUR', locale: 'es')
                    ->sortable(),
                IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean(),
                TextColumn::make('orden')
                    ->label('Orden')
                    ->numeric()
                    ->sortable(),
            ])
            ->defaultSort('orden')
            ->filters([
                TernaryFilter::make('activo')->label('Activo'),
            ])
            ->recordActions([
                EditAction::make()->label('Editar'),
                DeleteAction::make()->label('Eliminar'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
