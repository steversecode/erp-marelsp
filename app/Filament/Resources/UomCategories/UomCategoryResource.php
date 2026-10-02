<?php

namespace App\Filament\Resources\UomCategories;

use App\Filament\Resources\UomCategories\Pages\CreateUomCategory;
use App\Filament\Resources\UomCategories\Pages\EditUomCategory;
use App\Filament\Resources\UomCategories\Pages\ListUomCategories;
use App\Filament\Resources\UomCategories\Pages\ViewUomCategory;
use App\Filament\Resources\UomCategories\Schemas\UomCategoryForm;
use App\Filament\Resources\UomCategories\Schemas\UomCategoryInfolist;
use App\Filament\Resources\UomCategories\Tables\UomCategoriesTable;
use App\Models\UomCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UomCategoryResource extends Resource
{
    protected static ?string $model = UomCategory::class;

    protected static \UnitEnum|string|null $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 4;
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return UomCategoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UomCategoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UomCategoriesTable::configure($table);
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
            'index' => ListUomCategories::route('/'),
            'create' => CreateUomCategory::route('/create'),
            'view' => ViewUomCategory::route('/{record}'),
            'edit' => EditUomCategory::route('/{record}/edit'),
        ];
    }
}
