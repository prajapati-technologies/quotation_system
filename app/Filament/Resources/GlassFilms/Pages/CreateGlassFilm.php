<?php

namespace App\Filament\Resources\GlassFilms\Pages;

use App\Filament\Resources\GlassFilms\GlassFilmResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGlassFilm extends CreateRecord
{
    protected static string $resource = GlassFilmResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
