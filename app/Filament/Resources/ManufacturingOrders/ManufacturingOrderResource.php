<?php

namespace App\Filament\Resources\ManufacturingOrders;

use App\Filament\Resources\ManufacturingOrders\Pages\CreateManufacturingOrder;
use App\Filament\Resources\ManufacturingOrders\Pages\EditManufacturingOrder;
use App\Filament\Resources\ManufacturingOrders\Pages\ListManufacturingOrders;
use App\Filament\Resources\ManufacturingOrders\Pages\ViewManufacturingOrder;
use App\Filament\Resources\ManufacturingOrders\Schemas\ManufacturingOrderForm;
use App\Filament\Resources\ManufacturingOrders\Schemas\ManufacturingOrderInfolist;
use App\Filament\Resources\ManufacturingOrders\Tables\ManufacturingOrdersTable;
use App\Models\ManufacturingOrder;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ManufacturingOrderResource extends Resource
{
    protected static ?string $model = ManufacturingOrder::class;

    protected static \UnitEnum|string|null $navigationGroup = 'Manufacturing (MRP)';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'mo_number';

    public static function form(Schema $schema): Schema
    {
        return ManufacturingOrderForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ManufacturingOrderInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ManufacturingOrdersTable::configure($table);
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
            'index' => ListManufacturingOrders::route('/'),
            'create' => CreateManufacturingOrder::route('/create'),
            'view' => ViewManufacturingOrder::route('/{record}'),
            'edit' => EditManufacturingOrder::route('/{record}/edit'),
        ];
    }
}
