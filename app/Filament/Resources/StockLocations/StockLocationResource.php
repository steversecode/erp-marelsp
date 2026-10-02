<?php

namespace App\Filament\Resources\StockLocations;

use App\Filament\Resources\StockLocations\Pages\CreateStockLocation;
use App\Filament\Resources\StockLocations\Pages\EditStockLocation;
use App\Filament\Resources\StockLocations\Pages\ListStockLocations;
use App\Filament\Resources\StockLocations\Pages\ViewStockLocation;
use App\Filament\Resources\StockLocations\Schemas\StockLocationForm;
use App\Filament\Resources\StockLocations\Schemas\StockLocationInfolist;
use App\Filament\Resources\StockLocations\Tables\StockLocationsTable;
use App\Models\StockLocation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StockLocationResource extends Resource
{
    protected static ?string $model = StockLocation::class;

    protected static \UnitEnum|string|null $navigationGroup = 'Inventory & Warehouse';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return StockLocationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StockLocationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockLocationsTable::configure($table);
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
            'index' => ListStockLocations::route('/'),
            'create' => CreateStockLocation::route('/create'),
            'view' => ViewStockLocation::route('/{record}'),
            'edit' => EditStockLocation::route('/{record}/edit'),
        ];
    }
}
