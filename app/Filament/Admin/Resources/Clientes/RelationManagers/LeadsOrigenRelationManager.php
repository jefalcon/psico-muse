<?php

namespace App\Filament\Admin\Resources\Clientes\RelationManagers;

use App\Filament\Admin\Resources\Leads\LeadResource;
use App\Models\Lead;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LeadsOrigenRelationManager extends RelationManager
{
    protected static string $relationship = 'leadsConvertidos';

    protected static ?string $title = 'Lead de origen e interacciones';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')->label('Nombre'),
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Lead::estados()[$state] ?? $state),
                TextColumn::make('origen')
                    ->label('Origen')
                    ->formatStateUsing(fn (string $state) => Lead::origenes()[$state] ?? $state),
                TextColumn::make('interacciones_count')
                    ->label('Interacciones')
                    ->counts('interacciones'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Ver lead e interacciones')
                    ->url(fn ($record) => LeadResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
