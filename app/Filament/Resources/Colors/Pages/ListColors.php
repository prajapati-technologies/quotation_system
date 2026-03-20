<?php

namespace App\Filament\Resources\Colors\Pages;

use App\Filament\Resources\Colors\ColorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListColors extends ListRecords
{
    protected static string $resource = ColorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make('create_sub_color')
                ->label('Add Sub Color')
                ->icon('heroicon-o-swatch')
                ->color('success')
                ->url(fn (): string => static::getResource()::getUrl('create', ['is_sub_color' => '1'])),

            CreateAction::make('create_main_color')
                ->label('Add Main Color')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->url(fn (): string => static::getResource()::getUrl('create', ['is_sub_color' => '0'])),
        ];
    }
}
