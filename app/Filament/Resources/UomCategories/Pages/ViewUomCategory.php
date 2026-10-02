<?php

namespace App\Filament\Resources\UomCategories\Pages;

use App\Filament\Resources\UomCategories\UomCategoryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewUomCategory extends ViewRecord
{
    protected static string $resource = UomCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
