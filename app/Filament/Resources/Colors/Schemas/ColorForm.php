<?php

namespace App\Filament\Resources\Colors\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ColorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('brand_id')
                    ->label('Brand')
                    ->options(\App\Models\Brand::pluck('name', 'id'))
                    ->afterStateHydrated(function (Select $component, $record) {
                        if ($record && $record->category) {
                            $component->state($record->category->brand_id);
                        }
                    })
                    ->searchable()
                    ->live()
                    ->required()
                    ->dehydrated(false),
                Select::make('category_id')
                    ->label('Category')
                    ->options(function (callable $get) {
                        $brandId = $get('brand_id');
                        if (!$brandId) {
                            return \App\Models\Category::pluck('name', 'id');
                        }
                        return \App\Models\Category::where('brand_id', $brandId)->pluck('name', 'id');
                    })
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $category = \App\Models\Category::find($state);
                            $set('sub_category_display', $category?->sub_category);
                        } else {
                            $set('sub_category_display', null);
                        }
                    }),
                TextInput::make('sub_category_display')
                    ->label('Sub Category')
                    ->placeholder('Select a category to see sub-category')
                    ->disabled()
                    ->dehydrated(false)
                    ->afterStateHydrated(function ($component, $record) {
                        if ($record && $record->category) {
                            $component->state($record->category->sub_category);
                        }
                    }),
                TextInput::make('name')
                    ->label('Color Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('code')
                    ->label('Color Code')
                    ->maxLength(255),
                Select::make('color_type')
                    ->options([
                        'NORMAL' => 'NORMAL',
                        'SPECIAL' => 'SPECIAL',
                    ])
                    ->required()
                    ->default('NORMAL')
                    ->live(),
                TextInput::make('additional_price')
                    ->label('Additional Price')
                    ->required()
                    ->numeric()
                    ->default(0.00)
                    ->prefix('฿')
                    ->disabled(fn(callable $get) => $get('color_type') === 'NORMAL')
                    ->dehydrated() // Ensure it's saved even if disabled
                    ->formatStateUsing(fn(string $state, callable $get) => $get('color_type') === 'NORMAL' ? 0 : $state),
            ]);
    }
}