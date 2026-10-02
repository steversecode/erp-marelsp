<?php

namespace App\Filament\Resources\Boms;

use App\Filament\Resources\Boms\Pages\CreateBom;
use App\Filament\Resources\Boms\Pages\EditBom;
use App\Filament\Resources\Boms\Pages\ListBoms;
use App\Filament\Resources\Boms\Pages\ViewBom;
use App\Filament\Resources\Boms\Schemas\BomForm;
use App\Filament\Resources\Boms\Schemas\BomInfolist;
use App\Filament\Resources\Boms\Tables\BomsTable;
use App\Models\Bom;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BomResource extends Resource
{
    protected static ?string $model = Bom::class;

    protected static \UnitEnum|string|null $navigationGroup = 'Manufacturing (MRP)';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'code';

    public static function form(Schema $schema): Schema
    {
        return BomForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BomInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BomsTable::configure($table);
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
            'index' => ListBoms::route('/'),
            'create' => CreateBom::route('/create'),
            'view' => ViewBom::route('/{record}'),
            'edit' => EditBom::route('/{record}/edit'),
        ];
    }
}
