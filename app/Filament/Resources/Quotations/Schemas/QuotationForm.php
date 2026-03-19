<?php

namespace App\Filament\Resources\Quotations\Schemas;

use App\Models\Brand;
use App\Models\Color;
use App\Models\Glass;
use App\Models\MaterialType;
use App\Models\Product;
use App\Models\Accessory;
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
                // SECTION 1: HEADER (Customer & Meta Data)
                Section::make('Quotation Information')
                    ->description('Customer details and project assignment.')
                    ->icon('heroicon-o-document-text')
                    ->collapsible()
                    ->maxWidth(\Filament\Support\Enums\Width::Full)
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(['default' => 1, 'sm' => 2, 'lg' => 4])
                            ->schema([
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
                            ->hiddenLabel()
                            ->content('Product Details')
                            ->extraAttributes(['class' => 'font-bold text-base tracking-wide text-gray-700 mb-1'])
                            ->columnSpanFull(),

                        Placeholder::make('product_details_helper')
                            ->content('1) Material → 2) Type → 3) Brand → 4) Product')
                            ->extraAttributes(['class' => 'text-xs text-gray-400 mb-3 border-b pb-2'])
                            ->columnSpanFull(),

                        Select::make('material_id')
                            ->label('Material')
                            ->relationship('material', 'name')
                            ->required()
                            ->live()
                            ->prefixIcon('heroicon-o-cube-transparent')
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                $set('material_type_id', null);
                                $set('brand_id', null);
                                $set('product_id', null);
                                self::updatePrices($set, $get);
                            }),

                        Select::make('material_type_id')
                            ->label('Type')
                            ->options(fn(callable $get) => MaterialType::where('material_id', $get('material_id'))->pluck('name', 'id'))
                            ->required()
                            ->live()
                            ->visible(fn(callable $get) => filled($get('material_id')))
                            ->afterStateUpdated(fn(callable $set, callable $get) => self::updatePrices($set, $get)),

                        Select::make('brand_id')
                            ->label('Brand')
                            ->options(fn(callable $get) => Brand::where('material_id', $get('material_id'))->pluck('name', 'id'))
                            ->required()
                            ->live()
                            ->visible(fn(callable $get) => filled($get('material_type_id')))
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                $set('product_id', null);
                                self::updatePrices($set, $get);
                            }),

                        Select::make('product_id')
                            ->label('Product')
                            ->options(fn(callable $get) => Product::whereHas('brandRates', fn($q) => $q->where('brand_id', $get('brand_id')))->pluck('name', 'id'))
                            ->required()
                            ->live()
                            ->searchable()
                            ->preload()
                            ->visible(fn(callable $get) => filled($get('brand_id')))
                            ->afterStateUpdated(fn(callable $set, callable $get) => self::updatePrices($set, $get)),

                        Placeholder::make('product_image_preview')
                            ->label('Product Image')
                            ->visible(fn(callable $get) => filled($get('product_id')))
                            ->columnSpanFull()
                            ->content(function (callable $get) {
                                $productId = $get('product_id');
                                if (!$productId)
                                    return '';

                                $product = Product::find($productId);
                                if (!$product || !$product->drawing_path) {
                                    return new \Illuminate\Support\HtmlString(
                                        '<div class="flex items-center justify-center h-32 rounded-lg bg-gray-100 text-gray-400 text-sm font-medium">
                                            <span>No image available for this product</span>
                                        </div>'
                                    );
                                }

                                $url = asset('storage/' . $product->drawing_path);
                                return new \Illuminate\Support\HtmlString(
                                    '<div class="flex flex-col items-center gap-2 p-3 bg-gray-50 rounded-xl border border-gray-200 shadow-sm">
                                        <img src="' . e($url) . '"
                                             alt="' . e($product->name) . '"
                                             class="max-h-52 max-w-full rounded-lg object-contain shadow"
                                             style="max-height:210px; border:1px solid #e5e7eb;"
                                        />
                                        <span class="text-xs text-gray-500 font-medium">' . e($product->name) . '</span>
                                    </div>'
                                );
                            }),

                        // Dimensions & Classification
                        Placeholder::make('specifications_label')
                            ->label('Specifications')
                            ->hiddenLabel()
                            ->content('Specifications')
                            ->extraAttributes(['class' => 'font-bold text-lg text-primary-600 mb-2 mt-4 border-b pb-2'])
                            ->columnSpanFull(),

                        TextInput::make('width')
                            ->label('Width (mm)')
                            ->numeric()
                            ->default(1000)
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn(callable $set, callable $get) => self::updatePrices($set, $get)),

                        TextInput::make('height')
                            ->label('Height (mm)')
                            ->numeric()
                            ->default(1000)
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn(callable $set, callable $get) => self::updatePrices($set, $get)),

                        TextInput::make('quantity')
                            ->label('Quantity')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->required()
                            ->live()
                            ->prefixIcon('heroicon-o-hashtag')
                            ->afterStateUpdated(fn(callable $set, callable $get) => self::updatePrices($set, $get)),

                        TextInput::make('classification')
                            ->label('Class (Auto)')
                            ->default('NORMAL')
                            ->required()
                            ->live()
                            ->disabled()      // Auto-set by color — no manual edit
                            ->dehydrated()    // Save value even when disabled
                            ->afterStateUpdated(fn(callable $set, callable $get) => self::updatePrices($set, $get)),

                        TextInput::make('area')
                            ->label('Area (Sqm)')
                            ->numeric()
                            ->readOnly()
                            ->dehydrated(false) // DB me column nahi, sirf UI display ke liye
                            ->prefixIcon('heroicon-o-square-2-stack')
                            ->extraAttributes(['class' => 'bg-blue-50']),

                        // Finishes (Color, Glass, Film)
                        Placeholder::make('finishes_label')
                            ->label('Finishes & Options')
                            ->hiddenLabel()
                            ->content('Finishes & Options')
                            ->extraAttributes(['class' => 'font-bold text-lg text-primary-600 mb-2 mt-4 border-b pb-2'])
                            ->columnSpanFull(),

                        Select::make('category_id')
                            ->label('Color Category')
                            ->options(function (callable $get) {
                                $brandId = $get('brand_id');
                                // Brand / Product ke hisab se category filter karo
                                if ($brandId) {
                                    return \App\Models\Category::where('brand_id', $brandId)->pluck('name', 'id');
                                }

                                return \App\Models\Category::pluck('name', 'id');
                            })
                            ->required()
                            ->live()
                            ->dehydrated(false)
                            ->afterStateHydrated(function (callable $set, callable $get, $state) {
                                if (filled($state))
                                    return;

                                $colorId = $get('color_id');
                                if (filled($colorId)) {
                                    $color = \App\Models\Color::find($colorId);
                                    if ($color) {
                                        $set('category_id', $color->category_id);
                                    }
                                }
                            })
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                $set('color_id', null);
                                self::updatePrices($set, $get);
                            }),

                        Select::make('color_id')
                            ->label('Color Shade')
                            ->options(fn(callable $get) => \App\Models\Color::where('category_id', $get('category_id'))->pluck('name', 'id'))
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                $colorId = $get('color_id');
                                if ($colorId) {
                                    $color = \App\Models\Color::find($colorId);
                                    if ($color) {
                                        // Auto-set classification based on color type
                                        $set('classification', $color->color_type);
                                    }
                                }
                                self::updatePrices($set, $get);
                            }),

                        Select::make('glass_id')
                            ->label('Glass Type')
                            ->options(Glass::pluck('name', 'id'))
                            ->required()
                            ->live()
                            ->searchable()
                            ->afterStateUpdated(fn(callable $set, callable $get) => self::updatePrices($set, $get)),

                        // Extras & Price
                        Placeholder::make('pricing_label')
                            ->label('Extras & Pricing')
                            ->hiddenLabel()
                            ->content('Extras & Pricing')
                            ->extraAttributes(['class' => 'font-bold text-lg text-primary-600 mb-2 mt-4 border-b pb-2'])
                            ->columnSpanFull(),

                        CheckboxList::make('accessories')
                            ->label('Accessories')
                            ->options(Accessory::pluck('name', 'id'))
                            ->live()
                            ->columns(2)
                            ->afterStateUpdated(fn(callable $set, callable $get) => self::updatePrices($set, $get))
                            ->columnSpan(3),

                        TextInput::make('installation_cost')
                            ->label('Installation Cost (per Sqm)')
                            ->numeric()
                            ->prefix('฿')
                            ->default(0)
                            ->live()
                            ->afterStateUpdated(fn(callable $set, callable $get) => self::updatePrices($set, $get)),

                        TextInput::make('discount')
                            ->label('Discount (Fixed or %)')
                            ->placeholder('e.g. 500 or 10%')
                            ->live()
                            ->prefix('฿')
                            ->afterStateUpdated(fn(callable $set, callable $get) => self::updatePrices($set, $get)),

                        TextInput::make('price')
                            ->label('Item Total')
                            ->numeric()
                            ->readOnly()
                            ->dehydrated()
                            ->prefix('฿')
                            ->extraAttributes(['class' => 'font-bold bg-green-50 text-green-700 text-lg'])
                            ->columnSpan(1),
                    ])
                    ->columns(4)
                    ->columnSpanFull(),

                // SECTION 3: FOOTER (Financials)
                Section::make('Total Financials')
                    ->maxWidth(\Filament\Support\Enums\Width::Full)
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('total_goods')
                                    ->label('Goods Total')
                                    ->readOnly()
                                    ->dehydrated(false)
                                    ->prefix('฿')
                                    ->extraAttributes(['class' => 'text-right text-lg']),

                                TextInput::make('installation_total')
                                    ->label('Installation Total')
                                    ->readOnly()
                                    ->dehydrated(false)
                                    ->prefix('฿')
                                    ->extraAttributes(['class' => 'text-right text-lg text-primary-600']),

                                TextInput::make('total_price')
                                    ->label('Subtotal (Before VAT)')
                                    ->readOnly()
                                    ->dehydrated()
                                    ->prefix('฿')
                                    ->extraAttributes(['class' => 'text-right bg-blue-50']),

                                TextInput::make('discount')
                                    ->label('Total Discount')
                                    ->readOnly()
                                    ->dehydrated()
                                    ->prefix('฿')
                                    ->extraAttributes(['class' => 'text-right']),

                                TextInput::make('vat_percent')
                                    ->label('VAT (%)')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->live()
                                    ->suffix('%')
                                    ->placeholder('0')
                                    ->helperText('VAT applied after discount')
                                    ->afterStateUpdated(fn(callable $set, callable $get) => self::updatePrices($set, $get)),

                                TextInput::make('vat_amount')
                                    ->label('VAT Amount')
                                    ->numeric()
                                    ->readOnly()
                                    ->dehydrated()
                                    ->prefix('฿')
                                    ->extraAttributes(['class' => 'text-right']),

                                TextInput::make('final_price')
                                    ->label('Grand Total')
                                    ->readOnly()
                                    ->dehydrated()
                                    ->prefix('฿')
                                    ->extraAttributes(['class' => 'font-bold text-3xl text-primary-600 bg-gray-50 p-4 rounded-lg text-right']),
                            ]),
                    ])->columns(1),
            ]);
    }

    public static function updatePrices(callable $set, callable $get)
    {
        // Try to find the items at different relative paths
        $items = $get('items') ?? $get('../items') ?? $get('../../items') ?? $get('../../../items');
        
        $rootPrefix = '';
        if ($get('items') !== null) {
            $rootPrefix = '';
        } elseif ($get('../items') !== null) {
            $rootPrefix = '../';
        } elseif ($get('../../items') !== null) {
            $rootPrefix = '../../';
        } elseif ($get('../../../items') !== null) {
            $rootPrefix = '../../../';
        }

        if (!is_array($items)) {
            $items = [];
        }

        $totalGoodsAmount = 0;
        $totalInstallationAmount = 0;
        $totalDiscountAmountSum = 0;
        $service = new PricingService();

        // Formula Implementation per Item
        foreach ($items as $key => $item) {
            $calculation = $service->calculateItemPrice($item);
            
            // 1. Area = (Width ÷ 1000) × (Height ÷ 1000)
            $area = floatval(str_replace(',', '', $calculation['details']['applied_area_sqm'] ?? 0));
            $qty = intval($item['quantity'] ?? 1);
            
            // 2. Base Amount = Rate × Area × Qty (Product/Goods Rate)
            $goodsRate = floatval($calculation['goods_rate_per_sqm'] ?? 0);
            $baseAmount = $goodsRate * $area * $qty;

            // 3. Discount Total calculation
            $discountInput = trim($item['discount'] ?? '0');
            $discountTotalLine = 0;

            if (is_string($discountInput) && str_contains($discountInput, '%')) {
                $percentage = floatval(str_replace('%', '', $discountInput));
                // Percentage applied to base amount (Goods Rate * Area * Quantity)
                $discountTotalLine = ($baseAmount * $percentage) / 100;
            } else {
                $discountRate = floatval($discountInput);
                $discountTotalLine = ($discountRate * $area) * $qty;
            }

            // 4. Item Total (Goods Only) = Base Amount − Discount Total
            // Note: Installation is excluded from "Item Total" as per request.
            $itemTotalGoods = max(0, $baseAmount - $discountTotalLine);

            // 5. Installation Total = Installation (per Sqm) × Area × Qty
            $installRate = floatval($calculation['installation_rate_per_sqm'] ?? 0);
            $installationTotalLine = $installRate * $area * $qty;

            // 6. Final stored price for this item (excluding installation as requested)
            $set("{$rootPrefix}items.{$key}.price", number_format($itemTotalGoods, 2, '.', ''));
            $set("{$rootPrefix}items.{$key}.area", $calculation['details']['applied_area_sqm']);
            
            // Set default installation cost ONLY if not already set by user manually
            if (!isset($item['installation_cost']) || $item['installation_cost'] === '') {
                $set("{$rootPrefix}items.{$key}.installation_cost", $calculation['calculated_installation']);
            }

            $totalGoodsAmount += $itemTotalGoods;
            $totalInstallationAmount += $installationTotalLine;
            $totalDiscountAmountSum += $discountTotalLine;
        }

        // Global Totals (7 & 8)
        $vatPercent = floatval($get("{$rootPrefix}vat_percent") ?? 0);
        
        // 7. VAT Amount = (Goods + Installation) × (VAT ÷ 100)
        $totalTaxableAmount = $totalGoodsAmount + $totalInstallationAmount;
        $vatAmount = $totalTaxableAmount * ($vatPercent / 100);

        // 8. Grand Total = Goods + Installation + VAT Amount
        $grandTotal = $totalTaxableAmount + $vatAmount;

        // Set totals at the root level
        $set("{$rootPrefix}total_goods", number_format($totalGoodsAmount, 2, '.', ''));
        $set("{$rootPrefix}installation_total", number_format($totalInstallationAmount, 2, '.', ''));
        $set("{$rootPrefix}total_price", number_format($totalTaxableAmount, 2, '.', ''));
        $set("{$rootPrefix}discount", number_format($totalDiscountAmountSum, 2, '.', ''));
        $set("{$rootPrefix}vat_amount", number_format($vatAmount, 2, '.', ''));
        $set("{$rootPrefix}final_price", number_format($grandTotal, 2, '.', ''));
    }
}
