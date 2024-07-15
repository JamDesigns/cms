<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Filament\Resources\UserResource\Pages\ViewUser;
use App\Models\User;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Laravel\Jetstream\Jetstream;

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
                Section::make()
                    ->columns([
                        'default' => 1,
                        'sm' => 4,
                    ])
                    ->schema([
                        FileUpload::make('profile_photo_path')
                            ->label('Photo')
                            ->translateLabel()
                            ->avatar()
                            ->circleCropper()
                            ->image()
                            ->directory('users')
                            ->imageEditor(),
                        Hidden::make('profile_photo_url'),
                        Fieldset::make()
                            ->columnStart([
                                'sm' => 2,
                            ])
                            ->schema([
                                TextInput::make('name')
                                    ->translateLabel()
                                    ->string()
                                    ->required()
                                    ->autofocus()
                                    ->maxLength(255),
                                TextInput::make('email')
                                    ->translateLabel()
                                    ->email()
                                    ->unique(ignoreRecord: true)
                                    ->suffixIcon('heroicon-m-envelope')
                                    ->suffixIconColor('primary')
                                    ->required()
                                    ->maxLength(255)
                                    ->hint(fn(?User $record): string => $record != null && $record->email_verified_at != null ? __('Verified') : __('Unverified'))
                                    ->hintColor(fn(?User $record): string => $record != null && $record->email_verified_at != null ? 'success' : 'danger'),
                                TextInput::make('password')
                                    ->translateLabel()
                                    ->password()
                                    ->suffixIcon('heroicon-m-key')
                                    ->suffixIconColor('primary')
                                    ->revealable()
                                    ->required()
                                    ->confirmed()
                                    ->visibleOn('create')
                                    ->same('password_confirmation')
                                    ->maxLength(255),
                                TextInput::make('password_confirmation')
                                    ->translateLabel()
                                    ->password()
                                    ->suffixIcon('heroicon-m-key')
                                    ->suffixIconColor('primary')
                                    ->revealable()
                                    ->required()
                                    ->visibleOn('create')
                                    ->maxLength(255),
                                Select::make('roles')
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
                ImageColumn::make('profile_photo_url')
                    ->label('Photo')
                    ->translateLabel()
                    ->visible(true === \Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    ->circular(),
                TextColumn::make('name')
                    ->translateLabel()
                    ->wrap()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->translateLabel()
                    ->wrap()
                    ->icon('heroicon-m-envelope')
                    ->iconColor('primary')
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->translateLabel()
                    ->alignment(Alignment::Center)
                    ->badge()
                    ->sortable()
                    ->default(__('No role'))
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->color('info')
                    ->expandableLimitedList(),
                TextColumn::make('email_verified_at')
                    ->label('Email verified')
                    ->translateLabel()
                    ->dateTime()
                    ->since(),
                TextColumn::make('created_at')
                    ->label('Created at')
                    ->translateLabel()
                    ->dateTime()
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Updated at')
                    ->translateLabel()
                    ->dateTime()
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('not-verified')
                    ->label('Unverified')
                    ->translateLabel()
                    ->toggle()
                    ->query(fn(Builder $query): Builder => $query->where('email_verified_at', null)),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->after(function (Collection $records) {
                            // delete multiple
                            if (Jetstream::managesProfilePhotos()) {
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view' => ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
