<?php

namespace App\Filament\Resources\ManufacturingOrders\Pages;

use App\Filament\Resources\ManufacturingOrders\ManufacturingOrderResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewManufacturingOrder extends ViewRecord
{
    protected static string $resource = ManufacturingOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
