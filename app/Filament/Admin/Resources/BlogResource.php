<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BlogResource\Pages;
use App\Models\Blog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Blogs';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('link')
                ->required()
                ->url()
                // ->nullable()
                ->maxLength(255),

            Forms\Components\Textarea::make('description')
                ->required()
                ->rows(4),

            FileUpload::make('image')
                ->required()
                ->image()
                ->maxSize(2048)
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->directory('blogs')
                ->disk('public')
                ->preserveFilenames()
                ->openable(true)
                ->visibility('public'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('title')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('description')
                ->limit(50),

            Tables\Columns\ImageColumn::make('image')
                ->disk('public')
                ->label('Image')
                ->height(50)
                ->width(50)
                ->url(fn($record) => $record->image ? asset('storage/' . $record->image) : null),

            Tables\Columns\TextColumn::make('link')
                ->url(fn($record) => $record->link, true) // true = buka di tab baru
                ->label('Link'),

            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable(),
        ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
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
