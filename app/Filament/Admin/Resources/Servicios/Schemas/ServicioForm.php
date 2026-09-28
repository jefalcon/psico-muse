<?php

namespace App\Filament\Admin\Resources\Servicios\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ServicioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                Textarea::make('descripcion')
                    ->label('Descripción')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('duracion_minutos')
                    ->label('Duración (minutos)')
                    ->required()
                    ->numeric()
                    ->minValue(5)
                    ->maxValue(480),
                TextInput::make('precio')
                    ->label('Precio (€)')
                    ->numeric()
                    ->minValue(0)
                    ->placeholder('Opcional'),
                Toggle::make('activo')
                    ->label('Activo')
                    ->default(true),
                TextInput::make('orden')
                    ->label('Orden')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
