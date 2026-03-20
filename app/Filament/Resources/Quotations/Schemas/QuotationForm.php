<?php

namespace App\Filament\Resources\Quotations\Schemas;

use App\Models\Color;
use App\Models\Glass;
use App\Models\MaterialType;
use App\Models\Product;
use App\Models\Accessory;
use App\Models\ProductColorPrice;
use App\Services\PricingService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Illuminate\Support\Facades\Auth;

class QuotationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // SECTION 1: HEADER
                Section::make('Quotation Information')
                    ->description('Customer details and project assignment.')
                    ->icon('heroicon-o-document-text')
                    ->collapsible()
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(['default' => 1, 'sm' => 2, 'lg' => 5])
                            ->schema([
                                Placeholder::make('quotation_number')
                                    ->label('Quotation No')
                                    ->content(fn($record) => $record?->quotation_number ?? 'NEW'),

                                Placeholder::make('customer_number')
                                    ->label('Customer No')
                                    ->content(fn($get) => \App\Models\Customer::find($get('customer_id'))?->customer_number ?? 'N/A'),

                                Select::make('customer_id')
                                    ->relationship('customer', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->label('Customer')
                                    ->prefixIcon('heroicon-o-user')
                                    ->disabled(fn(callable $get) => $get('status') !== 'Draft' || auth()->user()->role === 'admin'),

                                Select::make('project_id')
                                    ->label('Project')
                                    ->relationship('project', 'name', modifyQueryUsing: fn($query) => auth()->user()->role === 'sales' ? $query->whereHas('customer', fn($q) => $q->where('user_id', auth()->id())) : $query)
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->prefixIcon('heroicon-o-briefcase')
                                    ->disabled(fn(callable $get) => $get('status') !== 'Draft' || auth()->user()->role === 'admin'),

                                DatePicker::make('quotation_date')
                                    ->label('Date')
                                    ->default(now())
                                    ->required()
                                    ->prefixIcon('heroicon-o-calendar')
                                    ->disabled(fn(callable $get) => $get('status') !== 'Draft' || auth()->user()->role === 'admin'),

                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'Draft' => 'Draft',
                                        'Approved' => 'Approved',
                                        'Production' => 'Production',
                                        'Completed' => 'Completed',
                                        'Rejected' => 'Rejected',
                                    ])
                                    ->required()
                                    ->default('Draft')
                                    ->disabled(fn() => auth()->user()->role === 'sales')
                                    ->dehydrated()
                                    ->prefixIcon('heroicon-o-check-circle'),

                                TextInput::make('sales_person')
                                    ->label('Sales Person')
                                    ->afterStateHydrated(fn($component, $record) => $component->state($record?->project?->customer?->user?->name ?? auth()->user()->name))
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->prefixIcon('heroicon-o-identification')
                                    ->columnSpan(['lg' => 1]),
                            ]),
                    ]),

                // SECTION 2: ITEMS
                Repeater::make('items')
                    ->label('Line Items')
                    ->hiddenLabel()
                    ->relationship()
                    ->maxWidth(\Filament\Support\Enums\Width::Full)
                    ->columnSpanFull()
                    ->addable(fn(callable $get) => auth()->user()->role === 'sales' && $get('status') === 'Draft')
                    ->disabled(fn(callable $get) => auth()->user()->role === 'admin' || $get('status') !== 'Draft')
                    ->columns(4)
                    ->schema([
                        // Product Details
                        Placeholder::make('product_details_label')
                            ->label('Product Details')
                            ->content('1) Material → 2) Type → 3) Product')
                            ->extraAttributes(['class' => 'font-bold text-sm text-gray-500 mb-1 border-b pb-1'])
                            ->columnSpanFull(),

                        Select::make('material_id')
                            ->label('Material')
                            ->relationship('material', 'name')
                            ->required()
                            ->live()
                            ->prefixIcon('heroicon-o-cube-transparent')
                            ->afterStateUpdated(function (callable $set) {
                                $set('material_type_id', null);
                                $set('product_id', null);
                                $set('color_id', null);
                                $set('sub_color_id', null);
                            }),

                        Select::make('material_type_id')
                            ->label('Material Type')
                            ->options(fn(callable $get) => MaterialType::where('material_id', $get('material_id'))->pluck('name', 'id'))
                            ->required()
                            ->live()
                            ->visible(fn(callable $get) => filled($get('material_id')))
                            ->afterStateUpdated(fn(callable $set) => $set('product_id', null)),

                        Select::make('product_id')
                            ->label('Product')
                            ->options(fn(callable $get) => Product::where('material_type_id', $get('material_type_id'))->pluck('name', 'id'))
                            ->required()
                            ->live()
                            ->searchable()
                            ->preload()
                            ->visible(fn(callable $get) => filled($get('material_type_id')))
                            ->afterStateUpdated(function (callable $set) {
                                $set('color_id', null);
                                $set('sub_color_id', null);
                            }),

                        Placeholder::make('product_image_preview')
                            ->label('Product Drawing')
                            ->visible(fn(callable $get) => filled($get('product_id')))
                            ->columnSpanFull()
                            ->content(function (callable $get) {
                                $productId = $get('product_id');
                                if (!$productId) return '';
                                $product = Product::find($productId);
                                if (!$product || !$product->drawing_path) return 'No drawing';
                                $url = asset('storage/' . $product->drawing_path);
                                return new \Illuminate\Support\HtmlString('<img src="'.$url.'" class="h-32 rounded border bg-white mx-auto shadow-sm"/>');
                            }),

                        // Dimensions
                        TextInput::make('width')->label('Width (mm)')->numeric()->default(1000)->required()->live(),
                        TextInput::make('height')->label('Height (mm)')->numeric()->default(1000)->required()->live(),
                        TextInput::make('quantity')->label('Qty')->numeric()->default(1)->required()->live(),
                        TextInput::make('classification')->label('Class')->default('NORMAL')->disabled()->dehydrated(),

                        // Finishes & Options
                        Placeholder::make('finishes_label')
                            ->label('Finishes & Options')
                            ->content('Main Color → Sub Color → Glass')
                            ->extraAttributes(['class' => 'font-bold text-sm text-gray-500 mb-1 border-b pb-1 mt-4'])
                            ->columnSpanFull(),

                        Select::make('color_id') 
                            ->label('Main Color')
                            ->options(function (callable $get) {
                                $productId = $get('product_id');
                                if (!$productId) return [];
                                return ProductColorPrice::where('product_id', $productId)
                                    ->join('colors', 'product_color_prices.main_color_id', '=', 'colors.id')
                                    ->pluck('colors.name', 'colors.id');
                            })
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                $set('sub_color_id', null);
                                self::updatePrices($set, $get);
                            }),

                        Select::make('sub_color_id')
                            ->label('Sub Color')
                            ->options(function (callable $get) {
                                $productId = $get('product_id');
                                $mainColorId = $get('color_id');
                                if (!$productId || !$mainColorId) return [];
                                
                                $priceRecord = ProductColorPrice::where('product_id', $productId)
                                    ->where('main_color_id', $mainColorId)
                                    ->first();
                                
                                return $priceRecord ? $priceRecord->subColors()->pluck('name', 'colors.id') : [];
                            })
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn($set, $get) => self::updatePrices($set, $get)),

                        Select::make('glass_id')
                            ->label('Glass Type')
                            ->options(Glass::pluck('name', 'id'))
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn($set, $get) => self::updatePrices($set, $get)),

                        TextInput::make('discount_amount')
                            ->label('Item Discount (฿)')
                            ->numeric()
                            ->default(0)
                            ->live()
                            ->afterStateUpdated(fn($set, $get) => self::updatePrices($set, $get)),

                        CheckboxList::make('accessories')
                            ->label('Accessories')
                            ->options(Accessory::pluck('name', 'id'))
                            ->live()
                            ->columns(2)
                            ->columnSpan(3)
                            ->afterStateUpdated(fn($set, $get) => self::updatePrices($set, $get)),

                        TextInput::make('price')->label('Item Total')->numeric()->readOnly()->dehydrated()->prefix('฿'),
                    ])
                    ->afterStateUpdated(fn($set, $get) => self::updatePrices($set, $get)),

                // SECTION 3: FOOTER
                Section::make('Total Financials')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('total_goods')->label('Goods Total')->readOnly()->prefix('฿'),
                                TextInput::make('installation_total')->label('Installation Total')->readOnly()->prefix('฿'),
                                TextInput::make('total_price')->label('Total (Before VAT)')->readOnly()->prefix('฿'),
                                
                                TextInput::make('vat_total')
                                    ->label('VAT (' . \App\Models\Setting::get('vat_percent', 7) . '%)')
                                    ->readOnly()
                                    ->prefix('฿'),
                                    
                                TextInput::make('final_price')->label('Grand Total')->readOnly()->prefix('฿')
                                    ->extraAttributes(['class' => 'font-bold text-xl text-primary-600']),
                            ]),
                    ]),
            ]);
    }

    public static function updatePrices(callable $set, callable $get)
    {
        $items = $get('items') ?? [];
        $totalGoods = 0;
        $totalInstallation = 0;
        $vatPercent = floatval(\App\Models\Setting::get('vat_percent', 7));

        foreach ($items as $key => $item) {
            $productId = $item['product_id'] ?? null;
            $mainColorId = $item['color_id'] ?? null;
            $glassId = $item['glass_id'] ?? null;
            $accessoryIds = $item['accessories'] ?? [];
            $itemDiscount = floatval($item['discount_amount'] ?? 0);
            
            $priceData = ProductColorPrice::where('product_id', $productId)
                ->where('main_color_id', $mainColorId)
                ->first();

            $basePrice = floatval($priceData?->price ?? 0);
            $installRate = floatval($priceData?->installation_price ?? 0);

            $width = ($item['width'] ?? 0) / 1000;
            $height = ($item['height'] ?? 0) / 1000;
            $area = max(1.0, $width * $height);
            $qty = intval($item['quantity'] ?? 1);

            $glassPricePerSqm = 0;
            if ($glassId) {
                $glassPricePerSqm = floatval(Glass::find($glassId)?->price_per_sqm ?? 0);
            }

            $accessoriesPrice = 0;
            if (!empty($accessoryIds)) {
                $accessoriesPrice = floatval(Accessory::whereIn('id', $accessoryIds)->sum('price'));
            }

            // Goods Total per item: (Base + Glass) * Area * Qty + Accessories - ItemDiscount
            $itemGoods = (($basePrice + $glassPricePerSqm) * $area * $qty) + ($accessoriesPrice * $qty) - $itemDiscount;
            if ($itemGoods < 0) $itemGoods = 0;

            $itemInstall = $installRate * $area * $qty;

            $set("items.{$key}.price", number_format($itemGoods, 2, '.', ''));
            
            $totalGoods += $itemGoods;
            $totalInstallation += $itemInstall;
        }

        $totalBeforeVat = $totalGoods + $totalInstallation;
        $vatAmount = $totalBeforeVat * ($vatPercent / 100);
        $grandTotal = $totalBeforeVat + $vatAmount;

        $set('total_goods', number_format($totalGoods, 2, '.', ''));
        $set('installation_total', number_format($totalInstallation, 2, '.', ''));
        $set('total_price', number_format($totalBeforeVat, 2, '.', ''));
        $set('vat_total', number_format($vatAmount, 2, '.', ''));
        $set('final_price', number_format($grandTotal, 2, '.', ''));
    }
}
