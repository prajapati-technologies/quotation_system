<?php

namespace App\Filament\Resources\MaterialTypes;

use App\Filament\Resources\MaterialTypes\Pages\CreateMaterialType;
use App\Filament\Resources\MaterialTypes\Pages\EditMaterialType;
use App\Filament\Resources\MaterialTypes\Pages\ListMaterialTypes;
use App\Filament\Resources\MaterialTypes\Schemas\MaterialTypeForm;
use App\Filament\Resources\MaterialTypes\Tables\MaterialTypesTable;
use App\Models\MaterialType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MaterialTypeResource extends Resource
{
    protected static ?string $model = MaterialType::class;

    protected static ?string $navigationLabel = 'Material Types';

    public static function getNavigationGroup(): ?string
    {
        return 'Master Data';
    }

    protected static ?int $navigationSort = 20;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->role === 'admin';
    }



    public static function form(Schema $schema): Schema
    {
        return MaterialTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MaterialTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMaterialTypes::route('/'),
            'create' => CreateMaterialType::route('/create'),
            'edit' => EditMaterialType::route('/{record}/edit'),
        ];
    }
}
