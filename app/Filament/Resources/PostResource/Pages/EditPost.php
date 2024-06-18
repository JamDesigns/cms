<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Models\Post;
use Filament\Actions;
use Illuminate\Support\Facades\Storage;
use App\Filament\Resources\PostResource;
use Filament\Resources\Pages\EditRecord;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    public $image = null;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->after(function (Post $post) {
                    // delete single
                    if (!empty($post->image)) {
                        Storage::disk('public')->delete($post->image);
                    }
                }),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->image = $data['image'];

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (!empty($this->image) && $this->image !== $data['image']) {
            Storage::disk('public')->delete($this->image);
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
