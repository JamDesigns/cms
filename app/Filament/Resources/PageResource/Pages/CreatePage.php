<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use Filament\Actions\LocaleSwitcher;
use Filament\Resources\Pages\CreateRecord;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Pages\CreateRecord\Concerns\TranslatableWithMedia;

class CreatePage extends CreateRecord
{
    use TranslatableWithMedia;

    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
        ];
    }


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
