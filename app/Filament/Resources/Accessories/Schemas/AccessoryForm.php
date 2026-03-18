<?php

namespace App\Filament\Resources\Accessories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AccessoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('฿'),
            ]);
    }
}
