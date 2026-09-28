<?php

namespace App\Filament\Admin\Resources\Historial;

use App\Filament\Admin\Resources\Historial\Pages\CreateEntradaHistorial;
use App\Filament\Admin\Resources\Historial\Pages\EditEntradaHistorial;
use App\Filament\Admin\Resources\Historial\Pages\ListEntradasHistorial;
use App\Filament\Admin\Resources\Historial\Pages\ViewEntradaHistorial;
use App\Models\Cliente;
use App\Models\EntradaHistorial;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EntradaHistorialResource extends Resource
{
    protected static ?string $model = EntradaHistorial::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $modelLabel = 'Entrada del historial';

    protected static ?string $pluralModelLabel = 'Historial clínico';

    protected static ?string $navigationLabel = 'Historial clínico';

    protected static ?string $slug = 'historial';

    protected static ?int $navigationSort = 50;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('cliente_id')
                ->label('Cliente')
                ->options(fn (): array => Cliente::opcionesParaSelector())
                ->searchable()
                ->preload()
                ->required(),
            DatePicker::make('fecha')
                ->label('Fecha de la sesión')
                ->required()
                ->default(today())
                ->maxDate(today()),
            Textarea::make('motivo')
                ->label('Motivo de consulta')
                ->required()
                ->columnSpanFull(),
            Textarea::make('notas_sesion')
                ->label('Notas de sesión')
                ->columnSpanFull(),
            Textarea::make('plan_terapeutico')
                ->label('Plan terapéutico')
                ->columnSpanFull(),
            Toggle::make('visible_cliente')
                ->label('Visible para el cliente')
                ->default(false),
            Repeater::make('adjuntos')
                ->label('Adjuntos (PDF o imagen, máx. 5 MB)')
                ->relationship()
                ->schema([
                    FileUpload::make('ruta')
                        ->label('Fichero')
                        ->disk('privado')
                        ->directory('historial')
                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/webp'])
                        ->maxSize(5120)
                        ->required()
                        ->downloadable()
                        ->openable(),
                ])
                ->columnSpanFull()
                ->addActionLabel('Añadir adjunto'),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('cliente.user.name')->label('Cliente'),
            TextEntry::make('fecha')->label('Fecha')->date('d/m/Y'),
            TextEntry::make('motivo')->label('Motivo de consulta')->columnSpanFull(),
            TextEntry::make('notas_sesion')->label('Notas de sesión')->placeholder('—')->columnSpanFull(),
            TextEntry::make('plan_terapeutico')->label('Plan terapéutico')->placeholder('—')->columnSpanFull(),
            TextEntry::make('visible_cliente')
                ->label('Visible para el cliente')
                ->formatStateUsing(fn (bool $state) => $state ? 'Sí' : 'No'),
            RepeatableEntry::make('adjuntos')
                ->label('Adjuntos')
                ->columnSpanFull()
                ->placeholder('Sin adjuntos')
                ->schema([
                    TextEntry::make('nombre_original')
                        ->label('Fichero')
                        ->url(fn ($record) => route('adjuntos.descargar', $record), shouldOpenInNewTab: true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('cliente.user.name')->label('Cliente')->searchable()->sortable(),
                TextColumn::make('fecha')->label('Fecha')->date('d/m/Y')->sortable(),
                TextColumn::make('motivo')->label('Motivo')->limit(50),
                IconColumn::make('visible_cliente')->label('Visible')->boolean(),
                TextColumn::make('adjuntos_count')->label('Adjuntos')->counts('adjuntos'),
            ])
            ->defaultSort('fecha', 'desc')
            ->filters([
                SelectFilter::make('cliente_id')
                    ->label('Cliente')
                    ->options(fn (): array => Cliente::opcionesParaSelector())
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make()->label('Ver'),
                EditAction::make()->label('Editar'),
                DeleteAction::make()->label('Eliminar'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function completarMetadatosAdjuntos(EntradaHistorial $entrada): void
    {
        foreach ($entrada->adjuntos()->whereNull('nombre_original')->get() as $adjunto) {
            $disco = \Illuminate\Support\Facades\Storage::disk('privado');
            $adjunto->update([
                'nombre_original' => basename($adjunto->ruta),
                'mime' => $disco->exists($adjunto->ruta) ? $disco->mimeType($adjunto->ruta) : null,
                'tamano' => $disco->exists($adjunto->ruta) ? $disco->size($adjunto->ruta) : 0,
            ]);
        }
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEntradasHistorial::route('/'),
            'create' => CreateEntradaHistorial::route('/create'),
            'view' => ViewEntradaHistorial::route('/{record}'),
            'edit' => EditEntradaHistorial::route('/{record}/edit'),
        ];
    }
}
