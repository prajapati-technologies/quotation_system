<?php

namespace App\Filament\Resources\Colors\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class ColorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('materialType.name')
                    ->label('Material Type')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('parent.name')
                    ->label('Main Color')
                    ->placeholder('None (Is Main Color)')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Color Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('code')
                    ->label('Code')
                    ->searchable(),

                TextColumn::make('additional_price')
                    ->money('THB')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('material_type_id')
                    ->label('Material Type')
                    ->relationship('materialType', 'name'),

                SelectFilter::make('parent_id')
                    ->label('Main Color')
                    ->relationship('parent', 'name'),
            ])
            ->recordActions([
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
