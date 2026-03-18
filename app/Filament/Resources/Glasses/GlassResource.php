<?php

namespace App\Filament\Resources\Glasses;

use App\Filament\Resources\Glasses\Pages\CreateGlass;
use App\Filament\Resources\Glasses\Pages\EditGlass;
use App\Filament\Resources\Glasses\Pages\ListGlasses;
use App\Filament\Resources\Glasses\Schemas\GlassForm;
use App\Filament\Resources\Glasses\Tables\GlassesTable;
use App\Models\Glass;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GlassResource extends Resource
{
    protected static ?string $model = Glass::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-beaker';

    protected static ?int $navigationSort = 8;

    public static function getNavigationGroup(): ?string
    {
        return 'Materials';
    }

    protected static ?string $recordTitleAttribute = 'name';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->role === 'admin';
    }

    public static function form(Schema $schema): Schema
    {
        return GlassForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GlassesTable::configure($table);
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
            'index' => ListGlasses::route('/'),
            'create' => CreateGlass::route('/create'),
            'edit' => EditGlass::route('/{record}/edit'),
        ];
    }
}
