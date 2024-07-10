<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Models\User;
use Filament\Actions;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Auth\VerifyEmail;
use Filament\Notifications\Auth\ResetPassword;

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

    protected function afterCreate(): void
    {
        $user = $this->record;

        //send veryfication email
        $notification = new VerifyEmail();
        $notification->url = URL::temporarySignedRoute(
            'filament.' . Filament::getPanel()->getPath() . '.auth.email-verification.verify',
            now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ],
        );
        $user->notify($notification);

        //reset password
        $token = app('auth.password.broker')->createToken($user);
        $notification = new ResetPassword($token);
        //set panel for url
        $notification->url = filament()->getPanel('app')->getResetPasswordUrl($token, $user);
        $user->notify($notification);
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
