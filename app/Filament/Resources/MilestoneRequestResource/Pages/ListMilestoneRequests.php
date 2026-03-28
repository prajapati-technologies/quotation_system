<?php

namespace App\Filament\Resources\MilestoneRequestResource\Pages;

use App\Filament\Resources\MilestoneRequestResource;
use Filament\Resources\Pages\ListRecords;

class ListMilestoneRequests extends ListRecords
{
    protected static string $resource = MilestoneRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
