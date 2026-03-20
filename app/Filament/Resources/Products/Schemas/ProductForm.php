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
                Grid::make(3)
                    ->schema([
                        // Left Column (Basic Info & Pricing)
                        Group::make()
                            ->schema([
                                Section::make('General Information')
                                    ->icon('heroicon-o-pencil-square')
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Product Name')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('e.g. Sliding Window 2 panels'),

                                        Select::make('material_type_id')
                                            ->label('Material Type')
                                            ->options(\App\Models\MaterialType::with('material')->get()->mapWithKeys(function ($item) {
                                                $materialName = $item->material->name ?? 'No Material';
                                                return [$item->id => "{$materialName} - {$item->name}"];
                                            }))
                                            ->searchable()
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
                                    ])->columns(2),

                                Section::make('Available Colors & Pricing')
                                    ->icon('heroicon-o-swatch')
                                    ->description('Enter specific prices for each and optional sub-colors.')
                                    ->schema([
                                        Repeater::make('colorPrices')
                                            ->relationship('colorPrices')
                                            ->label('Configurations')
                                            ->schema([
                                                Grid::make(3)
                                                    ->schema([
                                                        Select::make('main_color_id')
                                                            ->label('Main Color')
                                                            ->options(\App\Models\Color::pluck('name', 'id'))
                                                            ->disabled()
                                                            ->dehydrated()
                                                            ->columnSpan(1),

                                                        Select::make('subColors')
                                                            ->label('Sub Shades (Multi)')
                                                            ->relationship('subColors', 'name')
                                                            ->multiple()
                                                            ->searchable()
                                                            ->preload()
                                                            ->options(function (callable $get) {
                                                                $mainId = $get('main_color_id');
                                                                if (!$mainId) return [];
                                                                return \App\Models\Color::where('parent_id', $mainId)->pluck('name', 'id');
                                                            })
                                                            ->columnSpan(2),

                                                        TextInput::make('price')
                                                            ->label('Unit Price')
                                                            ->numeric()
                                                            ->prefix('฿')
                                                            ->required(),

                                                        TextInput::make('installation_price')
                                                            ->label('Install Rate')
                                                            ->numeric()
                                                            ->prefix('฿')
                                                            ->required(),
                                                    ]),
                                            ])
                                            ->addable(false)
                                            ->deletable(false)
                                            ->reorderable(false)
                                            ->columnSpanFull()
                                            ->itemLabel(fn(array $state): ?string => \App\Models\Color::find($state['main_color_id'])?->name),
                                    ]),
                            ])->columnSpan(2),

                        // Right Sidebar (Media & Settings)
                        Group::make()
                            ->schema([
                                Section::make('Visual Assets')
                                    ->icon('heroicon-o-camera')
                                    ->schema([
                                        FileUpload::make('drawing_path')
                                            ->label('Product Drawing')
                                            ->image()
                                            ->imageEditor()
                                            ->disk('public')
                                            ->directory('product-drawings')
                                            ->visibility('public')
                                            ->maxSize(10240),
                                    ]),

                                Section::make('Status')
                                    ->icon('heroicon-o-check-badge')
                                    ->schema([
                                        \Filament\Forms\Components\Placeholder::make('info')
                                            ->content('Click "Create" only after verifying all color prices.')
                                    ]),
                            ])->columnSpan(1),
                    ]),
            ]);
    }
}
