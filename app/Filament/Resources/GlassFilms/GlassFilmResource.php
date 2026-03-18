<?php

namespace App\Filament\Resources\GlassFilms;

use App\Filament\Resources\GlassFilms\Pages\CreateGlassFilm;
use App\Filament\Resources\GlassFilms\Pages\EditGlassFilm;
use App\Filament\Resources\GlassFilms\Pages\ListGlassFilms;
use App\Filament\Resources\GlassFilms\Schemas\GlassFilmForm;
use App\Filament\Resources\GlassFilms\Tables\GlassFilmsTable;
use App\Models\GlassFilm;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GlassFilmResource extends Resource
{
    protected static ?string $model = GlassFilm::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-film';

    protected static ?int $navigationSort = 9;

    public static function getNavigationGroup(): ?string
    {
        return 'Materials';
    }

    public static function shouldRegisterNavigation(): bool
    {
        // Hide Glass Films from admin sidebar navigation
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return GlassFilmForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GlassFilmsTable::configure($table);
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
            'index' => ListGlassFilms::route('/'),
            'create' => CreateGlassFilm::route('/create'),
            'edit' => EditGlassFilm::route('/{record}/edit'),
        ];
    }
}
