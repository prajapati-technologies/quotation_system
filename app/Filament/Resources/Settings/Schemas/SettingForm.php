<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText('Use "vat_percent" for the VAT setting.')
                    ->disabled(fn($record) => $record !== null), // Key should be fixed after creation
                TextInput::make('value')
                    ->required()
                    ->helperText('For "vat_percent", enter a numeric value (e.g., 7 for 7%).'),
            ]);
    }
}
