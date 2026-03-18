<?php

namespace App\Filament\Resources\Glasses\Pages;

use App\Filament\Resources\Glasses\GlassResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGlass extends CreateRecord
{
    protected static string $resource = GlassResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
