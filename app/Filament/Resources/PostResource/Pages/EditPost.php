<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Models\Post;
use Filament\Actions\DeleteAction;
use Filament\Actions\LocaleSwitcher;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\EditRecord\Concerns\Translatable;
use Illuminate\Support\Facades\Storage;

class EditPost extends EditRecord
{
    use Translatable;

    protected static string $resource = PostResource::class;

    public $image = null;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            ViewAction::make(),
            DeleteAction::make()
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
        dd($data, $data['image'], $this->image);
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
