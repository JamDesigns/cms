<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\LocaleSwitcher;
use Filament\Resources\Pages\EditRecord;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Pages\EditRecord\Concerns\TranslatableWithMedia;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Resource\Pages\Actions\CopyContentBlocksToLocalesAction;

class EditPage extends EditRecord
{
    use TranslatableWithMedia;

    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            CopyContentBlocksToLocalesAction::make(),
            DeleteAction::make(),
        ];
    }

    // protected function mutateFormDataBeforeSave(array $data): array
    // {
    //     // if (!empty($this->image) && $this->image !== $data['image']) {
    //     //     Storage::disk('public')->delete($this->image);
    //     // }

    //     return $data;
    // }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
