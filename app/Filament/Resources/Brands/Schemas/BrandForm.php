<?php

namespace App\Filament\Resources\Brands\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BrandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('material_id')
                    ->relationship('material', 'name')
                    ->required(),
                TextInput::make('name')
                    ->required(),
            ]);
    }
}
