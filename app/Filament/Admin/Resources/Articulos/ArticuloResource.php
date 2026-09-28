<?php

namespace App\Filament\Admin\Resources\Articulos;

use App\Filament\Admin\Resources\Articulos\Pages\CreateArticulo;
use App\Filament\Admin\Resources\Articulos\Pages\EditArticulo;
use App\Filament\Admin\Resources\Articulos\Pages\ListArticulos;
use App\Models\Articulo;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ArticuloResource extends Resource
{
    protected static ?string $model = Articulo::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHeart;

    protected static ?string $modelLabel = 'Artículo de salud';

    protected static ?string $pluralModelLabel = 'Artículos de salud';

    protected static ?string $navigationLabel = 'Artículos';

    protected static ?int $navigationSort = 21;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('titulo')
                ->label('Título')
                ->required()
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(function ($operation, $state, $set) {
                    if ($operation === 'create') {
                        $set('slug', Str::slug((string) $state));
                    }
                }),
            TextInput::make('slug')
                ->label('Slug')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->helperText('Se genera solo desde el título, pero puedes editarlo.'),
            Select::make('categoria')
                ->label('Categoría')
                ->options(Articulo::categorias())
                ->required(),
            Textarea::make('extracto')
                ->label('Extracto')
                ->columnSpanFull(),
            RichEditor::make('cuerpo')
                ->label('Cuerpo')
                ->required()
                ->columnSpanFull(),
            FileUpload::make('imagen')
                ->label('Imagen')
                ->image()
                ->disk('public')
                ->directory('articulos')
                ->maxSize(2048)
                ->columnSpanFull(),
            Select::make('estado')
                ->label('Estado')
                ->options(Articulo::estados())
                ->required()
                ->default(Articulo::ESTADO_BORRADOR),
            DateTimePicker::make('publicado_en')
                ->label('Fecha de publicación')
                ->seconds(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('imagen')
                    ->label('')
                    ->disk('public')
                    ->circular(),
                TextColumn::make('titulo')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('categoria')
                    ->label('Categoría')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Articulo::categorias()[$state] ?? $state),
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Articulo::estados()[$state] ?? $state)
                    ->color(fn (string $state) => $state === Articulo::ESTADO_PUBLICADO ? 'success' : 'gray'),
                TextColumn::make('publicado_en')
                    ->label('Publicado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('publicado_en', 'desc')
            ->filters([
                SelectFilter::make('estado')->label('Estado')->options(Articulo::estados()),
                SelectFilter::make('categoria')->label('Categoría')->options(Articulo::categorias()),
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

    public static function getPages(): array
    {
        return [
            'index' => ListArticulos::route('/'),
            'create' => CreateArticulo::route('/create'),
            'edit' => EditArticulo::route('/{record}/edit'),
        ];
    }
}
