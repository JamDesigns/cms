<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    public $image = null;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->after(function (User $user) {
                    // delete single
                    if (!empty($user->profile_photo_path)) {
                        Storage::disk('public')->delete($user->profile_photo_path);
                    }
                }),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->image = $data['profile_photo_path'];

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (!empty($this->image) && $this->image !== $data['profile_photo_path']) {
            Storage::disk('public')->delete($this->image);
        }

        $data['profile_photo_url'] = $data['profile_photo_path'];

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
