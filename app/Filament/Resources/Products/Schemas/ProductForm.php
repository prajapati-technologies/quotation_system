<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    // STEP 1: PRIMARY INFO
                    Step::make('Product Primary Details')
                        ->description('Main Product Information')
                        ->icon('heroicon-m-identification')
                        ->schema([
                            Section::make()
                                ->columns(1)
                                ->schema([
                                    TextInput::make('name')
                                        ->label('Product Name')
                                        ->required()
                                        ->maxLength(255)
                                        ->prefixIcon('heroicon-m-pencil-square')
                                        ->placeholder('Enter product name...'),

                                    Select::make('material_type_id')
                                        ->label('Material Type Selection')
                                        ->options(\App\Models\MaterialType::with(['material', 'mainColors'])->get()->mapWithKeys(function ($item) {
                                            $materialName = $item->material->name ?? 'No Material';
                                            $colorCount = $item->mainColors->count();
                                            $label = "{$materialName} - {$item->name}";
                                            if ($colorCount === 0) {
                                                $label .= " (No colors defined)";
                                            }
                                            return [$item->id => $label];
                                        }))
                                        ->searchable()
                                        ->preload()
                                        ->live()
                                        ->required()
                                        ->prefixIcon('heroicon-m-cube')
                                        ->afterStateUpdated(function ($set, $get, $state, ?\App\Models\Product $record) {
                                            // Don't clear prices if we change back to the original material type during editing
                                            // BUT allow it if the current configurations are empty (to fix broken data)
                                            if ($record && $state == $record->material_type_id && !empty($get('colorPrices'))) {
                                                return;
                                            }

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
                                        })
                                        ->hintAction(
                                            Action::make('syncColors')
                                                ->label('Sync Base Colors')
                                                ->hidden(fn ($get) => !$get('material_type_id'))
                                                ->icon('heroicon-m-arrow-path')
                                                ->requiresConfirmation()
                                                ->tooltip('This will re-populate the configuration list with the latest colors for this material. Existing prices in this product will be lost.')
                                                ->action(function ($set, $get, $state) {
                                                    if (!$state) return;
                                                    
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
                                                    
                                                    \Filament\Notifications\Notification::make()
                                                        ->title('Colors Synced')
                                                        ->success()
                                                        ->send();
                                                })
                                        ),
                                ])
                        ]),

                    // STEP 2: PRICING & COLORS
                    Step::make('Color-Specific Pricing & Shades')
                        ->description('Set rates for each color variant.')
                        ->icon('heroicon-m-banknotes')
                        ->schema([
                            Section::make('Configuration & Pricing')
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
                                            Grid::make(2)
                                                ->schema([
                                                    Select::make('main_color_id')
                                                        ->label('Main Color')
                                                        ->options(\App\Models\Color::pluck('name', 'id'))
                                                        ->disabled()
                                                        ->dehydrated()
                                                        ->prefixIcon('heroicon-m-swatch'),

                                                    Select::make('subColors')
                                                        ->label('Choose Shades (Multi)')
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
                                                ]),

                                            Grid::make(2)
                                                ->schema([
                                                    TextInput::make('price')
                                                        ->label('Item Unit Price')
                                                        ->numeric()
                                                        ->required()
                                                        ->prefix('฿')
                                                        ->placeholder('0.00'),

                                                    TextInput::make('installation_price')
                                                        ->label('Item Install Rate')
                                                        ->numeric()
                                                        ->required()
                                                        ->prefix('฿')
                                                        ->placeholder('0.00'),
                                                ]),
                                        ])
                                        ->itemLabel(fn(array $state): ?string => \App\Models\Color::find($state['main_color_id'] ?? null)?->name),
                                ])
                        ]),

                    // STEP 3: DOCUMENTATION
                    Step::make('Technical Documentation')
                        ->description('Visual assets and drawings.')
                        ->icon('heroicon-m-photo')
                        ->schema([
                            Section::make('Product Drawing / Layout')
                                ->columns(1)
                                ->schema([
                                    FileUpload::make('drawing_path')
                                        ->label('Upload Product Drawing')
                                        ->image()
                                        ->imageEditor()
                                        ->directory('product-drawings')
                                        ->disk('public')
                                        ->visibility('public')
                                        ->hint('Accepted: JPG, PNG, WEBP.'),
                                ]),
                        ]),
                ])
                ->columnSpanFull()
                // Persistence settings if needed, but Filament usually handles this.
            ]);
    }
}
