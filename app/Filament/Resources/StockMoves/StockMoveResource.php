<?php

namespace App\Filament\Resources\StockMoves;

use App\Filament\Resources\StockMoves\Pages\CreateStockMove;
use App\Filament\Resources\StockMoves\Pages\EditStockMove;
use App\Filament\Resources\StockMoves\Pages\ListStockMoves;
use App\Filament\Resources\StockMoves\Pages\ViewStockMove;
use App\Filament\Resources\StockMoves\Schemas\StockMoveForm;
use App\Filament\Resources\StockMoves\Schemas\StockMoveInfolist;
use App\Filament\Resources\StockMoves\Tables\StockMovesTable;
use App\Models\StockMove;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StockMoveResource extends Resource
{
    protected static ?string $model = StockMove::class;

    protected static \UnitEnum|string|null $navigationGroup = 'Inventory & Warehouse';
    protected static ?int $navigationSort = 3;
    protected static ?string $recordTitleAttribute = 'reference';

    public static function form(Schema $schema): Schema
    {
        return StockMoveForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StockMoveInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockMovesTable::configure($table);
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
            'index' => ListStockMoves::route('/'),
            'create' => CreateStockMove::route('/create'),
            'view' => ViewStockMove::route('/{record}'),
            'edit' => EditStockMove::route('/{record}/edit'),
        ];
    }
}
