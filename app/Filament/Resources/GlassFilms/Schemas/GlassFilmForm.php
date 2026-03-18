<?php

namespace App\Filament\Resources\GlassFilms\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GlassFilmForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                TextInput::make('price_per_sqm')
                    ->required()
                    ->numeric()
                    ->default(0.00)
                    ->prefix('฿'),
            ]);
    }
}
