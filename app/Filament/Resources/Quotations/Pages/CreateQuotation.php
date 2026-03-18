<?php

namespace App\Filament\Resources\Quotations\Pages;

use App\Filament\Resources\Quotations\QuotationResource;
use Filament\Resources\Pages\CreateRecord;

use Filament\Support\Enums\Width;

class CreateQuotation extends CreateRecord
{
    protected static string $resource = QuotationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getMaxContentWidth(): Width
    {
        return Width::Full;
    }

    public function getFormMaxWidth(): Width
    {
        return Width::Full;
    }
}
