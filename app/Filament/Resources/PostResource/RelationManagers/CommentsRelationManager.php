<?php

namespace App\Filament\Resources\PostResource\RelationManagers;

use App\Models\Comment;
use Carbon\Carbon;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    public static function getModelLabel(): string
    {
        return __('Comment');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Comments');
    }


    public static function getEloquentQuery(): Builder
        {
            return parent::getEloquentQuery()->orderBy('id', 'desc')->orderBy('comment_id', 'asc');
        }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns([
                        'default' => 1,
                        'sm' => 8,
                    ])
                    ->schema([
                        Section::make()
                            ->columns([
                                'default' => 1,
                                'sm' => 5,
                            ])
                            ->columnSpan([
                                'sm' => 5,
                            ])
                            ->schema([
                                Fieldset::make('Author')
                                    ->translateLabel()
                                    ->schema([
                                        Placeholder::make('Name')
                                            ->content(fn(Comment $record): string => $record->user->name)
                                            ->label('Name')
                                            ->translateLabel(),
                                        Placeholder::make('Email')
                                            ->content(fn(Comment $record): string => $record->user->email)
                                            ->label('Email')
                                            ->translateLabel(),
                                    ]),
                                Fieldset::make('content')
                                    ->label('Comment')
                                    ->translateLabel()
                                    ->schema([
                                        Placeholder::make('content')
                                            ->label('')
                                            ->translateLabel()
                                            ->content(fn(Comment $record): string => $record->content),
                                    ]),
                            ]),
                        Section::make()
                            ->columns([
                                'default' => 1,
                                'sm' => 3,
                            ])
                            ->columnStart([
                                'default' => 1,
                                'sm' => 6,
                            ])
                            ->schema([
                                ToggleButtons::make('status')
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
                                Placeholder::make('created_at')
                                    ->label('')
                                    ->content(fn(Comment $record): string => __('Sent on') . ': ' . (Carbon::parse($record->created_at)->isoFormat('D MMM YYYY hh:ss')))
                                    ->columnSpanFull(),
                                Placeholder::make('post.title')
                                    ->label('')
                                    ->content(fn(Comment $record): string => __('In response to post') . ': ' . $record->post->title)
                                    ->columnSpanFull(),
                                Placeholder::make('parent.user_id')
                                    ->label('')
                                    ->content(fn(Comment $record): string => ($record->comment_id >= 1 ? __('In response to user') . ' ' . $record->user->name : __('In response to no one')))
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('Comments'))
            ->columns([
                TextColumn::make('user.name')
                    ->label('Author')
                    ->translateLabel()
                    ->wrap()
                    ->sortable(),
                TextColumn::make('content')
                    ->label('Comment')
                    ->translateLabel()
                    ->wrap()
                    ->markdown()
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        return $state;
                    })
                    ->limit(50)
                    ->description(fn(Comment $record): string => ($record->comment_id !== null ? __('In response to') . ' ' . $record->parent->user->name : ''), position: 'above'),
                IconColumn::make('status')
                    ->label('Accepted')
                    ->translateLabel()
                    ->alignment(Alignment::Center)
                    ->boolean(),
                TextColumn::make('post.title')
                    ->label('In response to')
                    ->translateLabel()
                    ->wrap()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Sent on')
                    ->translateLabel()
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Filter::make('Rejected')
                    ->label('Rejected')
                    ->translateLabel()
                    ->toggle()
                    ->query(fn(Builder $query): Builder => $query->where('status', false)),
            ])
            ->headerActions([
                //
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                //
            ])
            ->deferLoading()
            ->striped()
            ->extremePaginationLinks()
            ->defaultPaginationPageOption(10);
    }
}
