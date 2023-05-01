<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
use Filament\Forms;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make([

                    Forms\Components\TextInput::make('title')
                        ->autofocus()
                        ->required()
                        ->lazy()
                        ->afterStateUpdated(fn ($state, $set) => $set('slug', str($state)->slug()))
                        ->maxLength(255)
                        ->placeholder('enter title'),
                    Forms\Components\TextInput::make('slug')
                        ->required()->unique(ignoreRecord: true),
                    Forms\Components\Select::make('author_id')
                        ->relationship('author', 'name')
                        ->required(),
                    Forms\Components\Select::make('category_id')->relationship('category', 'name')->required(),
                    Forms\Components\RichEditor::make('content')
                        ->required()->columnSpan(2)
                        ->placeholder('enter content'),
                    Forms\Components\TagsInput::make('tags')->placeholder('enter tags')
                        ->suggestions([
                            'laravel', 'php', 'javascript', 'vue', 'react', 'tailwindcss', 'alpinejs', 'livewire'
                        ]),

                ])->columns(2)->columnSpan(2),
                Forms\Components\Card::make([
                    Forms\Components\FileUpload::make('image')->image(),
                    Forms\Components\DatePicker::make('published_at')
                        ->required(),
                    Forms\Components\Toggle::make('is_visible'),
                    Forms\Components\TextInput::make('seo_title')->maxLength(60),
                    Forms\Components\TextInput::make('seo_description')->maxLength(160),

                ])->columnSpan(1),
                Forms\Components\Card::make([
                    Forms\Components\Repeater::make('images')->schema([
                        Forms\Components\FileUpload::make('image')->image()->required(),
                        Forms\Components\TextInput::make('alt')->nullable()->maxLength(255)
                    ])->relationship()->label('Slider Images')->grid(2)
                ])->columnSpan('full')
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->circular(),
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('category.name'),
                Tables\Columns\TextColumn::make('author.name'),
                Tables\Columns\TextColumn::make('published_at')->since(),
                Tables\Columns\ToggleColumn::make('is_visible'),
                Tables\Columns\TagsColumn::make('tags')
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')->relationship('category', 'name'),
                Tables\Filters\Filter::make('is_visible')->query(fn ($query) => $query->where('is_visible', true))->toggle(),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('send_notification')->label('')
                    ->icon('heroicon-o-bell')
                    ->action(function ($record) {
                        Notification::make()
                            ->success()
                            ->title($record->title)
                            ->body(new HtmlString($record->content))
                            ->actions([
                                Action::make('edit')->url(PostResource::getUrl('edit', $record))->openUrlInNewTab(),
                            ])
                            ->send();
                    }),
                Tables\Actions\ReplicateAction::make()
                    ->excludeAttributes(['title'])
                    ->form([
                        Forms\Components\TextInput::make('title')
                            ->autofocus()
                            ->required()
                            ->lazy()
                            ->afterStateUpdated(fn ($state, $set) => $set('slug', str($state)->slug()))
                            ->maxLength(255)
                            ->placeholder('enter title'),
                        Forms\Components\TextInput::make('slug')
                            ->required()->unique(ignoreRecord: true),
                        Forms\Components\DatePicker::make('published_at')
                            ->required(),
                    ])
                    ->beforeReplicaSaved(function (Model $replica, array $data): void {
                        $replica->fill($data);
                    }),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('invisible')->action(function ($records) {
                    Post::query()->whereIn('id', $records->pluck('id')->toArray())->update(['is_visible' => false]);
                }),
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CommentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }

    public static ?String $recordTitleAttribute = 'title';

    // or

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->title;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'title',
            'content',
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
