<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommentResource\Pages;
use App\Models\Comment;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    public static function getModelLabel(): string
    {
        return __('Comment');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Comments');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Content');
    }

    public static function getEloquentQuery(): Builder
        {
            return parent::getEloquentQuery()->orderBy('id', 'desc')->orderBy('comment_id', 'asc');
        }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->columns([
                        'default' => 1,
                        'sm' => 8,
                    ])
                    ->schema([
                        Forms\Components\Section::make()
                            ->columns([
                                'default' => 1,
                                'sm' => 5,
                            ])
                            ->columnSpan([
                                'sm' => 5,
                            ])
                            ->schema([
                                Forms\Components\Fieldset::make('Author')
                                    ->translateLabel()
                                    ->schema([
                                        Forms\Components\Placeholder::make('Name')
                                            ->content(fn(Comment $record): string => $record->user->name)
                                            ->label('Name')
                                            ->translateLabel(),
                                        Forms\Components\Placeholder::make('Email')
                                            ->content(fn(Comment $record): string => $record->user->email)
                                            ->label('Email')
                                            ->translateLabel(),
                                    ]),
                                Forms\Components\Fieldset::make('content')
                                    ->label('Comment')
                                    ->translateLabel()
                                    ->schema([
                                        Forms\Components\Placeholder::make('content')
                                            ->label('')
                                            ->translateLabel()
                                            ->content(fn(Comment $record): string => $record->content),
                                    ]),
                            ]),
                        Forms\Components\Section::make()
                            ->columns([
                                'default' => 1,
                                'sm' => 3,
                            ])
                            ->columnStart([
                                'default' => 1,
                                'sm' => 6,
                            ])
                            ->schema([
                                Forms\Components\ToggleButtons::make('status')
                                    ->translateLabel()
                                    ->boolean()
                                    ->grouped()
                                    ->inline()
                                    ->options([
                                        true => __('Accepted'),
                                        false => __('Rejected'),
                                    ])
                                    ->icons([
                                        true => 'heroicon-o-check-circle',
                                        false => 'heroicon-o-x-circle',
                                    ])
                                    ->colors([
                                        true => 'success',
                                        false => 'danger',
                                    ])
                                    ->columnSpanFull(),
                                Forms\Components\Placeholder::make('created_at')
                                    ->label('')
                                    ->content(fn(Comment $record): string => __('Sent on') . ': ' . (Carbon::parse($record->created_at)->isoFormat('D MMM YYYY hh:ss')))
                                    ->columnSpanFull(),
                                Forms\Components\Placeholder::make('post.title')
                                    ->label('')
                                    ->content(fn(Comment $record): string => __('In response to post') . ': ' . $record->post->title)
                                    ->columnSpanFull(),
                                Forms\Components\Placeholder::make('parent.user_id')
                                    ->label('')
                                    ->content(fn(Comment $record): string => ($record->comment_id >= 1 ? __('In response to user') . ' ' . $record->user->name : __('In response to no one')))
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Author')
                    ->translateLabel()
                    ->wrap()
                    ->sortable(),
                Tables\Columns\TextColumn::make('content')
                    ->label('Comment')
                    ->translateLabel()
                    ->wrap()
                    ->markdown()
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        return $state;
                    })
                    ->limit(50)
                    ->description(fn(Comment $record): string => ($record->comment_id !== null ? __('In response to') . ' ' . $record->parent->user->name : ''), position: 'above'),
                Tables\Columns\IconColumn::make('status')
                    ->label('Accepted')
                    ->translateLabel()
                    ->alignment(Alignment::Center)
                    ->boolean(),
                Tables\Columns\TextColumn::make('post.title')
                    ->label('In response to')
                    ->translateLabel()
                    ->wrap()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Sent on')
                    ->translateLabel()
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('Rejected')
                    ->label('Rejected')
                    ->translateLabel()
                    ->toggle()
                    ->query(fn(Builder $query): Builder => $query->where('status', false)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->persistFiltersInSession()
            ->deferLoading()
            ->striped()
            ->defaultPaginationPageOption(10);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComments::route('/'),
            'view' => Pages\ViewComment::route('/{record}'),
            'edit' => Pages\EditComment::route('/{record}/edit'),
        ];
    }
}
