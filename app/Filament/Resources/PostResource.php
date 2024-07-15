<?php

namespace App\Filament\Resources;

use App\Models\Post;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\Alignment;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Fieldset;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Components\DateTimePicker;
use Filament\Resources\Concerns\Translatable;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\PostResource\Pages\EditPost;
use App\Filament\Resources\PostResource\Pages\ViewPost;
use App\Filament\Resources\PostResource\Pages\ListPosts;
use App\Filament\Resources\PostResource\Pages\CreatePost;
use App\Filament\Resources\PostResource\RelationManagers\CommentsRelationManager;

class PostResource extends Resource
{
    use Translatable;

    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $recordRouteKeyName = 'id';

    public static function hiddenUserName(): bool
    {
        $hiddenUserName = true;

        $userRoles = auth()->user()->roles;

        foreach ($userRoles as $role) {

            if (($role->name === 'Super Admin' || $role->name === 'Admin' || $role->name === 'Editor')) {
                $hiddenUserName = false;
                break;
            }
        }

        return $hiddenUserName;
    }

    public static function getModelLabel(): string
    {
        return __('Post');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Posts');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Content');
    }

    public static function getEloquentQuery(): Builder
    {
        if (auth()->user()->hasAnyRole(['Super Admin', 'Admin', 'Editor'])) {
            return parent::getEloquentQuery()->orderBy('created_at', 'DESC');
        } else {
            return parent::getEloquentQuery()->whereBelongsTo(auth()->user())->orderBy('created_at', 'DESC');
        }
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns([
                        'sm' => 9,
                        'md' => 12,
                    ])
                    ->schema([
                        Fieldset::make()
                            ->schema([
                                TextInput::make('title')
                                    ->translateLabel()
                                    ->string()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                        if (($get('slug') ?? '') !== Str::slug($old)) {
                                            return;
                                        }

                                        $set('slug', Str::slug($state));
                                    })
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan('full')
                                    ->autofocus(),
                                TextInput::make('slug')
                                    ->translateLabel()
                                    ->readOnly()
                                    ->columnSpan('full'),
                                RichEditor::make('body')
                                    ->label('Content')
                                    ->translateLabel()
                                    ->fileAttachmentsDirectory('posts')
                                    ->required()
                                    ->columnSpan('full'),
                            ])
                            ->columnSpan([
                                'sm' => 6,
                                'md' => 8,
                            ]),
                        Fieldset::make()
                            ->schema([
                                FileUpload::make('image')
                                    ->label('Image')
                                    ->translateLabel()
                                    ->image()
                                    ->disk('public')
                                    ->directory('posts')
                                    ->imageEditor()
                                    ->columnSpan('full'),
                                Select::make('category_id')
                                    ->translateLabel()
                                    ->relationship('category', 'name')
                                    ->getOptionLabelFromRecordUsing(
                                        fn (Model $record) => $record->name
                                    )
                                    ->native(false)
                                    ->preload()
                                    ->searchable()
                                    ->required()
                                    ->columnSpan('full')
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->translateLabel()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                                if (($get('slug') ?? '') !== Str::slug($old)) {
                                                    return;
                                                }

                                                $set('slug', Str::slug($state));
                                            })
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('slug')
                                            ->disabled()
                                            ->readOnly(),
                                    ]),
                                Select::make('status')
                                    ->translateLabel()
                                    ->options(fn(): array =>
                                        (auth()->user()->hasAnyRole(['Super Admin', 'Admin', 'Editor'])) ?
                                            [
                                                __('In process') => [
                                                'draft' => __('Draft'),
                                                'reviewing' => __('Reviewing'),
                                                ],
                                                __('Reviewed') => [
                                                'published' => __('Published'),
                                                'rejected' => __('Rejected'),
                                                ],
                                            ]
                                            :
                                            [
                                                __('In process') => [
                                                'draft' => __('Draft'),
                                                ],
                                                __('Reviewed') => [
                                                'published' => __('Published'),
                                                ],
                                            ]
                                        )
                                    ->default('draft')
                                    ->live(onBlur : true)
                                    ->native(false)
                                    ->required(),
                                DateTimePicker::make('created_at')
                                    ->label('Published')
                                    ->translateLabel()
                                    ->hidden(fn(Get $get): bool => $get('status') !== 'published')
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->closeOnDateSelection()
                                    ->default(now()),
                            ])
                            ->columnSpan([
                                'sm' => 3,
                                'md' => 4,
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Image')
                    ->translateLabel()
                    ->defaultImageUrl(url('/images/no-image.png')),
                TextColumn::make('title')
                    ->translateLabel()
                    ->wrap()
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Author')
                    ->translateLabel()
                    ->wrap()
                    ->hidden(PostResource::hiddenUserName())
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->alignment(Alignment::Center)
                    ->translateLabel()
                    ->wrap()
                    ->color('info')
                    ->badge()
                    ->sortable(),
                TextColumn::make('status')
                    ->translateLabel()
                    ->alignment(Alignment::Center)
                    ->formatStateUsing(fn (string $state): string => __("{$state}"))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'warning',
                        'reviewing' => 'info',
                        'published' => 'success',
                        'rejected' => 'danger',
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created at')
                    ->translateLabel()
                    ->date(),
                TextColumn::make('updated_at')
                    ->label('Updated at')
                    ->translateLabel()
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('draft')
                    ->label('Draft')
                    ->translateLabel()
                    ->toggle()
                    ->query(fn(Builder $query): Builder => $query->where('status', 'draft')),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->after(function (Post $post) {
                        // delete single
                        Storage::disk('public')->delete($post->image);
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->after(function (Collection $records) {
                            // delete multiple
                            foreach ($records as $record) {
                                if ($record->image != null) {
                                    Storage::disk('public')->delete($record->image);
                                }
                            }
                        }),
                ]),
            ])
            ->persistFiltersInSession()
            ->deferLoading()
            ->striped()
            ->defaultPaginationPageOption(5);
    }

    public static function getRelations(): array
    {
        return [
            CommentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'view' => ViewPost::route('/{record:id}'),
            'edit' => EditPost::route('/{record:id}/edit'),
        ];
    }
}
