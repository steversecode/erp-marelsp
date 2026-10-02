<?php

namespace App\Filament\Resources\ManufacturingOrders\Pages;

use App\Filament\Resources\ManufacturingOrders\ManufacturingOrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateManufacturingOrder extends CreateRecord
{
    protected static string $resource = ManufacturingOrderResource::class;
}
