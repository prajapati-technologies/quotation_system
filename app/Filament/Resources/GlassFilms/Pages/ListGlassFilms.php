<?php

namespace App\Filament\Resources\GlassFilms\Pages;

use App\Filament\Resources\GlassFilms\GlassFilmResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGlassFilms extends ListRecords
{
    protected static string $resource = GlassFilmResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
