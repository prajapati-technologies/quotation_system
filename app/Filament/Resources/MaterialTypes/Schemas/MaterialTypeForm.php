<?php

namespace App\Filament\Resources\MaterialTypes\Schemas;

use Filament\Schemas\Schema;

class MaterialTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('material_id')
                    ->relationship('material', 'name')
                    ->required(),
                \Filament\Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }
}
