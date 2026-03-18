<?php

namespace App\Filament\Resources\Quotations\Pages;

use App\Filament\Resources\Quotations\QuotationResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListQuotations extends ListRecords
{
    protected static string $resource = QuotationResource::class;

    protected function getHeaderActions(): array
    {
        // Only sales users can create quotations
        if (auth()->check() && auth()->user()->role === 'sales') {
            return [
                Action::make('create')
                    ->label('Create Quotation')
                    ->icon('heroicon-o-plus-circle')
                    ->url(route('filament.admin.resources.quotations.create')),
            ];
        }
        
        return [];
    }
}
