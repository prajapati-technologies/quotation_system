<?php

namespace App\Filament\Resources\Glasses\Pages;

use App\Filament\Resources\Glasses\GlassResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGlasses extends ListRecords
{
    protected static string $resource = GlassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
