<?php

namespace App\Filament\Resources\Quotations\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\Width;
use Illuminate\Support\HtmlString;

class QuotationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // SECTION 1: HEADER
                Section::make('Quotation Information')
                    ->icon('heroicon-o-document-text')
                    ->maxWidth(Width::Full)
                    ->columnSpanFull()
                    ->columns(['default' => 1, 'sm' => 2, 'lg' => 4])
                    ->schema([
                        Placeholder::make('customer')
                            ->label('Customer')
                            ->content(fn($record) => $record->customer->name ?? 'N/A'),

                        Placeholder::make('project')
                            ->label('Project')
                            ->content(fn($record) => $record->project->name ?? 'N/A'),

                        Placeholder::make('date')
                            ->label('Quotation Date')
                            ->content(fn($record) => $record->quotation_date ? \Illuminate\Support\Carbon::parse($record->quotation_date)->format('M d, Y') : 'N/A'),

                        Placeholder::make('status')
                            ->label('Status')
                            ->content(fn($record) => new HtmlString("<span class='fi-badge flex items-center justify-center gap-x-1 rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset bg-gray-50 text-gray-600 ring-gray-600/10'>" . ($record->status ?? 'Draft') . "</span>")),
                    ]),

                // SECTION 2: ITEMS
                Section::make('Line Items')
                    ->icon('heroicon-o-list-bullet')
                    ->maxWidth(Width::Full)
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->hiddenLabel()
                            ->disabled()
                            ->dehydrated(false)
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->columns(3)
                            ->schema([
                                TextInput::make('material_name')
                                    ->label('Material')
                                    ->formatStateUsing(fn($record) => $record->material->name ?? 'N/A')
                                    ->disabled(),
                                TextInput::make('material_type_name')
                                    ->label('Type')
                                    ->formatStateUsing(fn($record) => $record->materialType->name ?? 'N/A')
                                    ->disabled(),
                                TextInput::make('brand_name')
                                    ->label('Brand')
                                    ->formatStateUsing(fn($record) => $record->brand->name ?? 'N/A')
                                    ->disabled(),

                                TextInput::make('product_name')
                                    ->label('Product')
                                    ->formatStateUsing(fn($record) => $record->product->name ?? 'N/A')
                                    ->columnSpanFull()
                                    ->disabled(),

                                Placeholder::make('product_image')
                                    ->label('Product Image')
                                    ->columnSpanFull()
                                    ->content(function ($record) {
                                        $product = $record->product ?? null;
                                        if (!$product || !$product->drawing_path) {
                                            return new HtmlString(
                                                '<div class="flex items-center justify-center h-28 rounded-lg bg-gray-100 text-gray-400 text-sm font-medium">
                                                    <span>No image available for this product</span>
                                                </div>'
                                            );
                                        }
                                        $url = asset('storage/' . $product->drawing_path);
                                        return new HtmlString(
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

                                TextInput::make('width')
                                    ->label('Width (mm)')
                                    ->disabled(),
                                TextInput::make('height')
                                    ->label('Height (mm)')
                                    ->disabled(),
                                TextInput::make('quantity')
                                    ->label('Quantity')
                                    ->disabled(),

                                TextInput::make('classification')
                                    ->label('Class')
                                    ->disabled(),
                                TextInput::make('color_name')
                                    ->label('Color')
                                    ->formatStateUsing(fn($record) => $record->color->name ?? 'N/A')
                                    ->disabled(),
                                TextInput::make('glass_name')
                                    ->label('Glass')
                                    ->formatStateUsing(fn($record) => $record->glass->name ?? 'N/A')
                                    ->disabled(),

                                 TextInput::make('discount')
                                    ->label('Discount (Fixed or %)')
                                    ->prefix('฿')
                                    ->disabled(),

                                TextInput::make('price')
                                    ->label('Item Total (Net)')
                                    ->prefix('฿')
                                    ->columnSpanFull()
                                    ->extraAttributes(['class' => 'font-bold text-green-600'])
                                    ->disabled(),
                            ])
                    ]),

                // SECTION 3: FINANCIALS
                Section::make('Total Financials')
                    ->icon('heroicon-o-currency-dollar')
                    ->maxWidth(Width::Full)
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Placeholder::make('total_goods')
                            ->label('Goods Total')
                            ->content(fn($record) => '฿' . number_format($record->total_goods ?? 0, 2)),

                        Placeholder::make('discount')
                            ->label('Total Discount')
                            ->content(fn($record) => '฿' . number_format($record->discount ?? 0, 2)),

                        Placeholder::make('installation_total')
                            ->label('Installation Total')
                            ->content(fn($record) => '฿' . number_format($record->installation_total ?? 0, 2)),

                        Placeholder::make('total_price')
                            ->label('Subtotal (Before VAT)')
                            ->content(fn($record) => '฿' . number_format($record->total_price, 2)),

                        Placeholder::make('vat_amount')
                            ->label(fn($record) => "VAT (" . ($record->vat_percent ?? 0) . "%)")
                            ->content(fn($record) => '฿' . number_format($record->vat_amount ?? 0, 2)),

                         Placeholder::make('final_price')
                            ->label('Grand Total')
                            ->columnSpanFull()
                            ->content(fn($record) => new HtmlString("<div class='text-4xl font-extrabold text-primary-600 uppercase tracking-tight text-right'>฿" . number_format($record->final_price, 2) . "</div>")),
                    ]),
            ]);
    }
}
