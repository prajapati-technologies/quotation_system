<?php

namespace App\Filament\Resources\GlassFilms\Pages;

use App\Filament\Resources\GlassFilms\GlassFilmResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGlassFilm extends EditRecord
{
    protected static string $resource = GlassFilmResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
