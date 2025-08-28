<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BlogResource\Pages;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Tag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Blogs';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('title')
                ->required()
                ->maxLength(255)
                ->live()
                ->afterStateUpdated(fn($state, callable $set) => $set('slug', Str::slug($state))),

            TextInput::make('slug')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),

            Textarea::make('excerpt')
                ->rows(3)
                ->maxLength(500)
                ->helperText('Short description for list / SEO'),

            RichEditor::make('content')
                ->required()
                ->fileAttachmentsDisk('public')
                ->fileAttachmentsDirectory('blogs/content')
                ->columnSpanFull(),

            Select::make('category_id')
                ->label('Category')
                ->options(Category::query()->pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->nullable(),

            TextInput::make('link')
                ->label('External Link (opsional)')
                ->url()
                ->nullable()
                ->maxLength(255),

            Select::make('tags')
                ->label('Tags')
                ->multiple()
                ->options(Tag::query()->pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->saveRelationshipsUsing(function ($component, $state, $record) {
                    $record->tags()->sync($state ?? []);
                })
                ->afterStateHydrated(function ($component, $state, $record) {
                    $component->state($record?->tags()->pluck('id')->toArray() ?? []);
                }),

            FileUpload::make('image')
                ->image()
                ->maxSize(2048)
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->directory('blogs')
                ->disk('public')
                ->preserveFilenames()
                ->openable(true)
                ->visibility('public'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->disk('public')->label('Image')->height(50)->width(50),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('category.name')->label('Category')->sortable(),
                Tables\Columns\TextColumn::make('tags.name')->badge()->separator(', ')->label('Tags'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogs::route('/'),
            'create' => Pages\CreateBlog::route('/create'),
            'edit' => Pages\EditBlog::route('/{record}/edit'),
        ];
    }
}
