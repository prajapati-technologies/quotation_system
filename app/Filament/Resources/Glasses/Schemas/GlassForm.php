<?php

namespace App\Filament\Resources\Glasses\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GlassForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->maxLength(255),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Textarea::make('features')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                TextInput::make('price_per_sqm')
                    ->label('Price per Sqm')
                    ->required()
                    ->numeric()
                    ->default(0.00)
                    ->prefix('฿'),
                TextInput::make('thickness')
                    ->maxLength(255),
                TextInput::make('max_size')
                    ->maxLength(255),
            ]);
    }
}
