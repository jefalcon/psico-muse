<?php

namespace App\Filament\Admin\Resources\Servicios;

use App\Filament\Admin\Resources\Servicios\Pages\CreateServicio;
use App\Filament\Admin\Resources\Servicios\Pages\EditServicio;
use App\Filament\Admin\Resources\Servicios\Pages\ListServicios;
use App\Filament\Admin\Resources\Servicios\Schemas\ServicioForm;
use App\Filament\Admin\Resources\Servicios\Tables\ServiciosTable;
use App\Models\Servicio;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ServicioResource extends Resource
{
    protected static ?string $model = Servicio::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?string $modelLabel = 'Servicio';

    protected static ?string $pluralModelLabel = 'Servicios';

    protected static ?string $navigationLabel = 'Servicios';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return ServicioForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ServiciosTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServicios::route('/'),
            'create' => CreateServicio::route('/create'),
            'edit' => EditServicio::route('/{record}/edit'),
        ];
    }
}
