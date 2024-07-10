<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getModelLabel(): string
    {
        return __('User');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Users');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Users');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->columns([
                        'default' => 1,
                        'sm' => 4,
                    ])
                    ->schema([
                        Forms\Components\FileUpload::make('profile_photo_path')
                            ->label('Photo')
                            ->translateLabel()
                            ->avatar()
                            ->circleCropper()
                            ->image()
                            ->directory('users')
                            ->imageEditor(),
                        Forms\Components\Hidden::make('profile_photo_url'),
                        Forms\Components\Fieldset::make()
                            ->columnStart([
                                'sm' => 2,
                            ])
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->translateLabel()
                                    ->string()
                                    ->required()
                                    ->autofocus()
                                    ->maxLength(255),
                                    Forms\Components\TextInput::make('email')
                                        ->translateLabel()
                                        ->email()
                                        ->unique(ignoreRecord: true)
                                        ->suffixIcon('heroicon-m-envelope')
                                        ->suffixIconColor('primary')
                                        ->required()
                                        ->maxLength(255)
                                        ->hint(fn(User $record): string => $record->email_verified_at === null ? __('Unverified') : __('Verified'))
                                        ->hintColor(fn(User $record): string => $record->email_verified_at === null ? 'danger' : 'success'),
                                Forms\Components\TextInput::make('password')
                                    ->translateLabel()
                                    ->password()
                                    ->suffixIcon('heroicon-m-key')
                                    ->suffixIconColor('primary')
                                    ->revealable()
                                    ->required()
                                    ->confirmed()
                                    ->hiddenOn('edit')
                                    ->same('password_confirmation')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('password_confirmation')
                                    ->translateLabel()
                                    ->password()
                                    ->suffixIcon('heroicon-m-key')
                                    ->suffixIconColor('primary')
                                    ->revealable()
                                    ->required()
                                    ->hiddenOn('edit')
                                    ->maxLength(255),
                                Forms\Components\Select::make('roles')
                                    ->relationship('roles', 'name')
                                    ->translateLabel()
                                    ->multiple()
                                    ->preload()
                                    ->native(false)
                                    ->required(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('profile_photo_url')
                    ->label('Photo')
                    ->translateLabel()
                    ->visible(true === \Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->translateLabel()
                    ->wrap()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->translateLabel()
                    ->wrap()
                    ->icon('heroicon-m-envelope')
                    ->iconColor('primary')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->translateLabel()
                    ->alignment(Alignment::Center)
                    ->badge()
                    ->sortable()
                    ->default(__('No role'))
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->color('info')
                    ->expandableLimitedList(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->label('Email verified')
                    ->translateLabel()
                    ->dateTime()
                    ->since(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created at')
                    ->translateLabel()
                    ->dateTime()
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated at')
                    ->translateLabel()
                    ->dateTime()
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('not-verified')
                    ->label('Unverified')
                    ->translateLabel()
                    ->toggle()
                    ->query(fn(Builder $query): Builder => $query->where('email_verified_at', null)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(function (Collection $records) {
                            // delete multiple
                            if (\Laravel\Jetstream\Jetstream::managesProfilePhotos()) {
                                foreach ($records as $record) {
                                    if ($record->profile_photo_path != null) {
                                        Storage::disk('public')->delete($record->profile_photo_path);
                                    }
                                }
                            }
                        }),
                ]),
            ])
            ->persistFiltersInSession()
            ->deferLoading()
            ->striped()
            ->defaultSort('name', 'asc')
            ->defaultPaginationPageOption(5);
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
