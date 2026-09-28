<?php

namespace App\Filament\Admin\Resources\Posts;

use App\Filament\Admin\Resources\Posts\Pages\CreatePost;
use App\Filament\Admin\Resources\Posts\Pages\EditPost;
use App\Filament\Admin\Resources\Posts\Pages\ListPosts;
use App\Models\Post;
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

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static ?string $modelLabel = 'Entrada del blog';

    protected static ?string $pluralModelLabel = 'Blog';

    protected static ?string $navigationLabel = 'Blog';

    protected static ?int $navigationSort = 20;

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
                ->directory('blog')
                ->maxSize(2048)
                ->columnSpanFull(),
            Select::make('estado')
                ->label('Estado')
                ->options(Post::estados())
                ->required()
                ->default(Post::ESTADO_BORRADOR),
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
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Post::estados()[$state] ?? $state)
                    ->color(fn (string $state) => $state === Post::ESTADO_PUBLICADO ? 'success' : 'gray'),
                TextColumn::make('publicado_en')
                    ->label('Publicado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('publicado_en', 'desc')
            ->filters([
                SelectFilter::make('estado')->label('Estado')->options(Post::estados()),
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
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'edit' => EditPost::route('/{record}/edit'),
        ];
    }
}
