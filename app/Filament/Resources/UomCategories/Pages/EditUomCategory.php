<?php

namespace App\Filament\Resources\UomCategories\Pages;

use App\Filament\Resources\UomCategories\UomCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditUomCategory extends EditRecord
{
    protected static string $resource = UomCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
