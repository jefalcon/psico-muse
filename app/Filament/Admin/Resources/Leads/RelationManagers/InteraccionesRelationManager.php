<?php

namespace App\Filament\Admin\Resources\Leads\RelationManagers;

use App\Models\Interaccion;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InteraccionesRelationManager extends RelationManager
{
    protected static string $relationship = 'interacciones';

    protected static ?string $title = 'Interacciones';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            DateTimePicker::make('fecha')
                ->label('Fecha')
                ->required()
                ->default(now())
                ->seconds(false),
            Select::make('canal')
                ->label('Canal')
                ->options(Interaccion::canales())
                ->required(),
            Textarea::make('nota')
                ->label('Nota')
                ->required()
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fecha')->label('Fecha')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('canal')
                    ->label('Canal')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Interaccion::canales()[$state] ?? $state),
                TextColumn::make('nota')->label('Nota')->limit(80),
            ])
            ->defaultSort('fecha', 'desc')
            ->headerActions([
                CreateAction::make()->label('Registrar interacción'),
            ])
            ->recordActions([
                EditAction::make()->label('Editar'),
                DeleteAction::make()->label('Eliminar'),
            ]);
    }
}
