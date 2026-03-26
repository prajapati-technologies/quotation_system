<?php

namespace App\Filament\Resources\QuotationMilestoneResource\Pages;

use App\Filament\Resources\QuotationMilestoneResource;
use Filament\Resources\Pages\ListRecords;

class ListQuotationMilestones extends ListRecords
{
    protected static string $resource = QuotationMilestoneResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
