<?php

namespace App\Filament\Resources\Brands\RelationManagers;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\CreateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BrandRatesRelationManager extends RelationManager
{
    protected static string $relationship = 'brandRates';

    protected static ?string $recordTitleAttribute = 'title'; // Changed from default to something valid or remove if not needed? Command asked for 'title'.

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('normal_price')
                    ->label('Normal color name')
                    ->required(),
                TextInput::make('special_price')
                    ->label('Sahara color')
                    ->required(),
                TextInput::make('installation_price')
                    ->required()
                    ->numeric()
                    ->prefix('฿'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('normal_price')
                    ->label('Normal color name')
                    ->sortable(),
                TextColumn::make('special_price')
                    ->label('Sahara color')
                    ->sortable(),
                TextColumn::make('installation_price')
                    ->money()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
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
