<?php

namespace App\Filament\Resources\Colors\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Schema;

class ColorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Persistence of the mode (Main or Sub)
                Hidden::make('is_sub_color')
                    ->default(fn () => request()->query('is_sub_color', '0'))
                    ->dehydrated(false),

                Select::make('material_type_id')
                    ->label('Material Type')
                    ->options(\App\Models\MaterialType::with('material')->get()->mapWithKeys(function ($item) {
                        $materialName = $item->material->name ?? 'No Material';
                        return [$item->id => "{$materialName} - {$item->name}"];
                    }))
                    ->searchable()
                    ->live()
                    ->required(),

                Select::make('parent_id')
                    ->label('Main Color')
                    ->placeholder('Select the Main Color')
                    ->options(function (callable $get) {
                        $materialTypeId = $get('material_type_id');
                        if (!$materialTypeId) {
                            return [];
                        }
                        return \App\Models\Color::where('material_type_id', $materialTypeId)
                            ->whereNull('parent_id')
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->visible(fn ($get, $record) => $get('is_sub_color') === '1' || ($record && $record->parent_id !== null))
                    ->required(fn ($get, $record) => $get('is_sub_color') === '1' || ($record && $record->parent_id !== null))
                    ->live(),

                TextInput::make('name')
                    ->label(fn ($get, $record) => ($get('is_sub_color') === '1' || ($record && $record->parent_id !== null)) ? 'Sub Color Name' : 'Main Color Name')
                    ->placeholder('e.g. Matt Black, Crystal White')
                    ->required()
                    ->maxLength(255),

                TextInput::make('code')
                    ->label('Color Code')
                    ->placeholder('e.g. #000000 or BL-01')
                    ->maxLength(255),

                TextInput::make('additional_price')
                    ->label('Additional Price')
                    ->numeric()
                    ->default(0)
                    ->prefix('฿')
                    ->required(),
            ]);
    }
}