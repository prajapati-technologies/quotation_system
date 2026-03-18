<?php

namespace App\Filament\Resources\Glasses\Pages;

use App\Filament\Resources\Glasses\GlassResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGlass extends EditRecord
{
    protected static string $resource = GlassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
