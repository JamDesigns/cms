<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Post;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
// use RalphJSmit\Filament\SEO\SEO;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $recordRouteKeyName = 'slug';

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
                Forms\Components\Section::make()
                    ->columns([
                        'sm' => 9,
                        'md' => 12,
                    ])
                    ->schema([
                        Forms\Components\Fieldset::make()
                            ->schema([
                                Forms\Components\TextInput::make('title')
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
                                Forms\Components\TextInput::make('slug')
                                    ->translateLabel()
                                    ->readOnly()
                                    ->columnSpan('full'),
                                Forms\Components\RichEditor::make('body')
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
                        Forms\Components\Fieldset::make()
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('image')
                                    ->label('Image')
                                    ->translateLabel()
                                    ->image()
                                    ->disk('public')
                                    ->directory('posts')
                                    ->responsiveImages()
                                    ->conversion('jpg')
                                    ->imageEditor()
                                    ->columnSpan('full'),
                                Forms\Components\Select::make('category_id')
                                    ->translateLabel()
                                    ->relationship('category', 'name')
                                    ->native(false)
                                    ->required()
                                    ->columnSpan('full')
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
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
                                        Forms\Components\TextInput::make('slug')
                                            ->disabled()
                                            ->readOnly(),
                                        // Forms\Components\Fieldset::make('SEO')
                                        // ->schema([
                                        //     SEO::make(),
                                        // ])
                                        // ->hidden(! auth()->user()->hasAnyRole(['Super Admin', 'Admin', 'Editor'])),
                                    ]),
                                Forms\Components\Select::make('status')
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
                                Forms\Components\DateTimePicker::make('created_at')
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
                            // Forms\Components\Fieldset::make('SEO')
                            // ->schema([
                            //     SEO::make(),
                            // ])
                            // ->visible(auth()->user()->hasAnyRole(['Super Admin', 'Admin', 'Editor'])),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')
                    ->label('Image')
                    ->translateLabel()
                    ->defaultImageUrl(url('/storage/no-image.png')),
                Tables\Columns\TextColumn::make('title')
                    ->translateLabel()
                    ->wrap()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Author')
                    ->translateLabel()
                    ->wrap()
                    ->hidden(PostResource::hiddenUserName())
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->alignment(Alignment::Center)
                    ->translateLabel()
                    ->wrap()
                    ->color('info')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
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
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created at')
                    ->translateLabel()
                    ->date(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated at')
                    ->translateLabel()
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('draft')
                    ->label('Draft')
                    ->translateLabel()
                    ->toggle()
                    ->query(fn(Builder $query): Builder => $query->where('status', 'draft')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->after(function (Post $post) {
                        // delete single
                        Storage::disk('public')->delete($post->image);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
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
            RelationManagers\CommentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'view' => Pages\ViewPost::route('/{record}'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
