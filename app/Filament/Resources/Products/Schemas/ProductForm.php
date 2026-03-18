<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Product Management')
                    ->tabs([
                        Tab::make('Basic Details')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Product Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('e.g. Sliding Window 2 panels')
                                    ->columnSpanFull(),

                                FileUpload::make('drawing_path')
                                    ->label('Product Drawing / Image')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                                    ->disk('public')
                                    ->directory('product-drawings')
                                    ->visibility('public')
                                    ->maxSize(10240)
                                    ->columnSpanFull(),
                            ]),

                        Tab::make('Brand Pricing (Series Wise)')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Repeater::make('brandRates')
                                    ->relationship('brandRates')
                                    ->label('Manage Brand Rates')
                                    ->schema([
                                        Select::make('brand_id')
                                            ->label('Brand')
                                            ->relationship('brand', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->distinct()
                                            ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                                        TextInput::make('normal_price')
                                            ->label('Normal Rate')
                                            ->numeric()
                                            ->prefix('฿')
                                            ->required()
                                            ->placeholder('0.00'),

                                        TextInput::make('special_price')
                                            ->label('Special Rate')
                                            ->numeric()
                                            ->prefix('฿')
                                            ->required()
                                            ->placeholder('0.00'),

                                        TextInput::make('installation_price')
                                            ->label('Installation Rate')
                                            ->numeric()
                                            ->prefix('฿')
                                            ->required()
                                            ->default(0)
                                            ->placeholder('0.00'),
                                    ])
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->grid(2)
                                    ->itemLabel(fn(array $state): ?string => \App\Models\Brand::find($state['brand_id'])?->name ?? 'Set New Rate'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
