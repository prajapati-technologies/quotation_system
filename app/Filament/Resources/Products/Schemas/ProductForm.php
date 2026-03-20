<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 1. PRIMARY INFO
                Section::make('Product Primary Details')
                    ->description('Main identifying info.')
                    ->icon('heroicon-m-identification')
                    ->columns(1)
                    ->schema([
                        TextInput::make('name')
                            ->label('Product Name')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-m-pencil-square'),

                        Select::make('material_type_id')
                            ->label('Material Type')
                            ->options(\App\Models\MaterialType::with('material')->get()->mapWithKeys(function ($item) {
                                $materialName = $item->material->name ?? 'No Material';
                                return [$item->id => "{$materialName} - {$item->name}"];
                            }))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required()
                            ->afterStateUpdated(function ($set, $get, $state) {
                                if (!$state) {
                                    $set('colorPrices', []);
                                    return;
                                }

                                $mainColors = \App\Models\Color::where('material_type_id', $state)
                                    ->whereNull('parent_id')
                                    ->get();

                                $prices = $mainColors->map(function ($color) {
                                    return [
                                        'main_color_id' => $color->id,
                                        'price' => 0,
                                        'installation_price' => 0,
                                    ];
                                })->toArray();

                                $set('colorPrices', $prices);
                            }),
                    ]),

                // 2. PRICING REPEATER
                Section::make('Color-Specific Pricing & Shades')
                    ->description('Set individual rates for each available color.')
                    ->icon('heroicon-m-banknotes')
                    ->columns(1)
                    ->schema([
                        Repeater::make('colorPrices')
                            ->relationship('colorPrices')
                            ->label('Configurations')
                            ->columns(1)
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->schema([
                                Select::make('main_color_id')
                                    ->label('Main Color')
                                    ->options(\App\Models\Color::pluck('name', 'id'))
                                    ->disabled()
                                    ->dehydrated()
                                    ->prefixIcon('heroicon-m-swatch'),

                                Select::make('subColors')
                                    ->label('Sub Shades')
                                    ->relationship('subColors', 'name')
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->prefixIcon('heroicon-m-list-bullet')
                                    ->options(function (callable $get) {
                                        $mainId = $get('main_color_id');
                                        if (!$mainId) return [];
                                        return \App\Models\Color::where('parent_id', $mainId)->pluck('name', 'id');
                                    }),

                                TextInput::make('price')
                                    ->label('Unit Price (฿)')
                                    ->numeric()
                                    ->required()
                                    ->prefix('฿'),

                                TextInput::make('installation_price')
                                    ->label('Install Rate (฿)')
                                    ->numeric()
                                    ->required()
                                    ->prefix('฿'),
                            ])
                            ->itemLabel(fn(array $state): ?string => \App\Models\Color::find($state['main_color_id'] ?? null)?->name),
                    ]),

                // 3. MEDIA
                Section::make('Visual Documentation')
                    ->icon('heroicon-m-photo')
                    ->columns(1)
                    ->schema([
                        FileUpload::make('drawing_path')
                            ->label('Product Image')
                            ->image()
                            ->imageEditor()
                            ->directory('product-drawings')
                            ->disk('public')
                            ->visibility('public'),
                    ]),
            ]);
    }
}
