<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Facades\Filament;
use Filament\Notifications\Auth\ResetPassword;
use Filament\Notifications\Auth\VerifyEmail;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\URL;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

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

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ($data['password_confirmation'] === $data['password']) {
            unset($data['password_confirmation']);
        }

        $data['password'] = bcrypt($data['password']);
        $data['profile_photo_url'] = $data['profile_photo_path'];

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
