<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('materialType.name')
                    ->label('Material Type')
                    ->sortable()
                    ->placeholder('N/A'),

                TextColumn::make('colorPrices_count')
                    ->label('Color Configs')
                    ->counts('colorPrices')
                    ->sortable(),

                ImageColumn::make('drawing_path')
                    ->label('Drawing')
                    ->disk('public')
                    ->height(50)
                    ->width(50)
                    ->extraImgAttributes(['class' => 'rounded object-contain cursor-pointer'])
                    ->url(fn($record) => $record->drawing_path ? asset('storage/' . $record->drawing_path) : null)
                    ->openUrlInNewTab(),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
