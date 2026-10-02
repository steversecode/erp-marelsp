<?php

namespace App\Filament\Resources\UomCategories\Pages;

use App\Filament\Resources\UomCategories\UomCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUomCategories extends ListRecords
{
    protected static string $resource = UomCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
